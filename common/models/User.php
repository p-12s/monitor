<?php
namespace common\models;

use Yii;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;
use yii\web\IdentityInterface;
use Exception;

class User extends ActiveRecord implements IdentityInterface
{
    const STATUS_DEACTIVATED = 0;
    const STATUS_ACTIVE = 1;

    public static function tableName()
    {
        return '{{%user}}';
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
        return [
            ['status', 'default', 'value' => self::STATUS_DEACTIVATED],
            ['status', 'in', 'range' =>
                [
                    self::STATUS_DEACTIVATED,
                    self::STATUS_ACTIVE,
                ]
            ],
        ];
    }

    public function getUserInGroup()
    {
        return $this->hasMany(UserInGroup::className(), ['user_id' => 'id']);
    }

    public static function findIdentity($id)
    {
        $sql= 'SELECT * FROM public."user" WHERE id = :id AND status != :status';
        return static::findBySql($sql,
            [
                ':id' => $id,
                ':status' => self::STATUS_DEACTIVATED
            ])->limit(1)->one();
    }

    public static function findIdentityByAccessToken($token, $type = null)
    { // метод должен быть, реализовывать его не обязательно
        throw new NotSupportedException('"findIdentityByAccessToken" is not implemented.');
    }

    public static function findByUsername($username)
    {
        return static::findOne(['email' => $username]);
    }

    public function getId()
    {
        return $this->getPrimaryKey();
    }

    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    public function getAuthKey()
    {
        return $this->auth_key;
    }

    public function validatePassword($password)
    {
        return Yii::$app->getSecurity()->validatePassword($password, $this->password);
    }

    public static function findIfExistAndNotDeactivated($email)
    {
        $sql='SELECT * FROM public."user" WHERE (LOWER(email)=LOWER(:email)) AND status != :status';
        return static::findBySql($sql,
            [
                ':email' => $email,
                ':status' => self::STATUS_DEACTIVATED
            ])
            ->limit(1)
            ->one();
    }
}
