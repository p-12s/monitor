<?php
namespace console\controllers;

use DateTime;
use Yii;
use yii\console\Controller;

use yii\data\Pagination;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\web\BadRequestHttpException;
use yii\base\InvalidArgumentException;
use yii\helpers\Url;
use Codeception\Util\Debug;
use Exception;

// время Московское, чтобы начислять по-Москве
date_default_timezone_set('Europe/Moscow');
// локализация
setlocale(LC_ALL, 'ru_RU', 'ru_RU.UTF-8', 'ru', 'russian');
setlocale(LC_TIME, 'ru_RU');

class RbacController extends Controller
{
    public function actionInit()
    {
        $auth = Yii::$app->authManager;
        //$auth->removeAll();

        // add "docs" permission
        $permission = $auth->createPermission('docsManagement');
        $permission->description = 'User uploaded financial docs management';
        $auth->add($permission);

        // add "updatePost" permission
        /*$updatePost = $auth->createPermission('updatePost');
        $updatePost->description = 'Update post';
        $auth->add($updatePost);*/

        // add role and give this role the permission
        $role = $auth->createRole('accountant');
        $auth->add($role);
        $auth->addChild($role, $permission);

        // add "admin" role and give this role the "updatePost" permission
        // as well as the permissions of the "author" role
        $admin = $auth->getRole('admin');
        $auth->addChild($admin, $role);

        // Assign roles to users. 1 and 2 are IDs returned by IdentityInterface::getId()
        // usually implemented in your User model.
        $auth->assign($role, 2);
    }
}
