<?php

use kartik\form\ActiveForm;
use yii\helpers\Html;
 
/* @var $this yii\web\View */
/* @var $model frontend\models\ChangePasswordForm */
/* @var $form ActiveForm */
 
$this->title = 'Change Password';
?>
<div class="card">
<div class="card-body pt-4"><div class="user-changePassword">
 
    <?php $form = ActiveForm::begin(); ?>
	
	<div class="row">
<div class="col-md-4"><?= $form->field($model, 'password_old')->passwordInput() ?></div>
</div>
	
	<div class="row">
<div class="col-md-4"><?= $form->field($model, 'password')->passwordInput() ?></div>
</div>

	<div class="row">
<div class="col-md-4"><?= $form->field($model, 'confirm_password')->passwordInput() ?></div>
</div>
 
<br />
        
        
 
        <div class="form-group">
            <?= Html::submitButton('Change Password', ['class' => 'btn btn-primary']) ?>
        </div>
    <?php ActiveForm::end(); ?>
 
</div></div>
</div>