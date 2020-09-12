<?php
    use yii\helpers\Html;
    use yii\bootstrap\ActiveForm;
    use mihaildev\ckeditor\CKEditor;

    $this->title = 'Добавить сайт';
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

<div class="">
    <h1><?= Html::encode($this->title) ?></h1>
    <p><a href="/cabinet/index">К списку сайтов</a></p>

    <?php $form = ActiveForm::begin(); ?>
        <?= $form->field($model, 'url')
            ->textInput(['class' => 'input-long']) ?>
        <?= $form->field($model, 'interval') ?>
        <?= $form->field($model, 'status') ?>
        <?= $form->field($model, 'groupId')
            ->dropDownList($groups, ['value' => $model->groupId])
        ?>
        <div class="form-group">
            <?= Html::submitButton('Сохранить', ['class' => 'btn btn-primary']) ?>
        </div>
    <?php ActiveForm::end(); ?>
</div>
