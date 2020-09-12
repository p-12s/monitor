<?php
namespace console\controllers;

use common\helpers\Helper;
use common\models\NotAvailableSite;
use common\models\Notification;
use common\models\SiteInGroup;
use Yii;
use yii\web\Controller;
use yii\data\Pagination;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\web\BadRequestHttpException;
use yii\base\InvalidArgumentException;
use yii\helpers\Url;
use common\models\User;
use DateTime;
use common\helpers\DateUtil;
use common\helpers\Sender;
use common\models\Site;
use common\models\History;
use common\models\UserInGroup;
use Exception;

// время Московское, чтобы начислять по-Москве
date_default_timezone_set('Europe/Moscow');

/**
 *
 * */
class SiteUnavailabilityController extends Controller
{
    const GREENSMS_API = 'http://api3.greensms.ru/sms/send';
    const GREENSMS_USER = 'USERNAME';
    const GREENSMS_PASS = 'PASS';
    const GREENSMS_FROM = 'GREENSMS';

    private $now;
    private $delayedFallReaction;

    public function beforeAction($action)
    {
        $this->now = DateUtil::now();
        $minutesAgo = -1 * 30;
        $this->delayedFallReaction = DateUtil::cloneDateWithShift($this->now, $minutesAgo)->format('Y-m-d H:i:s');
        $this->enableCsrfValidation = false;
        return parent::beforeAction($action);
    }

    /** Работа с результатами проверки сайтов:
     * 1) проверка, упал ли какой-нибудь сайт
     * 2) поднялся ли упавший сайт
     * */
    public function actionIndex()
    {
        // достаем историю за 5 минут по всем вкл сайтам
        $enabledSites = Site::find()->where(['status' => Site::STATUS_ENABLED])->orderBy(['id' => SORT_ASC])->all();
        $unavailableSites = NotAvailableSite::find()->asArray()->all();
        // обрабатываем каждый сайт отдельно, причина:
        // интервалы проверки могут быть отличаться, а значит отличаеется время, на которое нужно смотреть назад
        foreach ($enabledSites as $site) {
            try {
                echo "\n$site->id";
                if ($site->created_at > $this->delayedFallReaction) {
                    echo "\n сайт создан менее часа назад, поэтому не считаем его упавшим из-за отсутствия истории";
                    continue;
                }

                $isSiteMarkedAsUnavailable = self::isItemFoundById($unavailableSites, $site->id, 'site_id');

                // берем интервал мониторинга сайта, кол-во обязательных фейлов подряд из конфига
                // (например, после 5-ти фейлов отправляем смс о падении, или после 2-х успешных проверок смс о поднятии)
                $howManyMinutesAgo = 0;
                if ($isSiteMarkedAsUnavailable) {
                    echo "\n сайт числится в недоступных";
                    // ищем, чтобы 2 ответа подряд были 2хх
                    $howManyMinutesAgo = $site->interval * Yii::$app->params['numbersOfOks'];
                    $clonedDate = DateUtil::cloneDateWithShift($this->now, (-1 * $howManyMinutesAgo));
                    $responseCodes = Helper::convertNestedArr(
                        History::find()->select('code')->where(['in', 'site_id', $site->id])->andWhere(['>=', 'created_at', $clonedDate->format('Y-m-d H:i:s') ])->asArray()->all(),
                        'code');

                    $isSiteWork = Helper::isAllCodesDesired($responseCodes, Site::RESPONSE_CODE_OK); // сайт поднялся
                    $isSiteMarkedAsUnavailable = NotAvailableSite::find()->where(['site_id' => $site->id])->exists();

                    if ($isSiteWork && $isSiteMarkedAsUnavailable) {
                        try {
                            $notificationGroupIds = self::getNotificationGroupIds($site->id);
                            $recipientPhones = self::getRecipientPhones($notificationGroupIds);

                            self::sendSiteNotification($recipientPhones, $site->url, $howManyMinutesAgo, Notification::TYPE_SITE_OK);
                            self::saveSentNotification($site->id, $notificationGroupIds, Notification::TYPE_SITE_OK);
                            self::deleteNotAvailableSite($site->id);
                        } catch (Exception $e) {
                            echo "\n ERROR 1:<pre>";
                            print_r($e);
                            echo "\n</pre>";
                        }
                    } else {
                        echo "\n либо сайт не не поднялся, либо уже отправлено сообщение. id: ". $site->id;
                    }

                } else {
                    echo "\n сайта нет в таблице недоступных";
                    // ищем, чтобы несколько ответов подряд были не 2хх
                    $howManyMinutesAgo = $site->interval * Yii::$app->params['numbersOfFails'];
                    // и узнаем на какое кол-во времени назад смотрим по-истории
                    $clonedDate = (new \common\helpers\DateUtil)->cloneDateWithShift($this->now, (-1 * $howManyMinutesAgo));
                    $responseCodes = Helper::convertNestedArr(
                        History::find()->select('code')->where(['in', 'site_id', $site->id])->andWhere(['>=', 'created_at', $clonedDate->format('Y-m-d H:i:s') ])->asArray()->all(),
                        'code');

                    $isSiteCrashed = Helper::isAllCodesDesired($responseCodes, Site::RESPONSE_CODE_ERROR); // сайт упал
                    $isSiteMarkedAsUnavailable = NotAvailableSite::find()->where(['site_id' => $site->id])->exists();

                    // если пришедшие коды ответа все ошибочны, и сайт не помечен как недоступный
                    if ($isSiteCrashed && !$isSiteMarkedAsUnavailable) {
                        try {
                            $notificationGroupIds = self::getNotificationGroupIds($site->id);
                            $recipientPhones = self::getRecipientPhones($notificationGroupIds);
                            self::sendSiteNotification($recipientPhones, $site->url, $howManyMinutesAgo, Notification::TYPE_SITE_UNAVAILABLE);
                            self::saveSentNotification($site->id, $notificationGroupIds, Notification::TYPE_SITE_UNAVAILABLE);
                            self::saveNotAvailableSite($site->id);
                        } catch (Exception $e) {
                            echo "\n ERROR 2:<pre>";
                            print_r($e);
                            echo "\n</pre>";
                        }
                    } else {
                        echo "\n сайт работает, либо он уже упавший, и уже отправлено сообщение. id: ". $site->id;
                    }
                }
            } catch (Exception $e) {
                echo "\nERROR!!!";
            }
        }
        echo "\n";
    }

    private function isItemFoundById($items, $desiredId, $memberName)
    {
        $isFound = false;
        foreach ($items as $item) {
            if ($item[$memberName] === $desiredId) {
                $isFound = true;
                break;
            }
        }
        return $isFound;
    }

    private function getNotificationGroupIds($siteId)
    {
        return Helper::convertNestedArr(
            SiteInGroup::find()->select('group_id')->where(['site_id' => $siteId])->asArray()->all(),
            'group_id',
            true);
    }

    private function getRecipientPhones($notificationGroupIds)
    {
        $userIds = Helper::convertNestedArr(
            UserInGroup::find()->select('user_id')->where(['in', 'group_id', $notificationGroupIds])->asArray()->all(),
            'user_id',
            true);

        return Helper::convertNestedArr(
            User::find()->select('phone')->where(['status' => User::STATUS_ACTIVE])->andWhere(['in', 'id', $userIds])->asArray()->all(),
            'phone');
    }

    private function saveNotAvailableSite($siteId)
    {
        // доп. проверка на существование, можно убрать при нагрузках
        $notAvailableSite = NotAvailableSite::find()->where(['site_id' => $siteId])->one();
        if (!$notAvailableSite) {
            $notAvailableSite = new NotAvailableSite();
            $notAvailableSite->site_id = $siteId;
            $notAvailableSite->save();
        }
    }

    private function deleteNotAvailableSite($siteId)
    {
        NotAvailableSite::deleteAll(['site_id' => $siteId]);
    }

    private function saveSentNotification($siteId, $groupIds, $type)
    {
        foreach ($groupIds as $groupId) {
            $notification = new Notification();
            $notification->site_id = $siteId;
            $notification->group_id = $groupId;
            $notification->type = $type;
            $notification->save();
        }
    }

    private function sendSiteNotification($recipientPhones, $url, $howManyMinutesAgo, $notificationType)
    {

        $message = self::concatMessage($url, $howManyMinutesAgo, $notificationType);

        foreach ($recipientPhones as $phone) {
            self::sendSms($phone, $message);
        }
    }

    private function concatMessage($url, $howManyMinutesAgo, $notificationType)
    {
        switch ( $notificationType ) {
            case Notification::TYPE_SITE_UNAVAILABLE:
                return "Err: $url недоступен ". $howManyMinutesAgo ." мин.";
            case Notification::TYPE_SITE_OK:
                return "Ok: $url поднялся";
            default:
                throw new Exception('Неизвестный тип уведомления: '. $notificationType);
        }
    }

    // использование отдбельного класса Sender не работало (возможно я криво назначил файл исполняемым), поэтому функция отправки здесь
    private function sendSms($phone, $message)
    {
        echo "\nОТПРАВКА СМС ИЗ КОНТРОЛЛЕРА на номер $phone `$message`";

        $array = array(
            'user'   => self::GREENSMS_USER,
            'pass' => self::GREENSMS_PASS,
            'to' => $phone,
            'txt' => $message,
            'from' => self::GREENSMS_FROM
        );

        $ch = curl_init(self::GREENSMS_API);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $array);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($array, '', '&'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_exec($ch); // возвращает результат {"request_id":"072e125f-1335-4872-97e0-009efbf50de3"}
        curl_close($ch);
    }
}
