<?php
// TODO если делать отправку писем - нужен ящик от домена, либо полностью гугловая почта
$host = 'mail.privateemail.com';
$username = 'PROD_EMAIL';
$password = 'PROD_PASS';
$qa_server_names = array('YOUR_QA_DOMAINm');
if (!array_key_exists( 'SERVER_NAME' , $_SERVER ) || in_array($_SERVER['SERVER_NAME'], $qa_server_names)) {
    $host = 'smtp.gmail.com';
    $username = 'QA_EMAIL';
    $password = 'QA_PASS';
}

return [
    'components' => [
        'db' => [
            'class' => 'yii\db\Connection',
            'dsn' => 'pgsql:host=localhost;dbname=YOUR_DB_NAME',
            'username' => 'YOUR_USERNAME',
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
        'redis' => [
            'class' => 'yii\redis\Connection',
            'hostname' => 'localhost',
            'port' => 6379,
            'database' => 0,
        ],
    ],
];
