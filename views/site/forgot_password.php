<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap4\ActiveForm */

/* @var $model \frontend\models\PasswordResetRequestForm */

use kartik\form\ActiveForm;
use yii\helpers\Html;

$this->title = 'Forgot Password';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="row">
<div class="col-md-3"></div>
    <div class="col-md-5">


    <div class="d-flex justify-content-center py-4">
                <a href="index.html" class="logo d-flex align-items-center w-auto">
          
                  <span class="d-none d-lg-block">I-CREATE</span>
                </a>
              </div><!-- End Logo -->

              <div class="card mb-3">

                <div class="card-body">

                  <div class="pt-4 pb-2">
                    <h5 class="card-title text-center pb-0 fs-4">Recover Password</h5>
                    <p class="text-center small">Please fill out your email. A link to reset password will be sent there.</p>
                  </div>

     

            <?php $form = ActiveForm::begin(['id' => 'request-password-reset-form']); ?>

            <?= $form->field($model, 'email')->textInput(['autofocus' => true]) ?>

            <div class="form-group">
                <?= Html::submitButton('Send', ['class' => 'btn btn-primary']) ?>
            </div>

            <?php ActiveForm::end(); ?>





</div>
              </div>





    </div>
    
</div>
             