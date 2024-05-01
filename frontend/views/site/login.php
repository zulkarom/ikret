<?php
use yii\helpers\Html;
// use yii\bootstrap\ActiveForm;
use kartik\widgets\ActiveForm;

$this->title = 'Sign In';

$dirAssests = Yii::$app->assetManager->getPublishedUrl('@backend/assets/adminpress');
$fieldOptions1 = [
    'options' => ['class' => 'input-group mb-3'],
    'inputTemplate' => "{input}
            <div class='input-group-text'>
              <span class='fa fa-user'></span>
            </div>
          </div>"
];

$fieldOptions2 = [
    'options' => ['class' => 'input-group mb-3'],
    'inputTemplate' => "{input}<div class='input-group-append'>
            <div class='input-group-text'>
              <span class='fa fa-lock'></span>
            </div>
          </div>"
];
?>

<div class="login-box">
    <div class="login-logo">
      <br/><br/>
    <img src="<?= $dirAssests?>/images/logo-invoice.png" alt="AlMukhlisin">
        
  </div>
    <!-- /.login-logo -->
  <div class="card">
    <div class="card-body login-card-body">
        <p class="login-box-msg">frontend LOGIN</p>

        <?php $form = ActiveForm::begin(['id' => 'login-form', 'enableClientValidation' => false]); ?>
		
        <?= $form
            ->field($model, 'username', ['addon' => ['append' => ['content'=>'<i class="fa fa-user"></i>']]])
            ->label(false)
            ->textInput(['placeholder' => $model->getAttributeLabel('username')]) ?>

        <?= $form
            ->field($model, 'password', ['addon' => ['append' => ['content'=>'<i class="fa fa-lock"></i>']]])
            ->label(false)
            ->passwordInput(['placeholder' => $model->getAttributeLabel('password')]) ?>

        <div class="row">
            <div class="col-8">
                <?= $form->field($model, 'rememberMe')->checkbox() ?>
            </div>
            <!-- /.col -->
            <div class="col-4">
                <?= Html::submitButton('Sign in', ['class' => 'btn btn-info btn-block', 'name' => 'login-button']) ?>
            </div>
            <!-- /.col -->
        </div>


        <?php ActiveForm::end(); ?>
        <!-- /.social-auth-links -->
        <?= Html::a('I forgot my password',['/user/recovery/request'],['class' => 'field-label text-muted mb10', 'tabindex' => '5']) ?>
        <a href="#">I forgot my password</a><br>


    </div></div>
    <!-- /.login-box-body -->
</div><!-- /.login-box -->




<!--  <div class="login-box">
  <div class="login-logo">
    <img src="<?=$dirAssests?>/dist/img/logo-login.png" alt="AlMukhlisin">
  </div>

  <div class="card">
    <div class="card-body login-card-body">
      <p class="login-box-msg">Sign in to start your session</p>

      <form action="../../index3.html" method="post">
        <div class="input-group mb-3">
          <input type="text" class="form-control" placeholder="Username">
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fa fa-user"></span>
            </div>
          </div>
        </div>
        <div class="input-group mb-3">
          <input type="password" class="form-control" placeholder="Password">
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fa fa-lock"></span>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-8">
            <div class="icheck-primary">
              <input type="checkbox" id="remember">
              <label for="remember">
                Remember Me
              </label>
            </div>
          </div>
  
          <div class="col-4">
            <button type="submit" class="btn btn-primary btn-block">Sign In</button>
          </div>
   
        </div>
      </form>




      <p class="mb-1">
        <a href="forgot-password.html">I forgot my password</a>
      </p>
      <p class="mb-0">
        <a href="register.html" class="text-center">Register a new membership</a>
      </p>
    </div>

  </div>
</div> -->