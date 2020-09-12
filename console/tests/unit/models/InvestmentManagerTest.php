<?php
namespace console\tests\unit\models;

use Codeception\Util\Debug;
use common\fixtures\InterestChangeOverTimeFixture;
use common\models\Investition;
use common\models\InterestChangeOverTime;
use common\models\ProfitCalculator;
use DateTime;
use Exception;

use Yii;
use yii\db\ActiveRecord;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior2;

class InvestmentManagerTest extends \Codeception\Test\Unit
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
        $this->testInterestChangeOverTime = InterestChangeOverTime::find()->where(['id' => 1])->one();
    }

    public function testLoLoLo()
    {
        $investition = new InterestChangeOverTime();
        Debug::debug('----');
        Debug::debug($this->testInterestChangeOverTime->id);
        Debug::debug('----');
        expect(true)->true();
    }

    /**
     * Проверка нахождения начала и конца месяца по любой дате
     * @param array $pairsArr массив значений [дата, начало_месяца, конец_месяца]
     * @throws Exception
     */
    /*
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
*/
}
