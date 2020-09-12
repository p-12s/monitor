<?php
    use yii\helpers\Html;
    use yii\bootstrap\ActiveForm;

    $this->title = 'Редактировать пользователя';
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
    <div class="">
    <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>
        <?= $form->field($model, 'id')
            ->hiddenInput(['value'=> Html::encode($model->id)])
            ->label(false)
        ?>
        <?= $form->field($model, 'email')
            ->textInput(['value' => Html::encode($model->email)])
        ?>
        <?= $form->field($model, 'status')
            ->textInput(['value' => Html::encode($model->status)])
        ?>
        <?= $form->field($model, 'password')
            ->textInput(['value' => ''])
        ?>
        <?= $form->field($model, 'phone')
            ->textInput(['value' => Html::encode($model->phone)])
        ?>
        <?= $form->field($model, 'groupIds[]')
            ->dropDownList($groups, [ 'multiple'=>'multiple' ])
        ?>
        <div class="form-group">
            <?= Html::submitButton('Сохранить', ['class' => 'btn btn-primary']) ?>
        </div>
    <?php ActiveForm::end(); ?>
    </div>
</div>