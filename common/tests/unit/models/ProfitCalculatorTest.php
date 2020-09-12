<?php
namespace common\tests\unit\models;

use Codeception\Util\Debug;
use common\fixtures\InterestChangeOverTimeFixture;
use common\models\Investition;
use common\models\ProfitCalculator;
use DateTime;
use Exception;

class _ProfitCalculator extends ProfitCalculator
{
    public function __construct($date)
    {
        return new ProfitCalculator($date);
    }

    public function _calculateDurationOfMonthInSeconds($date)
    {
        return $this->calculateDurationOfMonthInSeconds($date);
    }

    public function _isDepositPeriodStartsFromFirstSecondOfMonth($date)
    {
        return $this->isDepositPeriodStartsFromFirstSecondOfMonth($date);
    }

    public function _findMonthStart($date)
    {
        return $this->findMonthStart($date);
    }

    public function _findMonthEnd($date)
    {
        return $this->findMonthEnd($date);
    }

    public function _countArrayElements($arr)
    {
        return $this->countArrayElements($arr);
    }

    public function _calculateInterest($depositBeginDate, $investitionType)
    {
        return $this->calculateInterest($depositBeginDate, $investitionType);
    }
}

class ProfitCalculatorTest extends \Codeception\Test\Unit
{
    /**
     * @var \common\tests\UnitTester
     */
    protected $tester;
    protected $testInterestChangeOverTime;
    const DEFAULT_DATE_STRING_VIEW = '2020-03-31 00:00:00';

    protected function _before()
    {
        $this->tester->haveFixtures([
            'interest_changes_over_time' => [
                'class' => InterestChangeOverTimeFixture::className(),
                'dataFile' => '/var/www/vladelets/common/fixtures/data/interest_change_over_time.php'
            ]
        ]);
    }

    /**
     * Проверка нахождения начала и конца месяца по любой дате
     * @param array $pairsArr массив значений [дата, начало_месяца, конец_месяца]
     * @throws Exception
     */
    private function checkDateInLoop($pairsArr = [])
    {
        $profitCalculator = new _ProfitCalculator(new DateTime( self::DEFAULT_DATE_STRING_VIEW ));
        foreach ($pairsArr as $data) {
            expect($profitCalculator->_findMonthStart(new DateTime( $data[0] )))->equals(new DateTime( $data[1] ));
            expect($profitCalculator->_findMonthEnd(new DateTime( $data[0] )))->equals(new DateTime( $data[2] ));
        }
    }
    public function testForAnyDateCanFindMonthStartAndMonthEnd()
    {
        self::checkDateInLoop(array(
            ['2020-03-31 00:00:00', '2020-03-01 00:00:00', '2020-03-31 23:59:59'], ['2020-03-31 00:00:02', '2020-03-01 00:00:00', '2020-03-31 23:59:59'],
            ['2020-03-31 00:04:00', '2020-03-01 00:00:00', '2020-03-31 23:59:59'], ['2020-03-31 15:00:00', '2020-03-01 00:00:00', '2020-03-31 23:59:59'],
            ['2020-03-23 00:00:00', '2020-03-01 00:00:00', '2020-03-31 23:59:59'], ['2020-03-01 00:00:00', '2020-03-01 00:00:00', '2020-03-31 23:59:59'],
            ['2020-02-20 15:05:25', '2020-02-01 00:00:00', '2020-02-29 23:59:59'], ['2020-01-14 00:00:00', '2020-01-01 00:00:00', '2020-01-31 23:59:59'],
            ['2019-02-20 15:05:25', '2019-02-01 00:00:00', '2019-02-28 23:59:59'], ['2018-02-14 00:00:00', '2018-02-01 00:00:00', '2018-02-28 23:59:59'],
            ['2021-02-20 15:05:25', '2021-02-01 00:00:00', '2021-02-28 23:59:59'], ['2017-02-14 00:00:00', '2017-02-01 00:00:00', '2017-02-28 23:59:59'],
            ['2022-02-20 15:05:25', '2022-02-01 00:00:00', '2022-02-28 23:59:59'], ['2023-02-14 00:00:00', '2023-02-01 00:00:00', '2023-02-28 23:59:59'],
            ['2022-11-05 19:05:25', '2022-11-01 00:00:00', '2022-11-30 23:59:59'], ['2023-05-19 16:43:32', '2023-05-01 00:00:00', '2023-05-31 23:59:59'],
        ));
        // передача неправильных типов данных (не DataType)
        $this->tester->expectThrowable('yii\base\ErrorException', function() {
            $profitCalculator = new _ProfitCalculator(date_create( self::DEFAULT_DATE_STRING_VIEW ));
            $profitCalculator->_findMonthStart(12345);
        });
        $this->tester->expectThrowable('yii\base\ErrorException', function() {
            $profitCalculator = new _ProfitCalculator(date_create( self::DEFAULT_DATE_STRING_VIEW ));
            $profitCalculator->_findMonthStart(12345.21);
        });
        $this->tester->expectThrowable('yii\base\ErrorException', function() {
            $profitCalculator = new _ProfitCalculator(date_create( self::DEFAULT_DATE_STRING_VIEW ));
            $profitCalculator->_findMonthStart(null);
        });
        $this->tester->expectThrowable('yii\base\ErrorException', function() {
            $profitCalculator = new _ProfitCalculator(date_create( self::DEFAULT_DATE_STRING_VIEW ));
            $profitCalculator->_findMonthStart(array());
        });
        $this->tester->expectThrowable('yii\base\ErrorException', function() {
            $profitCalculator = new _ProfitCalculator(date_create( self::DEFAULT_DATE_STRING_VIEW ));
            $profitCalculator->_findMonthEnd(12345);
        });
        $this->tester->expectThrowable('yii\base\ErrorException', function() {
            $profitCalculator = new _ProfitCalculator(date_create( self::DEFAULT_DATE_STRING_VIEW ));
            $profitCalculator->_findMonthEnd(12345.21);
        });
        $this->tester->expectThrowable('yii\base\ErrorException', function() {
            $profitCalculator = new _ProfitCalculator(date_create( self::DEFAULT_DATE_STRING_VIEW ));
            $profitCalculator->_findMonthEnd(null);
        });
        $this->tester->expectThrowable('yii\base\ErrorException', function() {
            $profitCalculator = new _ProfitCalculator(date_create( self::DEFAULT_DATE_STRING_VIEW ));
            $profitCalculator->_findMonthEnd(array());
        });
    }

    /**
     * Проверка значений
     * @param array $pairsArr массив пар [значение, должна_ли_быть_ошибка]
     * @throws Exception
     */
    private function checkPairsOfDateInLoop($pairsArr = [])
    {
        $profitCalculator = new _ProfitCalculator(new DateTime( self::DEFAULT_DATE_STRING_VIEW ));
        foreach ($pairsArr as $data) {
            if ($data[1]) {
                expect($profitCalculator->_isDepositPeriodStartsFromFirstSecondOfMonth(date_create($data[0])))->true();
            } else {
                expect($profitCalculator->_isDepositPeriodStartsFromFirstSecondOfMonth(date_create($data[0])))->false();
            }
        }
    }
    public function testCanCheckIsThisBeginMonthDatetimeOrNot()
    {
        self::checkPairsOfDateInLoop(array(
            ['2020-01-01 00:00:00', true], ['2020-02-01 00:00:00', true], ['2020-03-01 00:00:00', true],
            ['2020-04-01 00:00:00', true], ['2020-05-01 00:00:00', true], ['2020-06-01 00:00:00', true],
            ['2020-07-01 00:00:00', true], ['2020-08-01 00:00:00', true], ['2020-09-01 00:00:00', true],
            ['2020-10-01 00:00:00', true], ['2020-11-01 00:00:00', true], ['2020-10-01 00:00:00', true],
            ['2021-10-01 00:00:00', true], ['2022-11-01 00:00:00', true], ['2019-10-01 00:00:00', true],
            ['2020-10-02 00:00:00', false], ['2020-11-03 00:02:00', false], ['2020-10-01 9:00:00', false],
            ['2020-10-02 00:04:00', false], ['2020-11-03 00:02:00', false], ['2020-10-02 1:01:00', false],
            ['2020-10-02 00:02:00', false], ['2020-11-03 00:02:00', false], ['2020-10-01 20:00:00', false],
            ['2020-10-02 00:10:00', false], ['2020-11-03 00:05:55', false], ['2020-10-01 0:00:55', false],
            ['2020-10-02 01:00:00', false], ['2020-11-03 00:02:00', false], ['2020-10-01 0:33:00', false]
        ));
        // передача неправильных типов данных (не DataType)
        $profitCalculator = new _ProfitCalculator(date_create( self::DEFAULT_DATE_STRING_VIEW ));
        expect($profitCalculator->_isDepositPeriodStartsFromFirstSecondOfMonth('2020-01-01 00:03:00'))->false();
        expect($profitCalculator->_isDepositPeriodStartsFromFirstSecondOfMonth('2020-01-02 03:33:33'))->false();
        expect($profitCalculator->_isDepositPeriodStartsFromFirstSecondOfMonth(12345))->false();
        expect($profitCalculator->_isDepositPeriodStartsFromFirstSecondOfMonth(12345.21))->false();
        expect($profitCalculator->_isDepositPeriodStartsFromFirstSecondOfMonth(null))->false();
        expect($profitCalculator->_isDepositPeriodStartsFromFirstSecondOfMonth(array()))->false();
    }

    /**
     * Расчет секунд в месяце по кол-ву дней
     * @param int $days кол-во дней
     * @return int
     */
    private function countSecondsByDays($days)
    {
        return 60 * 60 * 24 * $days;
    }
    private function checkDurationInLoop($pairsArr = [])
    {
        $profitCalculator = new _ProfitCalculator(new DateTime( self::DEFAULT_DATE_STRING_VIEW ));
        foreach ($pairsArr as $data) {
            $durationInSeconds = $profitCalculator->_calculateDurationOfMonthInSeconds(date_create($data[0]));
            expect(self::countSecondsByDays($data[1]))->equals($durationInSeconds);
        }
    }
    public function testCanCalculateMonthDurationInSeconds()
    {
        self::checkDurationInLoop(array(
            ['2020-01-01 00:00:00', 31], ['2020-02-21 12:21:20', 29],
            ['2020-03-01 00:10:00', 31], ['2020-04-21 12:21:20', 30],
            ['2020-05-01 10:00:00', 31], ['2020-06-21 11:21:20', 30],
            ['2020-07-01 00:00:10', 31], ['2020-08-21 12:21:20', 31],
            ['2020-09-01 00:01:00', 30], ['2020-10-21 12:11:20', 31],
            ['2020-11-01 01:00:00', 30], ['2020-12-21 12:21:20', 31],
            ['2019-02-01 01:00:00', 28], ['2016-02-21 12:21:20', 29],
            ['2018-02-01 01:00:00', 28], ['2015-02-21 12:21:20', 28],
            ['2017-02-01 01:00:00', 28], ['2021-02-21 12:21:20', 28],
            ['2022-02-01 01:00:00', 28], ['2024-02-21 12:21:20', 29],
        ));
        $this->tester->expectThrowable('yii\base\ErrorException', function() {
            $profitCalculator = new _ProfitCalculator(new DateTime( self::DEFAULT_DATE_STRING_VIEW ));
            $profitCalculator->_calculateDurationOfMonthInSeconds(null);
        });
        $this->tester->expectThrowable('yii\base\ErrorException', function() {
            $profitCalculator = new _ProfitCalculator(new DateTime( self::DEFAULT_DATE_STRING_VIEW ));
            $profitCalculator->_calculateDurationOfMonthInSeconds(12345);
        });
        $this->tester->expectThrowable('yii\base\ErrorException', function() {
            $profitCalculator = new _ProfitCalculator(new DateTime( self::DEFAULT_DATE_STRING_VIEW ));
            $profitCalculator->_calculateDurationOfMonthInSeconds(123.56);
        });
        $this->tester->expectThrowable('yii\base\ErrorException', function() {
            $profitCalculator = new _ProfitCalculator(new DateTime( self::DEFAULT_DATE_STRING_VIEW ));
            $profitCalculator->_calculateDurationOfMonthInSeconds("some_string");
        });
        $this->tester->expectThrowable('yii\base\ErrorException', function() {
            $profitCalculator = new _ProfitCalculator(new DateTime( self::DEFAULT_DATE_STRING_VIEW ));
            $profitCalculator->_calculateDurationOfMonthInSeconds(array());
        });
    }

    /**
     * Расчет средне-взвешенной ставки по вкладу
     * контроль выполнялся в таблице https://docs.google.com/spreadsheets/d/1dMx5JSKQlaTaq0pjwLslethfI9bdg5Rc43E3vqjg-B4/edit#gid=0
     * @param object $model объект формы
     * @param boolean $mustBeSumError должна ли быть ошибка при проверке СУММЫ ?
     * @param boolean $mustBePayCodeError должна ли быть ошибка при проверке ПЛАТЕЖНОГО ПАРОЛЯ ?
     * @throws Exception
     */
    private function checkInterestEquals($pairsArr = [])
    {
        $profitCalculator = new ProfitCalculator(new DateTime( self::DEFAULT_DATE_STRING_VIEW ));
        foreach ($pairsArr as $data) {
            $resultInterest = $profitCalculator->calculateInterest(date_create($data[0]), Investition::TYPE_GRAND);
            expect($resultInterest)->equals($data[1]);
        }
    }
    private function checkInvestitionCloseInterestEquals($pairsArr = [])
    {
        foreach ($pairsArr as $data) {
            $profitCalculator = new ProfitCalculator(date_create( $data[0] ), date_create($data[1]));
            $resultInterest = $profitCalculator->calculateInterest(date_create($data[0]), Investition::TYPE_GRAND);
            expect($resultInterest)->equals($data[2]);
        }
    }
    public function testAsResultCanCalculateWeightedArithmeticInterest()
    {
        self::checkInterestEquals(array(
            ['2020-03-01 00:00:00', 0.03968], ['2020-03-01 12:00:00', 0.03952],
            ['2020-03-02 00:00:00', 0.03935], ['2020-03-03 12:00:00', 0.03887],
            ['2020-03-10 00:00:00', 0.03677], ['2020-03-15 00:00:00', 0.03516]
        ));
        $this->tester->expectThrowable('yii\base\ErrorException', function() {
            $profitCalculator = new ProfitCalculator(new DateTime( self::DEFAULT_DATE_STRING_VIEW ));
            $profitCalculator->calculateInterest(12345, Investition::TYPE_GRAND);
        });
        $this->tester->expectThrowable('yii\base\ErrorException', function() {
            $profitCalculator = new ProfitCalculator(new DateTime( self::DEFAULT_DATE_STRING_VIEW ));
            $profitCalculator->calculateInterest(12345.65, Investition::TYPE_GRAND_ELITE);
        });
        $this->tester->expectThrowable('yii\base\ErrorException', function() {
            $profitCalculator = new ProfitCalculator(new DateTime( self::DEFAULT_DATE_STRING_VIEW ));
            $profitCalculator->calculateInterest(null, Investition::TYPE_GRAND);
        });
        $this->tester->expectThrowable('yii\base\ErrorException', function() {
            $profitCalculator = new ProfitCalculator(new DateTime( self::DEFAULT_DATE_STRING_VIEW ));
            $profitCalculator->calculateInterest(array(), Investition::TYPE_GRAND_ELITE);
        });
        $this->tester->expectThrowable('yii\base\ErrorException', function() {
            $profitCalculator = new ProfitCalculator(new DateTime( self::DEFAULT_DATE_STRING_VIEW ));
            $profitCalculator->calculateInterest(date_create('2020-03-01 12:00:00'), 33);
        });
        $this->tester->expectThrowable('yii\base\ErrorException', function() {
            $profitCalculator = new ProfitCalculator(new DateTime( self::DEFAULT_DATE_STRING_VIEW ));
            $profitCalculator->calculateInterest(date_create('2020-03-01 12:00:00'), -33);
        });

        self::checkInvestitionCloseInterestEquals(array(
            // постепенно отодвигаем дату окончания депозита от конца месяца - имитируем закрытие вклада
            ['2020-03-01 00:00:00', '2020-03-30 23:59:59', 0.03645], ['2020-03-01 00:00:00', '2020-03-29 23:59:59', 0.03323],
            ['2020-03-01 00:00:00', '2020-03-28 23:59:59', 0.03000], ['2020-03-01 00:00:00', '2020-03-27 23:59:59', 0.02677],
            ['2020-03-01 00:00:00', '2020-03-26 23:59:59', 0.02355], ['2020-03-01 00:00:00', '2020-03-25 23:59:59', 0.02032],
            ['2020-03-01 00:00:00', '2020-03-24 23:59:59', 0.01871], ['2020-03-01 00:00:00', '2020-03-23 23:59:59', 0.01710],
            // отодвигаем дату начала депозита - имитируем случай создания вклада в текущем месяце
            ['2020-03-02 00:00:00', '2020-03-30 23:59:59', 0.03613], ['2020-03-03 00:00:00', '2020-03-30 23:59:59', 0.03581],
            ['2020-03-04 00:00:00', '2020-03-30 23:59:59', 0.03548], ['2020-03-05 00:00:00', '2020-03-31 23:59:59', 0.03839],
        ));
    }
}
