<?php
use yii\helpers\Html;
//use yii\bootstrap5\ActiveForm;
use kartik\widgets\ActiveForm;
use yii\helpers\Url;
$web = Yii::getAlias('@web');

$this->title = 'I-CREATE - Register';

?>

<div class="row">
<div class="col-md-3"></div>
    <div class="col-md-5">


    <div class="d-flex justify-content-center py-4">
                <a href="index.html" class="logo d-flex align-items-center w-auto">
          
                  <span class="d-none d-lg-block">I-CREATE Registration Form</span>
                </a>
              </div><!-- End Logo -->

              <div class="card mb-3">

                <div class="card-body">

                  <div class="pt-4 pb-2">
                    <h5 class="card-title text-center pb-0 fs-4">Create an Account</h5>
                    <p class="text-center small">Enter your personal details to create account</p>
                  </div>

             
                 
                  <?php $form = ActiveForm::begin(['id' => 'login-form', 'class' => 'row g-3 needs-validation']); ?>

                    <div class="col-12">

                    <?= $form
            ->field($model, 'fullname', ['addon' => ['append' => ['content'=>'<i class="bi bi-person"></i>']]])->textInput(['style' => 'text-transform: uppercase'])?>
            </div>
            <div class="col-12">
            <?= $form
            ->field($model, 'matric', ['addon' => ['append' => ['content'=>'<i class="bi bi-credit-card"></i>']]])
      
            ->textInput() ?>
            </div>
            <div class="col-12">
            <?= $form
            ->field($model, 'email', ['addon' => ['append' => ['content'=>'<i class="bi bi-envelope"></i>']]])
      
            ->textInput() ?>
            </div>
            <div class="col-12">
            <?= $form
            ->field($model, 'phone', ['addon' => ['append' => ['content'=>'<i class="bi bi-phone"></i>']]])
      
            ->textInput() ?>
            </div>

            <div class="col-12">
            <?= $form
            ->field($model, 'password', ['addon' => ['append' => ['content'=>'<i class="bi bi-lock"></i>']]])
    
            ->passwordInput() ?>
            </div>

            <div class="col-12">
            <?= $form
            ->field($model, 'password_repeat', ['addon' => ['append' => ['content'=>'<i class="bi bi-lock-fill"></i>']]])
    
            ->passwordInput() ?>
            </div>


            
                    <div class="col-12">
          
                      <?= Html::submitButton('Register iCreate', ['class' => 'btn btn-primary w-100', 'name' => 'login-button']) ?>
                    </div>
                    <br />
   
                    <div class="col-12">
                      <p class="small mb-0">Already have an account?  <a href="<?=Url::to(['/site/login'])?>">Login</a></p>
                    </div>
                    <?php ActiveForm::end(); ?>

                </div>
              </div>





    </div>
    
</div>
             