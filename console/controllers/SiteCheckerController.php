<?php
namespace console\controllers;

use common\helpers\Helper;
use Yii;
use yii\web\Controller;
use yii\data\Pagination;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\web\BadRequestHttpException;
use yii\base\InvalidArgumentException;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use common\models\User;
use DateTime;
use common\helpers\DateUtil;
use common\models\Site;
use common\models\History;
use Exception;

// время Московское, чтобы начислять по-Москве
date_default_timezone_set('Europe/Moscow');

/**
 *
 * */
class SiteCheckerController extends Controller
{
    const REDIS_SITES_KEY = 'sites';
    const SITES_UPDATE_KEY = 'isSitesUpdatedLessThanDayAgo'; // сбросить в консоли: expire isSitesUpdatedLessThanDayAgo 1
    private $redis;

    public function beforeAction($action)
    {
        $this->redis = new \Redis() or die("Cannot load Redis module.");
        $this->redis->connect('localhost');

        $this->enableCsrfValidation = false;
        return parent::beforeAction($action);
    }

    /** Проверка включенных сайтов на доспутность
     * */
    public function actionIndex()
    {
        if (self::isSitesNeedUpdate()) {
            self::putSitesIntoRedis();
            self::delaySitesUpdateForDay();
        }
        self::runAsyncChecker();
    }

    /** Работа с Redis
     * */
    private function isSitesNeedUpdate()
    {
        return !$this->redis->exists('isSitesUpdatedLessThanDayAgo');
    }

    private function delaySitesUpdateForDay()
    {
        $this->redis->set(self::SITES_UPDATE_KEY, 'string no matter');
        $this->redis->expire(self::SITES_UPDATE_KEY, 86400);
    }

    private function putSitesIntoRedis()
    {
        echo 'ОБНОВЛЕНИЕ САЙТОВ ИЗ БД: '. DateUtil::now()->format('Y-m-d H:i:s') ."\n";
        $sitesFromDb = Site::find()->where(['status' => Site::STATUS_ENABLED])->all();

        // delete all sites from redis (на случай, если при удалении сайта не произошло его очистки в redis)
        $sitesArr = $this->redis->hkeys(self::REDIS_SITES_KEY);
        foreach ($sitesArr as $item) {
            $this->redis->hdel(self::REDIS_SITES_KEY, $item);
        }

        // adding sites to redis
        foreach ($sitesFromDb as $item) {
            $value = $item->interval .'_'. $item->id; // view: interval_site-id
            $this->redis->hset(self::REDIS_SITES_KEY, $item->url, $value);
        }
    }

    private function runAsyncChecker()
    {
        echo 'async start: '. DateUtil::now()->format('Y-m-d H:i:s') ."\n";
        $readyToCheckSites = self::prepareSitesList();
        $responses = self::checkSitesResponseCodes($readyToCheckSites);
        self::saveHistory($responses);
        echo 'async end: '. DateUtil::now()->format('Y-m-d H:i:s') ."\n";
    }

    private function saveHistory($responses)
    {
        $now = DateUtil::now()->format('Y-m-d H:i:s');
        $dataArr = [];
        foreach ($responses as $siteId => $code) {
            array_push($dataArr, [$now, null, $siteId, $code]);
        }

        Yii::$app->db->createCommand()
            ->batchInsert('history', ['created_at', 'updated_at', 'site_id', 'code'], $dataArr)
            ->execute();
    }

    private function prepareSitesList()
    {
        $list = array();
        $sites = $this->redis->hkeys(self::REDIS_SITES_KEY);
        foreach ($sites as $url) {
            try {
                $data = $this->redis->hget(self::REDIS_SITES_KEY, $url);
                if (self::isItTime(self::selectIntervalFromData($data))) {
                    $siteId = self::selectSiteIdFromData($data);
                    $list[$siteId] = $url;
                }
            } catch ( Exception $e ) {
                echo "error:\n<pre>\n";
                print_r($e);
                echo "\n</pre>";
            }
        }
        return $list;
    }


    private function checkSitesResponseCodes($sites)
    {
        // array of curl handles
        $multiCurl = [];
        // data to be returned
        $result = [];
        // multi handle
        $mh = curl_multi_init();
        foreach ($sites as $siteId => $url) {
            $multiCurl[$siteId] = curl_init();
            curl_setopt($multiCurl[$siteId], CURLOPT_URL, $url);
            curl_setopt($multiCurl[$siteId], CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($multiCurl[$siteId], CURLOPT_HEADER, 1);
            curl_setopt($multiCurl[$siteId], CURLOPT_NOBODY, 1);
            curl_setopt($multiCurl[$siteId], CURLOPT_FOLLOWLOCATION, 1);
            curl_setopt($multiCurl[$siteId], CURLOPT_MAXREDIRS, 10);
            curl_setopt($multiCurl[$siteId], CURLOPT_ENCODING, 0);
            curl_setopt($multiCurl[$siteId], CURLOPT_AUTOREFERER, 1);
            curl_setopt($multiCurl[$siteId], CURLOPT_CONNECTTIMEOUT, 10);
            curl_setopt($multiCurl[$siteId], CURLOPT_TIMEOUT, 10);
            curl_multi_add_handle($mh, $multiCurl[$siteId]);
        }
        $index = null;
        do {
            curl_multi_exec($mh,$index);
        } while($index > 0);

        // get content and remove handles
        foreach($multiCurl as $key => $ch) {
            $result[$key] = self::getCodeFromResponse(curl_multi_getcontent($ch));
            curl_multi_remove_handle($mh, $ch);
        }

        // close
        curl_multi_close($mh);

        return $result;
    }

    private function selectIntervalFromData($str)
    {
        $defaultInterval = 1;
        $delimiter  = '_';
        $pos = strpos($str, $delimiter);
        return (int)substr($str, 0, $pos) ?? $defaultInterval;
    }

    private function selectSiteIdFromData($value)
    {
        $delimiter = '_';
        $pos = strpos($value, $delimiter);
        $siteId = (int)substr($value, ($pos + 1), (strlen($value) - 1));
        if(!$siteId) {
            throw new Exception('Не найден id сайта (в строке из redis)');
        }
        return $siteId;
    }

    private function isItTime($interval)
    {
        $currMinute = (int)date('i'); // текущая минута должна быть кратна инвервалу проверки
        return ($currMinute % $interval === 0);
    }

    private function getResponseCode($url)
    {
        $options = array(
            CURLOPT_RETURNTRANSFER => true,   // return web page
            CURLOPT_HEADER         => true,  // don't return headers
            CURLOPT_NOBODY         => true,  // don't return body
            CURLOPT_FOLLOWLOCATION => true,   // follow redirects
            CURLOPT_MAXREDIRS      => 10,     // stop after 10 redirects
            CURLOPT_ENCODING       => '',     // handle compressed
            CURLOPT_AUTOREFERER    => true,   // set referrer on redirect
            CURLOPT_CONNECTTIMEOUT => 10,    // time-out on connect
            CURLOPT_TIMEOUT        => 10,    // time-out on response
        );
        $ch = curl_init($url);
        curl_setopt_array($ch, $options);
        $content = curl_exec($ch);
        curl_close($ch);

        if (preg_match('/HTTP\/[0-9.-]+\s+([\d]+)/', $content, $matches)) {
            return (!empty($matches) && count($matches) > 1) ? (int)$matches[1] : 0;
        }
        return 0;
    }

    private function getCodeFromResponse($content)
    {
        if (preg_match('/HTTP\/[0-9.-]+\s+([\d]+)/', $content, $matches)) {
            return (!empty($matches) && count($matches) > 1) ? (int)$matches[1] : 0;
        }
        return 0;
    }
}
