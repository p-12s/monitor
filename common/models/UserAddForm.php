<?php
namespace common\models;

use common\helpers\DateUtil;
use common\models\User;
use common\models\UserInGroup;
use Yii;
use yii\base\Model;
use common\models\Group;

class UserAddForm extends Model
{
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
                    [ 'email', 'password', 'status', 'phone' ], 'trim',
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
        $user = User::find()->where(['email' => $this->email])->one();
        if ($user) {
            throw new \Exception('Пользователь с таким email уже существует');
        }
        $user = new User();
        $user->email = $this->email;
        $user->password = Yii::$app->security->generatePasswordHash($this->password);
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
                $userInGroup->user_id = $user->getPrimaryKey();
                $userInGroup->save();
            }
        }
    }
}
