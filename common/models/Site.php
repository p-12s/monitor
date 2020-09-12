<?php
namespace common\models;

use Yii;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;
use Exception;
use common\models\SiteInGroup;

class Site extends ActiveRecord
{
    const STATUS_DISABLED = 0;
    const STATUS_ENABLED = 1;

    const RESPONSE_CODE_ERROR = 500;
    const RESPONSE_CODE_OK = 200;

    public $groups;
    public $statusDescription;

    public static function tableName()
    {
        return '{{%site}}';
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

    public function findGroups( $siteId )
    {
        $siteInGroups = SiteInGroup::find()->where(['site_id' => $siteId])->all();
        $groups = array();
        foreach ($siteInGroups as $item) {
            array_push($groups, Group::find()->where(['id' => $item->group_id])->one());
        }
        return $groups;
    }
}
