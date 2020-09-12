<?php
    use Yii;
    use yii\helpers\Html;
    use app\assets\AppAsset;
    use common\models\User;
    use common\helpers\DateUtil;
    use common\helpers\Helper;
?>

<?php $this->beginPage() ?>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="format-detection" content="telephone=no">
    <meta charset="<?= Yii::$app->charset ?>">
    <?php $this->registerCsrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <script type="text/javascript" src="/js/jquery.min.js"></script>
    <link rel="stylesheet" type="text/css" href="/css/style.css">
    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>
<div class="nav-c">
    <div class="nav-media">
        <div class="nav-icon">Monitor</div>
        <div class="nav-logo d-block"><a href="/">Monitor</a></div>
    </div>
    <div class="nav-b">
        <div class="nav-logo"><a href="/">Monitor</a></div>
        <nav class="nav-menu">
            <a class="nav-link" href="/cabinet/index">Сайты</a>
            <a class="nav-link" href="/cabinet/not-available-sites">Недоступные сайты</a>
            <a class="nav-link" href="/cabinet/groups">Группы</a>
            <a class="nav-link" href="/cabinet/users">Пользователи</a>
        </nav>
        <div class="nav-bottom">
            <div class="nav-name">
                <?php
                    if (!Yii::$app->user->isGuest) {
                        echo Yii::$app->user->identity->email;
                    }
                ?>
            </div>
            <a class="nav-logout" href="/cabinet/logout">Выход</a>
        </div>
    </div>
</div>
<div class="content-c">
    <div class="modal-bg"></div>
    <!-- содержимое -->
    <?= !empty($content) ? $content : '' ?>
    <!-- /содержимое -->
</div>
<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
