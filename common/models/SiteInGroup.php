<?php
namespace common\models;

use Yii;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;
use Exception;
use common\models\Site;
use common\models\Group;

class SiteInGroup extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%site_in_group}}';
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

    public function getSite()
    {
        $this->hasOne(Site::className(), ['id' => 'site_id'])->one();
    }
}
