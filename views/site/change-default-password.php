<?php

use kartik\widgets\ActiveForm;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\DefaultPasswordForm */
/* @var $returnUrl string */

$this->title = 'Change Password';
?>

<div class="row">
    <div class="col-md-3"></div>
    <div class="col-md-5">
        <div class="d-flex justify-content-center py-4">
            <a href="<?= Yii::$app->homeUrl ?>" class="logo d-flex align-items-center w-auto">
                <span class="d-none d-lg-block">I-CREATE</span>
            </a>
        </div>

        <div class="card mb-3">
            <div class="card-body">
                <div class="pt-4 pb-2">
                    <h5 class="card-title text-center pb-0 fs-4">Update Your Password</h5>
                    <p class="text-center small">Please enter a new password before continuing.</p>
                </div>

                <?php $form = ActiveForm::begin(['id' => 'change-default-password-form', 'class' => 'row g-3']); ?>

                <div class="col-12">
                    <?= $form
                        ->field($model, 'password', ['addon' => ['append' => ['content' => '<i class="bi bi-lock"></i>']]])
                        ->passwordInput(['autocomplete' => 'new-password']) ?>
                </div>

                <?php if ($model->requiresEmailUpdate()): ?>
                    <div class="col-12">
                        <?= $form
                            ->field($model, 'email', ['addon' => ['append' => ['content' => '<i class="bi bi-envelope"></i>']]])
                            ->textInput(['maxlength' => true, 'autocomplete' => 'email']) ?>
                    </div>
                <?php endif; ?>

                <div class="col-12">
                    <?= Html::submitButton('Save', ['class' => 'btn btn-primary w-100']) ?>
                </div>

                <div class="col-12 mt-2">
                    <?= Html::a('Skip This Time', $returnUrl, [
                        'class' => 'btn btn-outline-secondary w-100',
                    ]) ?>
                </div>

                <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>
</div>
