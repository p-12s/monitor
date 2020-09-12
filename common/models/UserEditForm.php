<?php
namespace common\models;

use common\helpers\DateUtil;
use common\helpers\RedisTool;
use common\models\Group;
use common\models\User;
use common\models\UserInGroup;
use Yii;
use yii\base\Model;
use Exception;

class UserEditForm extends Model
{
    public $id;
    public $email;
    public $password;
    public $status;
    public $phone;
    public $groupIds;

    public function rules()
    {
        return 
        [
            [
                [ 'id', 'email', 'password', 'status', 'phone' ], 'trim',
            ],
        ];
    }

    public function attributeLabels()
    {
        return [
            'email' => 'Email',
            'password' => 'Пароль',
            'status' => 'Статус (1 - вкл., 0 - выкл.)',
            'phone' => 'Телефон (обязательно с 8-ки)',
            'groupIds' => 'Группы уведомления',
        ];
    }

    public function save($groupIds = array()){
        $user = User::find()->where(['id' => (int)$this->id])->one();
        if (!$user) {
            throw new \Exception('Пользователь для обновления не найден');
        }
        $user->email = $this->email;
        $user->password = empty($this->password) ? $user->password : Yii::$app->security->generatePasswordHash($this->password);
        $user->status = $this->status;
        $user->phone = $this->phone;
        $user->save();

        foreach ((array)$groupIds as $id) {
            $isUserGroup = UserInGroup::find()
                ->where(['group_id' => (int)$id])
                ->andWhere(['user_id' => (int)$this->id])->exists();
            if (!$isUserGroup) {
                $userInGroup = new UserInGroup();
                $userInGroup->created_at = DateUtil::now()->format('Y-m-d H:i:s');
                $userInGroup->group_id = $id;
                $userInGroup->user_id = (int)$this->id;
                $userInGroup->save();
            }
        }
    }
}
