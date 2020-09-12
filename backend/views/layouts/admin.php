<?php
    use yii\helpers\Html;
    use backend\assets\AppAsset;
    use yii\filters\AccessControl;

    use common\models\InitiationFee;
    use common\models\FirstMembershipFee;
    use common\models\MembershipFee;
    use common\models\VoluntaryContribution;
    use common\models\StartFee;
    use common\models\MortgagePayment;
    use common\models\BalanceWithdrawal;
    use common\helpers\EventStatus;

    AppAsset::register($this);
?>

<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <meta charset="<?= Yii::$app->charset ?>">
        <?php $this->registerCsrfMetaTags() ?>
        <title><?= Html::encode($this->title) ?></title>
        <?php $this->head() ?>
    </head>
    <body>
        <?php $this->beginBody() ?>
        <header>
            <?php
                // кол-во загруженных документов, требующих внимания
                $newDocsCount = InitiationFee::find()->where( [ 'status' => EventStatus::ON_CHECK ] )->count();
                $newDocsCount += FirstMembershipFee::find()->where( [ 'status' => EventStatus::ON_CHECK ] )->count();
                $newDocsCount += MembershipFee::find()->where( [ 'status' => EventStatus::ON_CHECK ] )->count();
                $newDocsCount += VoluntaryContribution::find()->where( [ 'status' => EventStatus::ON_CHECK ] )->count();
                $newDocsCount += StartFee::find()->where( [ 'status' => EventStatus::ON_CHECK ] )->count();
                $newDocsCount += MortgagePayment::find()->where( [ 'status' => EventStatus::ON_CHECK ] )->count();
                // запросы на вывод средств
                $newOutputCount = BalanceWithdrawal::find()->where( [ 'status' => EventStatus::ON_CHECK ] )->count();
                // должники
                $debtorsCount = 0;
            ?>
            <!-- Верхнее меню -->
            <nav class="navbar navbar-default">
                <div class="container-fluid">
                    <div class="navbar-header">
                        <a class="navbar-brand" href="/">
                            <img alt="Brand" src="/img/logo_caishen.png" class='adm-menu-logo'>
                        </a>
                        <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
                            <span class="sr-only">Toggle navigation</span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                        </button>
                        <a class="navbar-brand" href="/">Monitor АДМИН</a>
                    </div>
                    <!-- Collect the nav links, forms, and other content for toggling -->
                    <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                        <ul class="nav navbar-nav">
                            <?php if (Yii::$app->user->can('admin') || Yii::$app->user->can('author')): ?>
                            <li class="dropdown">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true"
                                   aria-expanded="false">Контент<span class="caret"></span></a>
                                <ul class="dropdown-menu">
                                    <li><a href="/admin/news">Новости</a></li>
                                </ul>
                            </li>
                            <?php endif; ?>
                            <?php if (Yii::$app->user->can('admin')): ?>
                                <li class="dropdown">
                                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true"
                                       aria-expanded="false">Аккаунты<span class="caret"></span></a>
                                    <ul class="dropdown-menu">
                                        <li><a href="/admin/investors">Активные</a></li>
                                        <li><a href="/admin/unconfirmed" >Неподтверждённые</a></li>
                                        <li><a href="/admin/blocked">Деактивированные</a></li>
                                    </ul>
                                </li>
                            <?php endif; ?>
                            <?php if (Yii::$app->user->can('admin')): ?>
                                <li class="dropdown">
                                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true"
                                       aria-expanded="false">Управление<span class="caret"></span></a>
                                    <ul class="dropdown-menu">
                                        <li><a href="/admin/all-users">Пользователями</a></li>
                                        <li><a href="/admin/docs">Платежками, требующими внимания
                                            <?= ($newDocsCount > 0) ? ' (+'.$newDocsCount.')' : '' ?>
                                            </a></li>
                                        <li><a href="/admin/line">Очередью</a></li>
                                        <li><a href="/admin/output">Запросами на вывод стредств
                                            <?= ($newOutputCount > 0) ? ' (+'.$newOutputCount.')' : '' ?>
                                            </a></li>
                                        <li><a href="/admin/debtors">Должниками
                                                <?= ($debtorsCount > 0) ? ' (+'.$debtorsCount.')' : '' ?>
                                            </a></li>
                                    </ul>
                                </li>
                            <?php elseif (Yii::$app->user->can('accountant')): ?>
                                <li class="dropdown">
                                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true"
                                       aria-expanded="false">Управление<span class="caret"></span></a>
                                    <ul class="dropdown-menu">
                                        <li><a href="/admin/docs">Платежками, требующими внимания
                                                <?= ($newDocsCount > 0) ? ' (+'.$newDocsCount.')' : '' ?>
                                            </a></li>
                                        <li><a href="/admin/output">Запросами на вывод стредств
                                                <?= ($newOutputCount > 0) ? ' (+'.$newOutputCount.')' : '' ?>
                                            </a></li>
                                    </ul>
                                </li>
                            <?php endif; ?>
                            <?php if (Yii::$app->user->can('admin')): ?>
                                <li class="dropdown">
                                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true"
                                       aria-expanded="false">Настройки<span class="caret"></span></a>
                                    <ul class="dropdown-menu">
                                        <li><a href="/settings/index">Вознаграждение</a></li>
                                    </ul>
                                </li>
                            <?php endif; ?>
                            <li class="dropdown">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true"
                                   aria-expanded="false">
                                    <?=Yii::$app->user->identity['email']?> <?=Yii::$app->user->identity['last_name']?>
                                    <span class="caret"></span></a>
                                <ul class="dropdown-menu">
                                    <li><a href="/site/logout">Выход</a></li>
                                </ul>
                            </li>
                        </ul>
                    </div><!-- /.navbar-collapse -->
                </div><!-- /.container-fluid -->
            </nav>
            <!-- /Верхнее меню -->
        </header>
        <!-- содержимое -->
        <?= $content ?>
        <!-- /содержимое -->
        <footer></footer>
        <?php $this->endBody() ?>
    </body>
</html>
<?php $this->endPage() ?>
