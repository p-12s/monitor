<?php
namespace common\models;

use Yii;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;
use common\models\Site;
use Exception;

class NotAvailableSite extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%not_available_site}}';
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
