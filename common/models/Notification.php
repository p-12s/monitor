<?php
namespace common\models;

use Yii;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;
use common\models\Site;
use common\models\Group;
use Exception;

class Notification extends ActiveRecord
{
    const TYPE_SITE_UNAVAILABLE = 0;
    const TYPE_SITE_OK = 1;

    public static function tableName()
    {
        return '{{%notification}}';
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

    public function getSite()
    {
        $this->hasOne(Site::className(), ['id' => 'site_id'])->one();
    }

    public function getGroup()
    {
        $this->hasOne(Group::className(), ['id' => 'group_id'])->one();
    }
}
