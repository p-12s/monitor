<?php

$host = 'mail.privateemail.com';
$username = 'PROD_USERNAME';
$password = 'PROD_PASS';
$qa_server_names = array('YOUR_QA_DOMAIN');
if (!array_key_exists( 'SERVER_NAME' , $_SERVER ) || in_array($_SERVER['SERVER_NAME'], $qa_server_names)) {
    $host = 'smtp.gmail.com';
    $username = 'QA_USER_NAME';
    $password = 'QA_PASS';
}

return [
    'id' => 'app-console-tests',
    'basePath' => dirname(__DIR__),
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor', // TODO точно?
    'name' => 'Monitor',
    'components' => [
        'user' => [
            'class' => 'yii\web\User',
            'identityClass' => 'common\models\User',
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'security' => [
            'passwordHashCost' => 10, //default is 13
        ],
        'db' => [
            'class' => 'yii\db\Connection',
            'dsn' => 'pgsql:host=localhost;dbname=YOUR_DB_NAME',
            'username' => 'YOUR_USER_NAME',
            'password' => 'YOUR_PASS',
            'charset' => 'utf8',
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            'viewPath' => '@common/mail',
            'useFileTransport' => false,
            'transport' => [
                'class' => 'Swift_SmtpTransport',
                'host' => $host,
                'username' => $username,
                'password' => $password,
                'port' => '587',
                'encryption' => 'tls',
            ],
        ],
    ],
];
