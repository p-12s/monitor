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

class InvestitionTest extends \Codeception\Test\Unit
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
     * Проверка связанной таблицы invest_user
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

}
