<?php
    use yii\helpers\Html;
    use yii\bootstrap\ActiveForm;
    use common\models\Group;

    $this->title = 'Список групп';
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
    <p><a href="/cabinet/group-add">Добавить</a></p>
    <div class="row">
        <table class="table">
            <thead>
                <th>№</th>
                <th>Група</th>
                <th>Редактирование</th>
            </thead>
            <tbody>
                <?php
                    $i = 1;
                    foreach($groups as $group):
                        $edit = "/cabinet/group-edit/?id=" . Html::encode($group->id);
                        $delete = "/cabinet/group-delete/?id=" . Html::encode($group->id);
                ?>
                    <tr>
                        <td><?=$i?></td>
                        <td><?=$group->name?></td>
                        <td>
                            <a href="<?=$edit?>">Редактировать</a>
                        </td>
                    </tr>
                <?php
                    $i++;
                    endforeach ?>
            </tbody>
        </table>
    </div>
</div>
