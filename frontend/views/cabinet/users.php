<?php
    use yii\helpers\Html;
    use yii\bootstrap\ActiveForm;
    use common\models\Group;
    use common\models\User;

    $this->title = 'Список пользователей';
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
    <p><a href="/cabinet/user-add">Добавить</a></p>
    <div class="row">
        <table class="table">
            <thead>
                <th>№</th>
                <th>Email</th>
                <th>Статус</th>
                <th>Телефон (с 8-ки)</th>
                <th>Редактирование</th>
            </thead>
            <tbody>
                <?php
                    $i = 1;
                    foreach($users as $user):
                        $edit = "/cabinet/user-edit/?id=" . Html::encode($user->id);
                ?>
                    <tr>
                        <td><?=$i?></td>
                        <td><?=$user->email?></td>
                        <td><?=$user->status?></td>
                        <td><?=$user->phone?></td>
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
