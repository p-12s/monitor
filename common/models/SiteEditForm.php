<?php
namespace common\models;

use common\helpers\RedisTool;
use common\models\Group;
use common\models\SiteInGroup;
use Yii;
use yii\base\Model;
use Exception;

class SiteEditForm extends Model
{
    public $id;
    public $url;
    public $interval;
    public $status;
    public $groupId;

    public function rules()
    {
        return 
        [
            [
                [ 'id', 'url', 'interval', 'status', 'groupId', ], 'trim',
            ],
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
        $site = Site::find()->where(['id' => (int)$this->id])->one();
        if (!$site) {
            throw new \Exception('Сайт для обновления не найден');
        }
        // запись в Redis
        if (!RedisTool::IsDataUpdatedSuccessfully($site->url, $this->url, $this->interval, $this->id)) {
            throw new \Exception('Возникла ошибка при редактировании сайта в Redis');
        }
        // запись в табл. сайтов
        $site->url = $this->url;
        $site->interval = $this->interval;
        $site->status = $this->status;
        $site->save();
        // группа уведомлений
        $siteInGroup = SiteInGroup::find()->where(['site_id' => $this->id])->one();
        if (!$siteInGroup) {
            $siteInGroup = new SiteInGroup();
            $siteInGroup->site_id = $this->id;
        }
        $siteInGroup->group_id = $this->groupId;
        $siteInGroup->save();
    }
}
