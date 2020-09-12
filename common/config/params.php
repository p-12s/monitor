<?php
// TODO если делать отправку писем - нужен ящик от домена, либо полностью гугловая почта
$adminEmail = 'PROD_ADMIIN_EMAIL';
$supportEmail = 'PROD_SUPPORT_EMAIL';
$noReplyEmail = 'PROD_NOREPLY_EMAIL';
$qa_server_names = array('YOUR_QA_DOMAIN');
if (!array_key_exists( 'SERVER_NAME' , $_SERVER ) || in_array($_SERVER['SERVER_NAME'], $qa_server_names)) {
    $host = 'smtp.gmail.com';
    $username = 'QA_EMAIL';
    $password = 'QA_PASS';
}

return [
    'adminEmail' => $adminEmail,
    'supportEmail' => $supportEmail,
    'noreplyEmail' => $noReplyEmail,
    'numbersOfFails'  => 5,
    'numbersOfOks'  => 2,
    'historyKeepDays'  => 7,
    'user.passwordResetTokenExpire' => 3600,
];
