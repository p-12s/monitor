<?php
namespace common\fixtures;

use yii\test\ActiveFixture;

class CashTransactionFixture extends ActiveFixture
{
    public $modelClass = 'common\models\CashTransaction';
    public $depends = ['common\fixtures\UserFixture'];
}