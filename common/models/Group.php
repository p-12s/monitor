<?php
namespace common\models;

use Yii;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;
use Exception;

class Group extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%group}}';
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

    public function getUsersInGroup($groupId)
    {
        $userInGroup = UserInGroup::find()->where(['group_id' => $groupId])->all();
        $users = array();
        foreach ($userInGroup as $item) {
            array_push($users, $item->user);
        }
        return $users;
    }
}
