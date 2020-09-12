<?php
namespace common\models;

use Yii;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;
use Exception;

class UserInGroup extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%user_in_group}}';
    }

    public function behaviors()
    {
        return [
             [
                 'class' => TimestampBehavior::className(),
                 'createdAtAttribute' => 'created_at',
                 'updatedAtAttribute' => 'updated_at',
                 'value' => date( 'Y-m-d H:i:s' ),
             ],
        ];
    }

    public function rules()
    {
        return [];
    }

    public function getGroup()
    {
        $this->hasOne(Group::className(), ['id' => 'group_id'])->one();
    }

    public function getUser()
    {
        $this->hasOne(User::className(), ['id' => 'user_id'])->one();
    }
}
