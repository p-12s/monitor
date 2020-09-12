<?php

$adminEmail = 'PROD_SUPPORT_EMAIL';
$supportEmail = 'PROD_ADMIN_EMAIL';
$noReplyEmail = 'PROD_NO_REPLY';
$qa_server_names = array('YOUR_QA_DOMAIN');
if (!array_key_exists( 'SERVER_NAME' , $_SERVER ) || in_array($_SERVER['SERVER_NAME'], $qa_server_names)) {
    $host = 'smtp.gmail.com';
    $username = 'QA_USERNAME';
    $password = 'QA_PROD';
}

return [
    'adminEmail' => $adminEmail,
    'supportEmail' => $supportEmail,
    'noreplyEmail' => $noReplyEmail,
    'user.passwordResetTokenExpire' => 3600,
    'numbersOfFails'  => 5,
    'numbersOfOks'  => 2,
    'historyKeepDays'  => 7,
    'defaultDocSrc'  => '/img/ico-document.jpg',
];
