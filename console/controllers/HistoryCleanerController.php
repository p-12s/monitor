<?php
namespace console\controllers;

use Yii;
use yii\web\Controller;
use yii\data\Pagination;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\web\BadRequestHttpException;
use yii\base\InvalidArgumentException;
use yii\helpers\Url;
use DateTime;
use common\helpers\DateUtil;
use common\models\History;
use Exception;

// время Московское, чтобы начислять по-Москве
date_default_timezone_set('Europe/Moscow');

/**
 *
 * */
class HistoryCleanerController extends Controller
{
    const MINUTES_IN_DAY = 60 * 24;
    const PAST_FACTOR = -1;

    public function beforeAction($action)
    {
        $this->enableCsrfValidation = false;
        return parent::beforeAction($action);
    }

    /** Очистка истории старше определенного периода
     * */
    public function actionIndex()
    {
        $diffMinutes = self::PAST_FACTOR * self::MINUTES_IN_DAY * Yii::$app->params['historyKeepDays'];
        $date = DateUtil::cloneDateWithShift(DateUtil::now(), $diffMinutes)->format('Y-m-d H:i:s');
        History::deleteAll(['<', 'created_at', $date]);
        echo "\n". $date;
    }
}
