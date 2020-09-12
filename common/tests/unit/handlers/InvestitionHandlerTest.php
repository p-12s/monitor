<?php
namespace common\tests\unit\models;

use Codeception\Util\Debug;
use common\models\Investition;
use common\models\CashTransaction;
use common\fixtures\InvestitionFixture;
use common\models\ProfitCalculator;
use common\models\User;
use common\fixtures\UserFixture;
use DateTime;
use Exception;
use common\helpers\DateUtil;
use common\handlers\InvestitionHandler;

class _InvestitionHandler extends InvestitionHandler
{
    public function _runClosingProcess()
    {
        return parent::runClosingProcess();
    }

    public static function _processInvestition( $investition, $interestForGrand, $interestForGrandElite )
    {
        return InvestitionHandler::processInvestition( $investition, $interestForGrand, $interestForGrandElite );
    }

    public static function _close( $userId, $investmentId, $profit, $type, $sum )
    {
        return parent::close( $userId, $investmentId, $profit, $type, $sum );
    }

    public static function _calculateSum( $list )
    {
        return parent::calculateSum( $list );
    }
}

class InvestitionHandlerTest extends \Codeception\Test\Unit
{
    /**
     * @var \common\tests\UnitTester
     */
    protected $tester;
    protected $testInvestitions;
    protected $testNotClosedInvestitions;

    protected function _before()
    {
        $this->tester->haveFixtures([
            'investitions' => [
                'class' => InvestitionFixture::className(),
                'dataFile' => '/var/www/vladelets/common/fixtures/data/investition.php'
            ],
            'users' => [
                'class' => UserFixture::className(),
                'dataFile' => '/var/www/vladelets/common/fixtures/data/user.php'
            ]
            // TODO добавить транзации
        ]);
        $this->testInvestitions = Investition::find()->all();
        $this->testNotClosedInvestitions = Investition::find()
            ->where(['is', 'closed_at', new \yii\db\Expression('null')])
            ->andWhere(['status' => Investition::STATUS_ACTIVE])
            ->orderBy(['created_at' => SORT_ASC])
            ->all();
    }

    private function getNotClosed()
    {
        return Investition::find()
            ->where(['is', 'closed_at', new \yii\db\Expression('null')])
            ->andWhere(['status' => Investition::STATUS_ACTIVE])
            ->orderBy(['created_at' => SORT_ASC])
            ->all();
    }

    /**
     * Проверка, подгружается ли связанная таблица invest_user
     */
    public function testCanGetUserByUser_id()
    {
        $investment = $this->testInvestitions[0];
        expect($investment->user_id)->equals($investment->user->id);
        $investment1 = $this->testInvestitions[1];
        expect($investment1->user_id)->equals($investment1->user->id);
        $investment2 = $this->testInvestitions[2];
        expect($investment2->user_id)->equals($investment2->user->id);
    }

    /**
     * Проверка возможности закртытия инвестиций, у которых прошел срок вклада
     */
    public function testCanCloseInvestment()
    {
        $allNotClosedInvestments = self::getNotClosed();
        // в тестовых данных дожны быть инвестиции для закрытия (просроченые)
        expect(count($allNotClosedInvestments))->notEquals(0);

        $_PROFIT = 322.05; // условная прибыль за последний месяц
        foreach ($allNotClosedInvestments as $investment) {
            // Закроем инвестицию
            _InvestitionHandler::_close($investment->user_id, $investment->id, $_PROFIT, $investment->type_id, $investment->sum);
            // Подготовим дату закрытия и прибыль, которые пригодятся при проверке
            $closeDateTime = date_create(date('Y-m-d H:i:s'));
            $profitToTableCell = ( $investment->type_id === (int)Investition::TYPE_GRAND_ELITE ) ? $_PROFIT : 0;
            // 1) Проверка изменений в таблице Investition
            $savedInv = Investition::find()->where(['id' => $investment->id])->one();
            // Действительно профит, статус, дата закрытия - указанные
            expect($savedInv['profit'])->equals($profitToTableCell);
            expect($savedInv['status'])->equals(Investition::STATUS_CLOSED);
            expect($savedInv['closed_at'])->equals($closeDateTime->format('Y-m-d H:i:s'));
            // 2) Проверка изменений в таблице CashTransaction
            $totalAmount = $investment->sum + $_PROFIT;
            $savedCashTransaction = CashTransaction::find()
                ->where(['updated_at' => $closeDateTime->format('Y-m-d H:i:s')])
                ->andWhere(['user_id' => $savedInv->user_id])
                ->andWhere(['sum' => $totalAmount])
                ->andWhere(['type' => CashTransaction::TYPE_CLOSING_INVESTMENT])
                ->all();

            // действительно есть транзакция, и она одна
            expect(count($savedCashTransaction))->equals(1);
        }
    }

    /**
     * Проверка возможности подсчитать сумму инвестиций у списка
     */
    public function testCanCalculateSumOfInvestment()
    {
        $result = _InvestitionHandler::_calculateSum($this->testInvestitions);
        expect($result)->equals(37300);

        $this->tester->expectThrowable('yii\base\ErrorException', function () {
            $fakeArray = array(array('sum' => 1000));
            _InvestitionHandler::_calculateSum($fakeArray);
        });
    }

    /**
     * Проверка возможности обработать список инвестиций, и закрыть подошедшие
     * (проверка упрощенная, проверяю только статус)
     */
    public function testCanRunAndCompleteClosingProcess()
    {
        // до закрытия
        foreach ($this->testNotClosedInvestitions as $item) {
            if ( DateUtil::isFullYearAlreadyPassed(new DateTime( $item['created_at'] )) ) {
                expect($item['status'])->equals(Investition::STATUS_ACTIVE);
            }
        }
        // после закрытия
        _InvestitionHandler::_runClosingProcess($this->testInvestitions);

        // расчет средневзвешенной %-й ставки.
        // Он будет одинаковым для всех вкладов, закрывающихся сегодня
        // просто копирую код, здесь его не тестирую
        $profitCalculator = new ProfitCalculator(DateUtil::findFirstDayOfCurrentMonth(), DateUtil::now());
        $interestForGrand = $profitCalculator->calculateInterestForClose(Investition::TYPE_GRAND);
        $interestForGrandElite = $profitCalculator->calculateInterestForClose(Investition::TYPE_GRAND_ELITE);

        $investments = Investition::find()->all();
        foreach ($investments as $item) {
            if ( !DateUtil::isFullYearAlreadyPassed(new DateTime( $item['created_at'] )) ) {
                expect($item['status'])->equals(Investition::STATUS_ACTIVE);
            }
            expect($item['status'])->equals(Investition::STATUS_CLOSED);
        }
    }
}
