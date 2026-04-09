<?php

use kartik\widgets\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;

 $storageEntry = $storageEntry ?? false;

$this->title = 'Edit Registration - ' . $model->program_name;
?>
<div class="d-flex justify-content-center py-4">
    <a href="index.html" class="logo d-flex align-items-center w-auto">
        <span class="d-none d-lg-block"><?=$model->program_name?></span>
    </a>
</div>

<div class="card mb-3">
    <div class="card-header">Edit Registration</div>
    <div class="card-body">
        <p class="small">Enter the email and password / PIN used during registration.</p>

        <?php $form = ActiveForm::begin([
            'action' => Url::to(['public-edit-auth', 'id' => $model->id]),
            'enableClientValidation' => true,
        ]); ?>

        <?php if($storageEntry){ ?>
            <?= Html::hiddenInput('storage_action', 'public-edit-auth') ?>
            <?= Html::hiddenInput('storage_entry', 1) ?>
            <?= Html::hiddenInput('program_id', $model->id) ?>
        <?php } ?>

        <?= $form->errorSummary($accessForm, ['class' => 'alert alert-danger']) ?>

        <?= $form->field($accessForm, 'contact_email')->textInput() ?>
        <?= $form->field($accessForm, 'password')->passwordInput() ?>

        <div class="mt-3">
            <?= Html::submitButton('Continue', ['class' => 'btn btn-primary']) ?>
        </div>

        <?php ActiveForm::end(); ?>

        <div class="mt-2">
            <?= Html::a('Back to Registration Form', ['public-register-form', 'id' => $model->id], ['class' => 'btn btn-outline-secondary']) ?>
        </div>
    </div>
</div>
