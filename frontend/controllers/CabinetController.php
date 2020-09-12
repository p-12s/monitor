<?php
namespace frontend\controllers;

use Yii;
use yii\base\InvalidArgumentException;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\web\UploadedFile;
use yii\data\Pagination;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use common\models\User;
use common\models\Site;
use common\models\Group;
use common\models\SiteInGroup;
use Exception;
use common\models\GroupEditForm;
use common\models\GroupAddForm;
use common\models\SiteEditForm;
use common\models\SiteAddForm;
use common\models\UserEditForm;
use common\models\UserAddForm;
use common\models\History;
use common\models\NotAvailableSite;
use common\helpers\Interpreter;
use common\helpers\Helper;
use common\helpers\RedisTool;

// время Московское, чтобы начислять по-Москве
date_default_timezone_set('Europe/Moscow');
// локализация
setlocale(LC_ALL, 'ru_RU', 'ru_RU.UTF-8', 'ru', 'russian');
setlocale(LC_TIME, 'ru_RU');

class CabinetController extends Controller
{
    public  $layout = 'cabinet';
    private $user;

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    public function beforeAction($action)
    {
        if(!Yii::$app->user->isGuest){
            $id   = Yii::$app->user->identity->id;
            $this->user = User::find()->where(['id' => $id])->one();
            return parent::beforeAction($action);
        }
        return parent::beforeAction($action);
    }

    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    public function actionNotAvailableSites()
    {
        $notAvailableSiteIds = Helper::convertNestedArr(
            NotAvailableSite::find()->select('site_id')->asArray()->all(),
            'site_id');
        $sites = Site::find()->where(['in', 'id', $notAvailableSiteIds])->all();

        return $this->render('not-available-sites', [
            'sites' => $sites,
        ]);
    }

    public function actionIndex()
    {
        $sites = Site::find()->all();

        foreach ($sites as $site) {
            $site->groups = Site::findGroups($site->id);
            $site->statusDescription = Interpreter::getSiteStatusDescription($site->status);
        }

        return $this->render('index', [
            'sites' => $sites,
        ]);
    }

    public function actionGroups()
    {
        $query = Group::find();
        $pagination = new Pagination([
            'defaultPageSize' => 10,
            'totalCount' => $query->count(),
        ]);

        $groups = $query->orderBy( 'created_at DESC' )
            ->offset( $pagination->offset )
            ->limit( $pagination->limit )
            ->all();

        return $this->render( 'groups', [
            'groups'       => $groups,
            'pagination' => $pagination,
        ]);
    }

    public function actionSiteAdd()
    {
        $model = new SiteAddForm();
        if ( $model->load(Yii::$app->request->post()) && $model->validate()) {
            $model->save();

            $successMessage = 'Это успех!';
            Yii::$app->session->setFlash('success', $successMessage);
            return $this->actionIndex();
        }
        $groups = Group::find()->select(['id', 'name'])->asArray()->all();
        $groups = ArrayHelper::map($groups,'id','name');

        return $this->render('site-add',[
            'model' => $model,
            'groups' => $groups
        ]);
    }

    public function actionSiteEdit()
    {
        $id = htmlspecialchars(trim(Yii::$app->request->get('id')));
        $site = Site::find()->where(['id' => $id])->one();
        $includedGroup = Site::findGroups($site->id);

        $model = new SiteEditForm();
        if ( $model->load(Yii::$app->request->post()) && $model->validate()) {
            $model->save();

            $successMessage = 'Это успех!';
            Yii::$app->session->setFlash('success', $successMessage);
            return $this->actionIndex();
        }
        $model->id = $site->id;
        $model->url = $site->url;
        $model->interval = $site->interval;
        $model->status = $site->status;

        $groups = Group::find()->select(['id', 'name'])->asArray()->all();
        $groups = ArrayHelper::map($groups,'id','name');

        return $this->render('site-edit', [
            'model' => $model,
            'groups' => $groups
        ]);
    }

    public function actionSiteDelete()
    {
        $id = htmlspecialchars(trim(Yii::$app->request->get('id')));

        $site = Site::find()->where(['id' => (int)$id])->one();
        if (!$site) {
            throw new \Exception('Сайт для удаления не найден');
        }
        if (RedisTool::IsDataDeleteSuccessfully($site->url)) {
            NotAvailableSite::deleteAll(['site_id' => $site->id]);
            Site::deleteAll(['id' => $site->id]);
            $successMessage = 'Это успех!';
            Yii::$app->session->setFlash('success', $successMessage);
        }
        return $this->actionIndex();
    }

    public function actionGroupAdd()
    {
        $model = new GroupAddForm();
        if ( $model->load(Yii::$app->request->post()) && $model->validate()) {
            $model->save();

            $successMessage = 'Это успех!';
            Yii::$app->session->setFlash('success', $successMessage);
            return $this->actionGroups();
        }
        return $this->render('group-add',[
            'model' => $model
        ]);
    }

    public function actionGroupEdit()
    {
        $id = htmlspecialchars(trim(Yii::$app->request->get('id')));
        $group = Group::find()->where(['id' => $id])->one();

        $model = new GroupEditForm();
        if ( $model->load(Yii::$app->request->post()) && $model->validate()) {
            $model->save();

            $successMessage = 'Это успех!';
            Yii::$app->session->setFlash('success', $successMessage);
            return $this->actionGroups();
        }
        $model->id = $group->id;
        $model->name = $group->name;

        return $this->render('group-edit', [
            'model' => $model
        ]);
    }

    public function actionGroupDelete()
    {
        $id = htmlspecialchars(trim(Yii::$app->request->get('id')));
        $group = Group::find()->where(['id' => $id])->one();
        $group->delete();
        return $this->actionGroups();
    }

    public function actionHistory()
    {
        $siteId = htmlspecialchars(trim(Yii::$app->request->get('site_id')));
        $siteId = trim($siteId);

        $query = History::find()->where(['site_id' => $siteId]);
        $pagination = new Pagination([
            'defaultPageSize' => 20,
            'totalCount' => $query->count(),
        ]);

        $history = $query->orderBy( 'created_at DESC' )
            ->offset( $pagination->offset )
            ->limit( $pagination->limit )
            ->all();

        return $this->render( 'history', [
            'history'       => $history,
            'pagination' => $pagination,
        ]);
    }

    public function actionUsers()
    {
        $query = User::find();
        $pagination = new Pagination([
            'defaultPageSize' => 10,
            'totalCount' => $query->count(),
        ]);

        $users = $query->orderBy( 'created_at DESC' )
            ->offset( $pagination->offset )
            ->limit( $pagination->limit )
            ->all();

        return $this->render( 'users', [
            'users'       => $users,
            'pagination' => $pagination,
        ]);
    }

    public function actionUserAdd()
    {
        $model = new UserAddForm();
        if ( $model->load(Yii::$app->request->post()) && $model->validate()) {
            $groupIds = array();
            if (array_key_exists('UserEditForm', $_POST)
                && array_key_exists('groupIds', $_POST['UserEditForm'])
                && array_key_exists('groupIds', $_POST['UserEditForm'])) {
                $groupIds = $_POST['UserEditForm']['groupIds'];
            }
            $model->save($groupIds);

            $successMessage = 'Это успех!';
            Yii::$app->session->setFlash('success', $successMessage);
            return $this->actionUsers();
        }

        $groups = ArrayHelper::map(Group::find()->select('id, name')->all(), 'id', 'name');

        return $this->render('user-add',[
            'model' => $model,
            'groups' => $groups
        ]);
    }

    public function actionUserEdit()
    {
        $id = htmlspecialchars(trim(Yii::$app->request->get('id')));
        $user = User::find()->where(['id' => $id])->one();

        $model = new UserEditForm();
        if ( $model->load(Yii::$app->request->post()) && $model->validate()) {
            $groupIds = array();
            if (array_key_exists('UserEditForm', $_POST)
                && array_key_exists('groupIds', $_POST['UserEditForm'])
                && array_key_exists('groupIds', $_POST['UserEditForm'])) {
                $groupIds = $_POST['UserEditForm']['groupIds'];
            }
            $model->save($groupIds);

            $successMessage = 'Это успех!';
            Yii::$app->session->setFlash('success', $successMessage);
            return $this->actionUsers();
        }
        $model->id = $user->id;
        $model->email = $user->email;
        $model->status = $user->status;
        $model->phone = $user->phone;

        $groups = ArrayHelper::map(Group::find()->select('id, name')->all(), 'id', 'name');

        return $this->render('user-edit', [
            'model' => $model,
            'groups' => $groups
        ]);
    }

    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        }

        $model->password = '';
        return $this->render('login', [
            'model' => $model,
        ]);
    }

    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }
}
