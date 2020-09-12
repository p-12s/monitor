<?php

namespace common\helpers;

use Yii;
use common\models\User;
use common\models\Site;
use yii\base\Model;
use Exception;

class Helper
{
    public static function createUniqueUserPersonalNumber()
    {
        $randomNumber = 0;
        $user = new User();

        do {
            // создали
            $unixTime = microtime(true);
            $randomNumber = mt_rand(100000000, $unixTime);

            // проверили, нет ли такого персонального номера в БД
            $user->isThatPersonalNumberAlreadyExists($randomNumber);

        } while ($user->isThatPersonalNumberAlreadyExists($randomNumber));

        return $randomNumber;
    }

    public static function sendEmail(User $user, $subjectTail)
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
    }

    public static function selectFile($images, $type)
    {
        if (!$images || count($images) == 0)
            return null;

        $image = null;
        foreach ($images as $item) {
            if ( $item->type === $type ) {
                $image =  $item;
                break;
            }
        }
        return $image;
    }

    public static function findItemInArrayByDate($arr, $date)
    {
        $result = null;
        foreach ($arr as $item) {
            $dateFromDb = '';
            try {
                $dateFromDb = $item['date'];
            } catch (Exception $e) {}
            if (empty($dateFromDb)) {
                try {
                    $dateFromDb = $item['estimated_date'];
                } catch (Exception $e) {}
            }
            if (empty($dateFromDb)) {
                throw new Exception('Не найден ключ для поиска даты');
            }

            if (date_create( date($dateFromDb)) == date_create( date($date))) {
                $result = $item;
                break;
            }
        }
        return $result;
    }

    public static function getBase64Image($uploadedFile)
    {
        $imgSrc = '';
        if (!$uploadedFile) {
            return $imgSrc;
        }
        $path = $uploadedFile['path'];

        if ( !empty($path) && file_exists($path) && @getimagesize($path) ){
            $imageBase64Data = base64_encode(file_get_contents($path));
            $imageMimeContentType = mime_content_type($path);
            if($imageMimeContentType != 'image/jpeg' && $imageMimeContentType != 'image/jpg'
                && $imageMimeContentType != 'image/png' && $imageMimeContentType != 'application/pdf') {
                $imageMimeContentType = '';
            }
            if ( !empty($imageBase64Data) && !empty($imageMimeContentType) ) {
                $imgSrc = "data:$imageMimeContentType;base64,$imageBase64Data";
            }
        }
        return $imgSrc;
    }

    public static function numberFormat($number)
    {
        return number_format($number, 2, '.', ' ');
    }

    public static function numberFormatReverse($str)
    {
        // валидация, д.б. не больше 1 разделителя
        if (mb_substr_count($str, '.') > 1 || mb_substr_count($str, ',') > 1) {
            throw new Exception('Неверно введено число - разделителей (, или .) больше 1');
        }
        // убираем пробел
        $str = str_replace(' ', '', $str);
        return round((float)$str, 2);
    }

    public static function convertNestedArr($arr, $keyName, $needRemoveDuplicates = false)
    {
        $result = [];
        foreach ($arr as $item) {
            $value = $item[$keyName];
            if (!$needRemoveDuplicates || !in_array($value, $result)) {
                array_push($result, $value);
            }
        }
        return $result;
    }

    public static function convertToAssocArr($arr, $keyName, $dataName)
    {
        $result = array();
        foreach ($arr as $item) {
            if (array_key_exists($item[$keyName], $result)) {
                array_push($result[$item[$keyName]], $item[$dataName]);
            } else {
                $result[$item[$keyName]] = array($item[$dataName]);
            }
        }
        return $result;
    }

    public static function isAllCodesDesired($arr, $desiredResponseCode)
    {
        $expectationConfirmed = true;
        if ($desiredResponseCode == Site::RESPONSE_CODE_ERROR) {
            foreach ($arr as $item) {
                if ($item >= 200 && $item <= 299) {
                    $expectationConfirmed = false;
                    break;
                }
            }
        } else if($desiredResponseCode == Site::RESPONSE_CODE_OK) {
            foreach ($arr as $item) {
                if ($item < 200 || $item >= 300) {
                    $expectationConfirmed = false;
                    break;
                }
            }
        } else {
            throw new \Exception('Неизвестный код ответа для обработки: '. $desiredResponseCode);
        }
        return $expectationConfirmed;
    }
}
