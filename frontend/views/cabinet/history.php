<?php
    use yii\helpers\Html;
    use yii\bootstrap\ActiveForm;
    use yii\helpers\Url;
    use yii\widgets\LinkPager;

    $this->title = 'История';
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
    <p><a href="/cabinet/index">К списку сайтов</a></p>
    <div class="row">
        <table class="table">
            <thead>
                <th>№</th>
                <th>Дата</th>
                <th>Код ответа</th>
            </thead>
            <tbody>
                <?php
                    $i = 1;
                    foreach($history as $item):
                ?>
                    <tr>
                        <td><?=$i?></td>
                        <td><?=$item->created_at?></td>
                        <td><?=$item->code?></td>
                    </tr>
                <?php
                    $i++;
                    endforeach ?>
            </tbody>
        </table>
        <?= LinkPager::widget(['pagination' => $pagination, 'maxButtonCount' => 20 ]) ?>
    </div>
</div>
<script>
    setTimeout(function(){
        window.location.reload(1);
    }, 60000);
</script>