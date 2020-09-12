<?php
namespace common\models;

use Yii;
use yii\base\Model;
use common\models\Group;

class GroupAddForm extends Model
{
    public $name;

    public function rules()
    {
        return
            [
                [ [ 'name' ], 'trim' ]
            ];
    }

    public function attributeLabels()
    {
        return [
            'name' => 'Название',
        ];
    }

    public function save(){
        $model = new Group();
        $model->name = $this->name;
        $model->save();
    }
}
