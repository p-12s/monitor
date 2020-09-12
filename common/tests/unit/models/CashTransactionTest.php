<?php
namespace common\tests\unit\models;

use Codeception\Util\Debug;
use common\models\User;
use common\fixtures\UserFixture;
use common\models\CashTransaction;
use common\fixtures\CashTransactionFixture;
use DateTime;
use Exception;
use common\helpers\DateUtil;

class CashTransactionTest extends \Codeception\Test\Unit
{
    /**
     * @var \common\tests\UnitTester
     */
    protected $tester;
    protected $testUsers;

    protected function _before()
    {
        $this->tester->haveFixtures([
            'cashTransactions' => [
                'class' => CashTransactionFixture::className()
            ],
            'users' => [
                'class' => UserFixture::className(),
                'dataFile' => '/var/www/vladelets/common/fixtures/data/user.php'
            ]
        ]);
        $this->testCashTransactions = CashTransaction::find()->all();
        $this->testUsers = User::find()->all();
    }

    /**
     * Проверка возможности получения юзера
     */
    /* function testCanGetUser()
    {
        expect(count($this->testCashTransactions))->equals(0);
    }*/

    /**
     * Проверка таблицы cash_transaction, по-умолчанию там пусто
     */
    public function testHaveNotRecordsInTableCash_transactionByDefault()
    {
        expect(count($this->testCashTransactions))->equals(0);
    }


    /**
     * Проверка метода создания транзакции
     */
    public function testCanCreateTransaction()
    {
        $randomSum = rand(0, 1000);

        $user = $this->testUsers[0];
        $id = CashTransaction::saveIntoDb(
            $user['id'], $randomSum, CashTransaction::TYPE_CLOSING_INVESTMENT );
        $transaction = CashTransaction::find()->where(['id' => $id])->one();
        expect($transaction['user_id'])->equals($user['id']);
        expect($transaction['sum'])->equals($randomSum);
        expect($transaction['type'])->equals(CashTransaction::TYPE_CLOSING_INVESTMENT);
        expect(count(CashTransaction::find()->all()))->equals(1);

        $user = $this->testUsers[1];
        $id = CashTransaction::saveIntoDb(
            $user['id'], $randomSum, CashTransaction::TYPE_MAKING_INVESTMENT_ELITE );
        $transaction = CashTransaction::find()->where(['id' => $id])->one();
        expect($transaction['user_id'])->equals($user['id']);
        expect($transaction['sum'])->equals($randomSum);
        expect($transaction['type'])->equals(CashTransaction::TYPE_MAKING_INVESTMENT_ELITE);
        expect(count(CashTransaction::find()->all()))->equals(2);
    }
}
