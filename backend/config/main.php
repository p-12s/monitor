<?php
$params = array_merge(
    require __DIR__ . '/../../common/config/params.php',
    require __DIR__ . '/../../common/config/params-local.php',
    require __DIR__ . '/params.php',
    require __DIR__ . '/params-local.php'
);

return [
    'id' => 'app-backend',
    'basePath' => dirname(__DIR__),
    'controllerNamespace' => 'backend\controllers',
    'bootstrap' => ['log'],
    'language' => 'ru-Ru', // либо просто 'ru'
    'modules' => [],
    'components' => [
        'request' => [
            'csrfParam' => '_csrf-backend',
        ],
        'user' => [
            'identityClass' => 'common\models\User',
            'enableAutoLogin' => false,
            'identityCookie' => ['name' => '_identity-backend', 'httpOnly' => true],
        ],
        'session' => [
            // this is the name of the session cookie used for login on the backend
            'name' => 'advanced-backend',
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                    'maxLogFiles' => 5,
                    'logVars' => []
                ],
                [
                    'class' => 'yii\log\FileTarget',
                    'categories' => ['admin_actions'],
                    'logFile' => '@runtime/logs/admin_actions.log',
                    'maxLogFiles' => 5,
                    'logVars' => ['_GET', '_POST', '_FILES', '_PUT', '_DELETE'] // какие параметры логировать
                ]
            ],
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
                '/' => 'admin/index',
                '<controller>/<action>' => '<controller>/<action>'
            ],
        ],
        'formatter' => [
            'dateFormat' => 'Y-m-d',
            'datetimeFormat' => 'Y-m-d H:i:s',
            'timeFormat' => 'H:i:s',
            'locale' => 'ru-RU',
            'defaultTimeZone' => 'Europe/Moscow',
        ],
        'authManager' => [
            'class' => 'yii\rbac\DbManager',
            //'cache' => 'cache' // с включенным кешированием не работают права из бд
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',  // Подключаем файловое кэширование данных
        ],
    ],
    'params' => $params,
];
