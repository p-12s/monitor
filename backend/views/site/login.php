<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = 'Вход';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="container text-center">
    <h1><?= Html::encode($this->title) ?></h1>
    <div class="container text-center adm-login">
        <?php $form = ActiveForm::begin(['id' => 'login-form']); ?>
            <?= $form->field($model, 'username')->textInput(['autofocus' => true])->label('Логин') ?>
            <?= $form->field($model, 'password')->passwordInput()->label('Пароль') ?>
            <?= $form->field($model, 'rememberMe')->checkbox()->hiddenInput()->label('')?>
            <?= $form->field($model, 'reCaptcha')->widget(
                \himiklab\yii2\recaptcha\ReCaptcha2::className(),
                ['siteKey' => '6Ld_y7wUAAAAAIzN6dcKht8tNiQWdaI7S7KtZ0uE'])->label('') ?>
            <div class="form-group">
                <?= Html::submitButton('Войти', ['class' => 'btn btn-primary', 'name' => 'login-button']) ?>
            </div>
        <?php ActiveForm::end(); ?>
    </div>
</div>
