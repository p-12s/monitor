<?php
namespace common\models;

use Yii;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;
use Exception;
use common\models\SiteInGroup;

class History extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%history}}';
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
}
