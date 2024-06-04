<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\ProgramRegistrationSearch $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="program-registration-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'user_id') ?>

    <?= $form->field($model, 'program_id') ?>

    <?= $form->field($model, 'project_name') ?>

    <?= $form->field($model, 'nric') ?>

    <?php // echo $form->field($model, 'status') ?>

    <?php // echo $form->field($model, 'group_name') ?>

    <?php // echo $form->field($model, 'participant_cat_local') ?>

    <?php // echo $form->field($model, 'participant_cat_group') ?>

    <?php // echo $form->field($model, 'participant_cat_umk') ?>

    <?php // echo $form->field($model, 'participant_mode') ?>

    <?php // echo $form->field($model, 'participant_program') ?>

    <?php // echo $form->field($model, 'other_program') ?>

    <?php // echo $form->field($model, 'advisor') ?>

    <?php // echo $form->field($model, 'advisor_dropdown') ?>

    <?php // echo $form->field($model, 'institution') ?>

    <?php // echo $form->field($model, 'project_desc') ?>

    <?php // echo $form->field($model, 'competition_type') ?>

    <?php // echo $form->field($model, 'program_sub') ?>

    <?php // echo $form->field($model, 'booth_number') ?>

    <?php // echo $form->field($model, 'poster_file') ?>

    <?php // echo $form->field($model, 'payment_file') ?>

    <?php // echo $form->field($model, 'created_at') ?>

    <?php // echo $form->field($model, 'updated_at') ?>

    <?php // echo $form->field($model, 'submitted_at') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
