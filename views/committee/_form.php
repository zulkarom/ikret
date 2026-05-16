<?php

use app\models\Committee;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\Committee $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="committee-form">
    <?php $form = ActiveForm::begin(); ?>

    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'com_name_en')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'com_name')->textInput(['maxlength' => true]) ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4">
            <?= $form->field($model, 'committee_order')->textInput(['type' => 'number']) ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4">
            <?= $form->field($model, 'is_jawatankuasa')->dropDownList(Committee::yesNoOptions()) ?>
        </div>
        <div class="col-md-4">
            <?= $form->field($model, 'is_student')->dropDownList(Committee::yesNoOptions()) ?>
        </div>
        <div class="col-md-4">
            <?= $form->field($model, 'is_pengarah')->dropDownList(Committee::yesNoOptions()) ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4">
            <?= $form->field($model, 'can_approve')->dropDownList(Committee::yesNoOptions()) ?>
        </div>
        <div class="col-md-4">
            <?= $form->field($model, 'cert_only')->dropDownList(Committee::yesNoOptions()) ?>
        </div>
    </div>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
        <?= Html::a('Cancel', ['index'], ['class' => 'btn btn-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>
