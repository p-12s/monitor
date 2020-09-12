<?php

namespace common\helpers;

use Yii;
use yii\base\Model;
use DateTime;
use Codeception\Util\Debug;
use Exception;

date_default_timezone_set('Europe/Moscow');

class DateUtil
{
    /**
     * Поиск первого дня прошлого месяца (обычно метод запускается 1-го числа, значит ищем дату 1-го числа предыдущего месяца)
     * @param DateTime $defaultDate дата, которая берется за точку отсчета
     * @return DateTime
     */
    public function findFirstDayOfPreviousMonth($defaultDate = null)
    {
        if ($defaultDate === null) {
            return date_create(
                date_create( date( 'Y-m-01' ) )->modify('-1 day')->format( 'Y-m-01' ))
                ->setTime(0, 0);
        }
        if (!($defaultDate instanceof DateTime)) {
            trigger_error('Передан неверный тип даты в метод '. get_class_methods($this), E_USER_ERROR);
        }
        $firstDayFormat = $defaultDate->format('Y') .'-'. $defaultDate->format('m') .'-01';
        return date_create(
            date_create( date( $firstDayFormat ) )->modify('-1 day')->format( 'Y-m-01' ))
            ->setTime(0, 0);
    }

    /**
     * Поиск первого дня текущего месяца
     * @return DateTime
     */
    public static function findFirstDayOfCurrentMonth()
    {
        return date_create(date( 'Y-m-01 00:00:00' ))->setTime(0, 0);
    }

    /**
     * Сейчас
     * @return DateTime
     */
    public static function now()
    {
        return date_create(date( 'Y-m-d H:i:s' ));
    }

    /**
     * Проверка, прошел ли полный год от пришедшей даты
     * @param DateTime $date дата, которая берется за точку отсчета
     * @return boolean
     * @throws \Exception
     */
    public function isFullYearAlreadyPassed($date)
    {
        if (!($date instanceof DateTime)) {
            trigger_error('Передан неверный тип даты в метод '. get_class_methods($this), E_USER_ERROR);
        }

        $investmentEnding = new DateTime( $date->format('Y-m-d H:i:s') );
        $investmentEnding->modify('+1 year');
        $now = date_create(date('Y-m-d H:i:s'));
        return $investmentEnding <= $now;
    }

    /**
     * Поиск первого дня месяца
     * @param DateTime $date дата, которая берется за точку отсчета
     * @return DateTime
     */
    public function findFirstDay($date)
    {
        if (!($date instanceof DateTime)) {
            trigger_error('Передан неверный тип даты в метод '. get_class_methods($this), E_USER_ERROR);
        }
        $firstDayFormat = $date->format('Y') .'-'. $date->format('m') .'-01';
        return date_create(date( $firstDayFormat ))->setTime(0, 0);
    }

    /**
     * Поиск списка дат для ежемесячных ЧЛЕНСКИХ взносов. Начиная от следующего месяца от "даты активации" до текущего месяца
     * @param DateTime $date дата зачисления певроначального членского взноса (500р)
     * @return array()
     */
    public function findMembershipPaymentDays($date)
    {
        if (!($date instanceof DateTime)) {
            trigger_error('Передан неверный тип даты в метод '. get_class_methods($this), E_USER_ERROR);
        }

        // находим первое число следующего месяца - с этого дня должны будут вноситься платежи
        $firstDayFormat = $date->format('Y') .'-'. $date->format('m') .'-01';
        $day = date_create(date( $firstDayFormat ))->setTime(0, 0);
        $day->modify('+1 month');

        // первое число текущего мес. - этот день включительно платежи должны быть внесены
        $currMonthFirstDay = date_create(date( 'Y-m-d' ))->setTime(0, 0);
        $days = array();
        while($day <= $currMonthFirstDay) {
            array_push($days, date_create($day->format('Y-m-d'))->setTime(0, 0));//
            $day->modify('+1 month');
        }
        return $days;
    }

    /**
     * Поиск списка дат для ежемесячных ИПОТЕЧНЫХ взносов. Начиная от следующего месяца от "даты активации" до текущего месяца
     * @param DateTime $date дата принятия 25% взноса
     * @return array()
     */
    public function findMortgagePaymentDays($date)
    {
        if (!($date instanceof DateTime)) {
            trigger_error('Передан неверный тип даты в метод '. get_class_methods($this), E_USER_ERROR);
        }

        // находим дату со следующего месяца - с этого дня должны будут вноситься платежи
        $day = date_create($date->format('Y-m-d'))->setTime(0, 0);
        $day->modify('+1 month');
        $days = array();
        while($day <= self::now()) {
            array_push($days, date_create($day->format('Y-m-d'))->setTime(0, 0));//
            $day->modify('+1 month');
        }
        return $days;
    }

    public function cloneDateWithShift($date, $diffMinutes = 0)
    {
        if (!($date instanceof DateTime)) {
            trigger_error('Передан неверный тип даты в метод '. get_class_methods($this), E_USER_ERROR);
        }

        $newDate = date_create($date->format('Y-m-d H:i:s'));
        $diff = ($diffMinutes >= 0) ? '+'. $diffMinutes : $diffMinutes;
        $newDate->modify($diff .' minutes');

        return $newDate;
    }
}
