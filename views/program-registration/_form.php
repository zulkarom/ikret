<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\ProgramRegistration $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="program-registration-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'user_id')->textInput() ?>

    <?= $form->field($model, 'program_id')->textInput() ?>

    <?= $form->field($model, 'project_name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'nric')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'status')->textInput() ?>

    <?= $form->field($model, 'group_name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'participant_cat_local')->textInput() ?>

    <?= $form->field($model, 'participant_cat_umk')->textInput() ?>

    <?= $form->field($model, 'other_program')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'advisor')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'advisor_dropdown')->textInput() ?>

    <?= $form->field($model, 'institution')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'project_desc')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'competition_type')->textInput() ?>

    <?= $form->field($model, 'booth_number')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'poster_file')->textarea(['rows' => 6]) ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
