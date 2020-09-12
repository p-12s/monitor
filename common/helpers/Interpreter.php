<?php

namespace common\helpers;

use Yii;
use yii\base\Model;
use common\models\User;
use common\models\Site;

class Interpreter
{
    public static function statusCodeToUserInfoBag( $statusCode )
    {
        switch ( $statusCode ) {
            case User::STATUS_DEACTIVATED:
                // пользователь не увидит этого статуса, т.к. не сможет залогиниться. Пусть просто будет
                return [
                    'status' => 'Неактивен',
                    'tooltip' => 'Для активации нужно обратиться к администратору'
                ];
            case User::STATUS_ACTIVE:
                return [
                    'status' => 'Активирован',
                    'tooltip' => ''
                ];
            default:
                return [
                    'status' => 'Неизвестен',
                    'tooltip' => 'Неизвестный статус'
                ];
        }
    }

    public static function getSiteStatusDescription( $statusCode )
    {
        switch ( $statusCode ) {
            case Site::STATUS_DISABLED:
                return [
                    'status' => 'Выключен',
                    'icon' => 'error'
                ];
            case Site::STATUS_ENABLED:
                return [
                    'status' => 'Включен',
                    'icon' => 'success'
                ];
            default:
                return [
                    'status' => 'Статус неизвестен',
                    'icon' => 'error'
                ];
        }
    }
}
