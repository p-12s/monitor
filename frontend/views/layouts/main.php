<?php
    use yii\helpers\Html;
    use yii\bootstrap\Nav;
    use yii\bootstrap\NavBar;
    use yii\widgets\Breadcrumbs;
    use frontend\assets\AppAsset;
    use common\widgets\Alert;

    AppAsset::register($this);
?>

<?php $this->beginPage() ?>

<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
    <head>
        <meta charset="<?= Yii::$app->charset ?>">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="/css/style.css">
        <script src="/js/jquery.min.js"></script>
        <?php $this->registerCsrfMetaTags() ?>
        <title><?= Html::encode($this->title) ?></title>
    </head>
    <body class="flex-column" style="background-color: #F4F6F7;">
        <?php $this->beginBody() ?>
        <div class="nav-m-c">
            <div class="nav-m-logo">
                <a href="<?= ($_SERVER['REQUEST_URI'] == '/site/login') ? '//monitor.pageuppers.com' : '/'?>">Monitor</a>
            </div>
        </div>
        <div class="container-m">
            <?= $content ?>
        </div>
        <?php $this->endBody() ?>
    </body>
</html>
<?php $this->endPage() ?>
