<?php
namespace common\models;

use Yii;
use yii\base\Model;
use common\models\Site;
use common\models\SiteInGroup;
use common\helpers\RedisTool;

class SiteAddForm extends Model
{
    public $url;
    public $interval;
    public $status;
    public $groupId;

    public function rules()
    {
        return
            [
                [ [ 'url', 'groupId' ], 'trim' ],
                ['status', 'default', 'value' => Site::STATUS_DISABLED],
                ['status', 'in', 'range' =>
                    [
                        Site::STATUS_DISABLED,
                        Site::STATUS_ENABLED
                    ]
                ],
                [['interval', 'groupId'], 'number'],
            ];
    }

    public function attributeLabels()
    {
        return [
            'url' => 'Полный адрес (https://site.ru/ru/some)',
            'interval' => 'Интервал мониторинга (мин)',
            'status' => 'Статус',
            'groupId' => 'Группа уведомления',
        ];
    }

    public function save(){
        $model = new Site();
        $model->created_at = date('Y-m-d H:i:s');
        $model->url = $this->url;
        $model->interval = $this->interval;
        $model->status = $this->status;
        $model->save();
        // запись в Redis
        if (!RedisTool::IsDataCreatedSuccessfully($this->url, $this->interval, $model->getPrimaryKey())) {
            throw new \Exception('Возникла ошибка при добавлении нового сайта в Redis');
        }
        // группа уведомлений
        $siteInGroup = SiteInGroup::find()->where(['site_id' => $model->getPrimaryKey()])->one();
        if (!$siteInGroup) {
            $siteInGroup = new SiteInGroup();
            $siteInGroup->site_id = $model->getPrimaryKey();
        }
        $siteInGroup->group_id = $this->groupId;
        $siteInGroup->save();
    }
}
