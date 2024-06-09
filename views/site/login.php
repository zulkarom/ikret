<?php
use yii\helpers\Html;
//use yii\bootstrap5\ActiveForm;
use kartik\widgets\ActiveForm;
use yii\helpers\Url;
$web = Yii::getAlias('@web');

$this->title = 'I-CREATE - Login';

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
                    <h5 class="card-title text-center pb-0 fs-4">Login to Your Account</h5>
                    <p class="text-center small">Enter your email & password to login</p>
                  </div>

             
                 
                  <?php
                  if($attendanceToken){
                    $url = ['/site/login', 't' => $attendanceToken];
                  }else{
                    $url = ['/site/login'];
                  }
                  
                  $form = ActiveForm::begin(['id' => 'login-form', 'class' => 'row g-3 needs-validation', 'action' => $url]); ?>

                    <div class="col-12">

                    <?= $form
            ->field($model, 'email', ['addon' => ['append' => ['content'=>'<i class="bi bi-person"></i>']]])
      
            ->textInput() ?>
            </div>

            <div class="col-12">
            <?= $form
            ->field($model, 'password', ['addon' => ['append' => ['content'=>'<i class="bi bi-lock"></i>']]])
    
            ->passwordInput() ?>
            </div>



            <div class="col-12">
                      <div class="form-check">
                <?= $form->field($model, 'rememberMe')->checkbox() ?>
            </div>
            <!-- /.col -->
    
            <!-- /.col -->
        </div>


       

            
                    <div class="col-12">
          
                      <?= Html::submitButton('Login', ['class' => 'btn btn-primary w-100', 'name' => 'login-button']) ?>
                    </div>
                    <br />
                    <div class="col-12">
                      <p class="small mb-0">I forgot my password <a href="<?=Url::to(['/site/forgot-password'])?>">Recover Password</a></p>
                    </div>
                    <div class="col-12">
                      <p class="small mb-0">Don't have account? <a href="<?=Url::to(['/site/register'])?>">Create an account</a></p>
                    </div>
                    <?php ActiveForm::end(); ?>

                </div>
              </div>





    </div>
    
</div>
             