<?php
namespace common\tests\unit\models;

use Codeception\Util\Debug;
use common\models\Investition;
use common\fixtures\InvestitionFixture;
use common\models\User;
use common\fixtures\UserFixture;
use DateTime;
use Exception;
use common\helpers\DateUtil;

class UserTest extends \Codeception\Test\Unit
{
    /**
     * @var \common\tests\UnitTester
     */
    protected $tester;
    protected $testUsers;

    protected function _before()
    {
        $this->tester->haveFixtures([
            'users' => [
                'class' => UserFixture::className(),
                'dataFile' => '/var/www/vladelets/common/fixtures/data/user.php'
            ]
        ]);
        $this->testUsers = User::find()->all();
    }

    /**
     * Проверка связанной таблицы invest_user
     */
    public function testHaveUsersInTableInvest_user()
    {
        expect(count($this->testUsers))->equals(4);
    }


    /**
     * Проверка метода увеличения баланса
     */
    public function testCanRechargeBalance()
    {
        foreach ($this->testUsers as $user) {
            $randomBalanceAdding = rand( 0, 1000 );
            $balanceBefore = $user['balance'];
            User::rechargeBalance( $user['id'], $randomBalanceAdding );
            $userForCheck = User::find()->where(['id' => $user['id']])->all();
            $balanceAfter = $userForCheck[0]['balance'];
            expect($balanceAfter)->equals($balanceBefore + $randomBalanceAdding);
        }
    }
}
