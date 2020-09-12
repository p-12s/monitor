<?php
namespace backend\controllers;

use Yii;
use yii\web\Controller;
use yii\data\Pagination;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\web\BadRequestHttpException;
use yii\base\InvalidArgumentException;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use Exception;

// время Московское, чтобы начислять по-Москве
date_default_timezone_set('Europe/Moscow');
// локализация
setlocale(LC_ALL, 'ru_RU', 'ru_RU.UTF-8', 'ru', 'russian');
setlocale(LC_TIME, 'ru_RU');

class AdminController extends Controller
{
    public $layout = 'admin';
 
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['index'],
                        'allow' => true,
                        'roles' => ['admin'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['logout',],
                        'roles' => ['@'],
                    ],
                ]
            ]
        ];
    }

    public function beforeAction($action)
    {
        Yii::info($_SERVER['REQUEST_URI'], 'admin_actions');
        return parent::beforeAction($action);
    }

    public function actionIndex()
    {
        return $this->render('index');
    }
}
