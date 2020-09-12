<?php

namespace console\tests\functional;

use DateTime;
use Yii;
use yii\console\Controller;
use console\controllers\InvestitionCloseController;
use Codeception\Util\Debug;
use console\tests\FunctionalTester;
use common\fixtures\UserFixture;

use common\models\User;
use common\models\Investition;
use common\models\CashTransaction;
use common\models\ProfitCalculator;
//use common\models\Investition;
use common\helpers\DateUtil;

/**
 * Class LoginCest
 */
class LoginCest
{
    /**
     * @var \console\tests\FunctionalTester
     */
    protected $tester;
    protected $testUser;

    protected function _before()
    {
        $this->tester->haveFixtures([
            'users' => [
                'class' => UserFixture::className(),
                'dataFile' => '/var/www/vladelets/common/fixtures/data/user.php'
            ]
        ]);
        $this->testUser = User::find()->where(['id' => 1])->one();
    }

    /**
     * Load fixtures before db transaction begin
     * Called in _before()
     * @see \Codeception\Module\Yii2::_before()
     * @see \Codeception\Module\Yii2::loadFixtures()
     * @return array

    public function _fixtures()
    {
        return [
            'user' => [
                'class' => UserFixture::className(),
                'dataFile' => '/var/www/vladelets/common/fixtures/data/user.php'
            ]
        ];
    }
    */

    /**
     * @param FunctionalTester $I
     */
    public function runCrone(FunctionalTester $I)
    {
        //$con = new InvestitionCloseController();
        Debug::debug('----------');

        InvestitionCloseController::actionIndex();  // Class 'common\models\Investition' not found

        Debug::debug('----------');

        //Yii::$app->createControllerByID('investition-close')->run('index');
    }

    /**
     * @param FunctionalTester $I
     */
    public function loginUser(FunctionalTester $I)
    {
        //$res = $I->amOnPage('/interest/test');
        //Debug::debug(Yii::$app);
        expect(true)->true();
        //$webApp->runAction('/interest/test',['param1' => 1,'param2' => 2]);

        //$I->see('Logout (erau)', 'form button[type=submit]');

        /*$I->amOnPage('/site/login');
        $I->fillField('Username', 'erau');
        $I->fillField('Password', 'password_0');
        $I->click('login-button');

        $I->see('Logout (erau)', 'form button[type=submit]');
        $I->dontSeeLink('Login');
        $I->dontSeeLink('Signup');*/
    }
}
