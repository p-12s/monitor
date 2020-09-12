<?php

namespace common\helpers;

use Yii;
use yii\base\Model;
use Exception;

// использование отдбельного класса Sender не работало (возможно я криво назначил файл исполняемым), поэтому метод sendSms($phone, $text) продублировал в контроллер
class Sender
{
    const GREENSMS_API = 'http://api3.greensms.ru/sms/send';
    const GREENSMS_USER = 'webtraktor';
    const GREENSMS_PASS = 'enigma666';
    const GREENSMS_FROM = 'GREENSMS';

    public static function sendSms($phone, $text)
    {
        echo "\nОТПРАВКА СМС на номер $phone `$text`";

        $array = array(
            'user'   => self::GREENSMS_USER,
            'pass' => self::GREENSMS_PASS,
            'to' => $phone,
            'txt' => $text,
            'from' => self::GREENSMS_FROM
        );

        $ch = curl_init(self::GREENSMS_API);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $array);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($array, '', '&'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HEADER, false);
        $html = curl_exec($ch);
        curl_close($ch);

        // TODO писать в лог отправленных уведомл.?
        return $html; // {"request_id":"072e125f-1335-4872-97e0-009efbf50de3"}
    }

    /*public static function sendEmail(User $user, $subjectTail)
    {
        $sent = Yii::$app->mailer
            ->compose(
                ['html' => 'user-uploaded-document-html', 'text' => 'user-uploaded-document-text'],
                ['user' => $user] )
            ->setTo( Yii::$app->params['supportEmail'] )
            ->setFrom( Yii::$app->params['noreplyEmail'] )
            ->setSubject( Yii::$app->name . ' - ' . $subjectTail )
            ->send();
        if (!$sent) {
            throw new \RuntimeException('Sending error.');
            // TODO сохранить в лог
        }
    }*/
}
