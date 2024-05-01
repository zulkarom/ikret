<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

?>

 
    <?php $form = ActiveForm::begin(); ?>
	
	<div class="row">
<div class="col-md-12"><?= $form->field($model, 'password_old')->passwordInput() ?></div>
</div>
	
	<div class="row">

<div class="col-md-12"><?= $form->field($model, 'password')->passwordInput() ?></div>
</div>

	<div class="row">

<div class="col-md-12"><?= $form->field($model, 'confirm_password')->passwordInput() ?></div>
</div>
 
        
        
 
        <div class="form-group">
            <?= Html::submitButton('Change Password', ['class' => 'btn btn-primary', 'name' => 'form-option', 'value' => 'change-password']) ?>
        </div>
    <?php ActiveForm::end(); ?>



