<?php
use yii\helpers\Html;
// use yii\bootstrap\ActiveForm;
use kartik\widgets\ActiveForm;
use yii\helpers\Url;
$web = Yii::getAlias('@web');

$this->title = 'iCreate - Login';

?>

<section class="d-flex flex-column align-items-center justify-content-center">
        <div class="container">
          <div class="row justify-content-center">
            <div class="d-flex flex-column align-items-center justify-content-center">


              <div class="card mb-3">

                <div class="card-body">

                  <div class="pt-4 pb-2">
                    <h5 class="card-title text-center pb-0 fs-4">Login to Your Account</h5>
                    <p class="text-center small">Enter your username & password to login</p>
                  </div>

                 

                  <?php $form = ActiveForm::begin(['id' => 'login-form', 'class' => 'row g-3 needs-validation']); ?>

                    <div class="col-12">

                    <?= $form
            ->field($model, 'username', ['addon' => ['append' => ['content'=>'<i class="bi bi-person"></i>']]])
      
            ->textInput(['placeholder' => $model->getAttributeLabel('username')]) ?>
            </div>

            <div class="col-12">
            <?= $form
            ->field($model, 'password', ['addon' => ['append' => ['content'=>'<i class="bi bi-lock"></i>']]])
    
            ->passwordInput(['placeholder' => $model->getAttributeLabel('password')]) ?>
            </div>



        <div class="row">
            <div class="col-8">
                <?= $form->field($model, 'rememberMe')->checkbox() ?>
            </div>
            <!-- /.col -->
    
            <!-- /.col -->
        </div>


       

            

                    <div class="col-12">
                      <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="remember" value="true" id="rememberMe">
                        <label class="form-check-label" for="rememberMe">Remember me</label>
                      </div>
                    </div>
                    <div class="col-12">
          
                      <?= Html::submitButton('Login', ['class' => 'btn btn-primary w-100', 'name' => 'login-button']) ?>
                    </div>
                    <br />
                    <div class="col-12">
                      <p class="small mb-0">I forgot my password <a href="pages-register.html">Recover Password</a></p>
                    </div>
                    <div class="col-12">
                      <p class="small mb-0">Don't have account? <a href="pages-register.html">Create an account</a></p>
                    </div>
                    <?php ActiveForm::end(); ?>

                
              </div>


            </div>
          </div>
        </div>

      </section>

