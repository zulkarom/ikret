<?php

use kartik\form\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;

 $form = ActiveForm::begin(['id' => 'login-form', 'class' => 'row']); ?>

<div class="col-12">

<?= $form
->field($model, 'fullname', ['template' => '{label}{input}<i style="font-size:11px">Make sure you type the full name (include title if any) correctly since it will be used in certificates.</i>{error}','addon' => ['append' => ['content'=>'<i class="bi bi-person"></i>']]])->textInput(['style' => 'text-transform: uppercase'])?>
</div>

<div class="col-12">
<?= $form
->field($model, 'username', ['template' => '{label}{input}<i style="font-size:11px">Matric number / Staff No. or any unique username</i>{error}','addon' => ['append' => ['content'=>'<i class="bi bi-person"></i>']]])
->textInput(['style' => 'text-transform: lowercase']) ?>
</div>

<div class="col-12">
<?= $form
->field($model, 'email', ['addon' => ['append' => ['content'=>'<i class="bi bi-envelope"></i>']]])
->textInput(['style' => 'text-transform: lowercase']) ?>
</div>

<?php if($model->self_register){?>
<div class="col-12">
<?= $form
->field($model, 'password', ['template' => '{label}{input}<i style="font-size:11px">A password is necessary for login purpose</i>{error}','addon' => ['append' => ['content'=>'<i class="bi bi-lock"></i>']]])

->passwordInput() ?>
</div>

<div class="col-12">
<?= $form
->field($model, 'password_repeat', ['addon' => ['append' => ['content'=>'<i class="bi bi-lock-fill"></i>']]])

->passwordInput() ?>
</div>

<?php } ?>

<div class="col-12">

  <?php 
  
  if($model->self_register){
  echo Html::submitButton('REGISTER I-CREATE', ['class' => 'btn btn-primary w-100', 'name' => 'login-button']);
  }else{
    echo Html::submitButton($model->button_label, ['class' => 'btn btn-success']);
  }
  
  ?>

</div>
<br />

<?php if($model->self_register){?>
<div class="col-12">
  <p class="small mb-0">Already have an account?  <a href="<?=Url::to(['/site/login'])?>">Login</a></p>
</div>
<?php } ?>
<?php ActiveForm::end(); ?>
