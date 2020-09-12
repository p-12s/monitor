<?php
namespace frontend\tests\unit\models;

use Codeception\Util\Debug;
use common\fixtures\UserFixture;
use common\models\User;
use frontend\models\PasswordResetRequestForm;

class PasswordResetRequestFormTest extends \Codeception\Test\Unit
{
    /**
     * @var \frontend\tests\UnitTester
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
     * Проверка валидации
     * @param object $model объект формы
     * @param boolean $mustBeSumError должна ли быть ошибка при проверке СУММЫ ?
     * @param boolean $mustBePayCodeError должна ли быть ошибка при проверке ПЛАТЕЖНОГО ПАРОЛЯ ?
     */
    /*private function checkExpectedValues($model, $mustBeSumError, $mustBePayCodeError)
    {
        if ($mustBeSumError) {
            expect('test 1', $model->errors)->hasKey('email');
        } else {
            expect('test 1', $model->errors)->hasntKey('email');
        }
        if ($mustBePayCodeError) {
            expect('test 2', $model->errors)->hasKey('reCaptcha');
        } else {
            expect('test 2', $model->errors)->hasntKey('reCaptcha');
        }
    }
    public function testHaveTwoFields()
    {
        $model = new PasswordResetRequestForm([
            'email' => $this->testUser->email,
            'reCaptcha' => 'xxxxxxxxxxx'
        ]);
        $model->validate();
        self::checkExpectedValues($model, true, false);
    }
*/
}
