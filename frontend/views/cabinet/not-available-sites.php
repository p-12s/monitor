<?php
    use yii\helpers\Html;
    use yii\bootstrap\ActiveForm;

    $this->title = 'Список недоступных сайтов';
?>

<?php if(Yii::$app->session->hasFlash('success')): ?>
    <div class="alert alert-success alert-dismissible" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
        <?= Yii::$app->session->getFlash('success'); ?>
    </div>
<?php endif; ?>

<?php if(Yii::$app->session->hasFlash('error')): ?>
    <div class="alert alert-danger alert-dismissible" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
        <?= Yii::$app->session->getFlash('error'); ?>
    </div>
<?php endif; ?>

<div class="site-login">
    <h1><?= Html::encode($this->title) ?></h1>
    <p><a href="/cabinet/site-add">Добавить сайт</a></p>
    <div class="row">
        <table class="table">
            <thead>
                <th>№</th>
                <th>Адрес</th>
                <th>Интервал (мин.)</th>
                <th>История</th>
                <th>Управление</th>
            </thead>
            <tbody>
                <?php
                    $i = 1;
                    //echo '<pre>'; print_r($notAvailableSites); echo '<pre>'; exit();
                    foreach($sites as $site):
                ?>
                    <tr>
                        <td><?=$i?></td>
                        <td><a href="<?=$site->url?>" target="_blank"><?=$site->url?></a></td>
                        <td><?=$site->interval?></td>
                        <td><a href="/cabinet/history?site_id=<?=$site->id?>">Перейти</a></td>
                        <td>
                            <a href="/cabinet/site-edit?id=<?=$site->id?>">Редактировать</a>
                        </td>
                    </tr>
                <?php
                    $i++;
                    endforeach;
                ?>
            </tbody>
        </table>
    </div>
</div>
