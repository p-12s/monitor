<?php
namespace common\tests\unit\models;

use Codeception\Util\Debug;
use common\helpers\DateUtil;
use DateTime;
use Exception;

class DateUtilTest extends \Codeception\Test\Unit
{
    /**
     * @var \common\tests\UnitTester
     */

    /**
     * Создание массива дат за весь 2020 год
     * @return array
     * @throws Exception
     */
    private function createYearsDate()
    {
        $year = array(1 => array(), 2 => array(), 3 => array(), 4 => array(), 5 => array(), 6 => array(),
            7 => array(), 8 => array(), 9 => array(), 10 => array(), 11 => array(), 12 => array());
        $date = date_create('2020-01-01 00:00:00');
        for ($i = 0; $i <= 366; $i++) {
            $date->modify('+1 day');
            array_push($year[intval($date->format('n'))], new DateTime($date->format( 'Y-m-d H:i:s' )));//
        }
        return $year;
    }
    public function testCanFindFirstDateOfPreviousMonth()
    {
        $yearsDates = self::createYearsDate();
        foreach ($yearsDates as $monthDates) {
            foreach ($monthDates as $date) {
                $desiredDate = DateUtil::findFirstDayOfPreviousMonth($date);
                $firstDayFormat = $date->format('Y') .'-'. $date->format('m') .'-01';
                $firstPreviousMonthDate = date_create(
                        date_create( date($firstDayFormat) )->modify('-1 day')->format( 'Y-m-01' ))
                        ->setTime(0, 0);
                expect($desiredDate)->equals($firstPreviousMonthDate);
            }
        }
        // передача неправильных типов данных (не DataType)
        $this->tester->expectThrowable('yii\base\ErrorException', static function() {
            $datUtil = new DateUtil();
            $datUtil->findFirstDayOfPreviousMonth(12345);
        });
        $this->tester->expectThrowable('yii\base\ErrorException', static function() {
            $datUtil = new DateUtil();
            $datUtil->findFirstDayOfPreviousMonth(123.45);
        });
        $this->tester->expectThrowable('yii\base\ErrorException', static function() {
            $datUtil = new DateUtil();
            $datUtil->findFirstDayOfPreviousMonth(array());
        });
    }

    /**
     * Создание массива дат за весь 2020 год
     * @return array
     * @throws Exception
     */
    private function getYearAgoDate()
    {
        $yearAgo = date_create(date('Y-m-d H:i:s'));
        return $yearAgo->modify('-1 year');
    }
    public function testCanCheckIsFullYearAlreadyPassed()
    {
        $yearAgo = self::getYearAgoDate();
        expect(DateUtil::isFullYearAlreadyPassed($yearAgo))->equals(true);

        $yearAgo = self::getYearAgoDate();
        $yearAgo->modify('+10 minutes');
        expect(DateUtil::isFullYearAlreadyPassed($yearAgo))->equals(false);

        $yearAgo = self::getYearAgoDate();
        $yearAgo->modify('+100 minutes');
        expect(DateUtil::isFullYearAlreadyPassed($yearAgo))->equals(false);

        $yearAgo = self::getYearAgoDate();
        $yearAgo->modify('-10 minutes');
        expect(DateUtil::isFullYearAlreadyPassed($yearAgo))->equals(true);

        $yearAgo = self::getYearAgoDate();
        $yearAgo->modify('-100 minutes');
        expect(DateUtil::isFullYearAlreadyPassed($yearAgo))->equals(true);

        // передача неправильных типов данных (не DataType)
        $this->tester->expectThrowable('yii\base\ErrorException', static function() {
            $datUtil = new DateUtil();
            $datUtil->isFullYearAlreadyPassed(12345);
        });
        $this->tester->expectThrowable('yii\base\ErrorException', static function() {
            $datUtil = new DateUtil();
            $datUtil->isFullYearAlreadyPassed(123.45);
        });
        $this->tester->expectThrowable('yii\base\ErrorException', static function() {
            $datUtil = new DateUtil();
            $datUtil->isFullYearAlreadyPassed(array());
        });
    }

    public function testCanFindFirstDatetimeOfCurrentMonth()
    {
        $now = date_create(date( 'Y-m-01 00:00:00' ));
        expect(DateUtil::findFirstDayOfCurrentMonth())->equals($now);
    }
}
