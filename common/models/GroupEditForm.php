<?php
namespace common\models;

use common\models\Group;
use Yii;
use yii\base\Model;
use Exception;

class GroupEditForm extends Model
{
    public $id;
    public $name;

    public function rules()
    {
        return 
        [
            [
                [ 'id', 'name', ], 'trim',
            ],
        ];
    }

    public function save(){
        $group = Group::find()->where(['id' => (int)$this->id])->one();
        if (!$group) {
            throw new \Exception('Группа для обновления не найдена');
        }
        $group->name = $this->name;
        $group->save();
    }
}
