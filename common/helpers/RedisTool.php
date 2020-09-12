<?php

namespace common\helpers;

use Yii;
use yii\base\Model;
use DateTime;
use Codeception\Util\Debug;
use Exception;

date_default_timezone_set('Europe/Moscow');

/** Работа с Redis
 */
class RedisTool
{
    const REDIS_SITES_KEY = 'sites';

    /** Обновление данных
     * @param $oldUrl string предыдущий адрес сайта
     * @param $newUrl string новый адрес сайта
     * @param $interval int интервал мониторинга
     * @param $id int id сайта
     * @return bool
     */
    public function IsDataUpdatedSuccessfully($oldUrl, $newUrl, $interval, $id)
    {
        try {
            $redis = new \Redis() or die("Cannot load Redis module.");
            $redis->connect('localhost');

            $value = $interval .'_'. $id; // view: interval_site-id
            if ($oldUrl === $newUrl) {
                $redis->hset(self::REDIS_SITES_KEY, $oldUrl, $value);
            } else {
                $redis->hdel(self::REDIS_SITES_KEY, $oldUrl);
                $redis->hset(self::REDIS_SITES_KEY, $newUrl, $value);
            }
            return true;

        } catch (Exception $e) {
            echo "error:\n<pre>\n";
            print_r($e);
            echo "\n</pre>";

            return false;
        }
    }

    /** Удаление данных
     * @param $url string адрес сайта
     * @return bool
     */
    public function IsDataDeleteSuccessfully($url)
    {
        try {
            $redis = new \Redis() or die("Cannot load Redis module.");
            $redis->connect('localhost');

            $redis->hdel(self::REDIS_SITES_KEY, $url);
            return true;

        } catch (Exception $e) {
            echo "error:\n<pre>\n";
            print_r($e);
            echo "\n</pre>";

            return false;
        }
    }

    /** Создание данных
     * @param $url string адрес сайта
     * @param $interval int интервал мониторинга
     * @param $id int id сайта
     * @return bool
     */
    public function IsDataCreatedSuccessfully($url, $interval, $id)
    {
        try {
            $redis = new \Redis() or die("Cannot load Redis module.");
            $redis->connect('localhost');

            $value = $interval .'_'. $id; // view: interval_site-id
            $redis->hset(self::REDIS_SITES_KEY, $url, $value);
            return true;

        } catch (Exception $e) {
            echo "error:\n<pre>\n";
            print_r($e);
            echo "\n</pre>";

            return false;
        }
    }
}
