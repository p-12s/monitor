<?php
$params = array_merge(
    require __DIR__ . '/../../common/config/params.php',
    require __DIR__ . '/../../common/config/params-local.php',
    require __DIR__ . '/params.php',
    require __DIR__ . '/params-local.php'
);

return [
    'id' => 'app-console',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'controllerNamespace' => 'console\controllers',
    /*'controllerMap' => [
        'fixture' => [
            'class' => 'yii\console\controllers\FixtureController',
            'namespace' => 'common\fixtures',
          ],
    ],*/
    'components' => [
        'user' => [ // TODO добавил директиву
            'class' => 'yii\web\User',
            'identityClass' => 'common\models\User',
            'enableAutoLogin' => true,
            //'identityCookie' => ['name' => '_identity-frontend', 'httpOnly' => true],
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
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
        'redis' => [
            'class' => 'yii\redis\Connection',
            'hostname' => 'localhost',
            'port' => 6379,
            'database' => 0,
        ],
    ],
    'params' => $params,
];
