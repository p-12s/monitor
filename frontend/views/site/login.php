<?php
    use yii\helpers\Html;
    use yii\bootstrap\ActiveForm;

    $this->title = 'Вход';
?>

<?php if(Yii::$app->session->hasFlash('success')): ?>
    <div class="alert-c alert-success">
        <?= Yii::$app->session->getFlash('success'); ?>
    </div>
<?php endif; ?>

<?php if(Yii::$app->session->hasFlash('error')): ?>
    <div class="alert-c alert-dangers">
        <?= Yii::$app->session->getFlash('error'); ?>
    </div>
<?php endif; ?>

<h1><?= Html::encode($this->title) ?></h1>
<?php $form = ActiveForm::begin(['id' => 'login-form']); ?>
    <?= $form->field($model, 'username')->textInput(['autofocus' => true])->label('Ваш E-mail*') ?>
    <?= $form->field($model, 'password')->passwordInput(['id' => 'password-input'])->label('Пароль*') ?>
    <label class="show-pass"><input type="checkbox" id="show-password"> Показать пароль</label>
    <?= $form->field($model, 'reCaptcha')->widget(
        \himiklab\yii2\recaptcha\ReCaptcha2::className(),
        ['siteKey' => 'RECAPTHCA_KEY'])->label('') ?>
    <div class="login-rpass">
        <?= Html::a('Забыли пароль?', ['site/request-password-reset']) ?>
    </div>
    <div class="form-group">
        <?= Html::submitButton('Вход', ['class' => 'btn-login', 'name' => 'login-button']) ?>
    </div>
<?php ActiveForm::end(); ?>
<script>
    $('#show-password').on('click', function(){
        if ($(this).is(':checked')){
            $('#password-input').attr('type', 'text');
        } else {
            $('#password-input').attr('type', 'password');
        }
    });
</script>
