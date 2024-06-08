<?php

use yii\bootstrap5\ActiveForm;
use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\ProgramRegistration $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="program-registration-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'fullname')->textInput() ?>

    <?= $form->field($model, 'email')->textInput() ?>

    <?= $form->field($model, 'phone')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'is_internal')->dropDownList($model->listIsInternal())->label('Category (Internal)')?>
    <?= $form->field($model, 'is_student')->dropDownList($model->listIsStudent())->label('Category (Student)')?>

    <?= $form->field($model, 'matric')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'institution')->textInput() ?>
    <br />
Fill in password to change, otherwise <b>leave blank</b>
    <?= $form->field($model, 'passwordRaw')->passwordInput(['maxlength' => true]) ?>
    <br />

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
