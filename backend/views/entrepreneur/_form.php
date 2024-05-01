<?php

use yii\helpers\Html;
use kartik\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use kartik\select2\Select2;
use yii\helpers\Url;
use karpoff\icrop\CropImageUpload;
use backend\models\Daerah;
use backend\models\Negeri;
/* @var $this yii\web\View */
/* @var $form ActiveForm */
$dirAssests = Yii::$app->assetManager->getPublishedUrl('@backend/assets/crypto');

$model->u_longitude = $model->longitude; 
$model->u_latitude = $model->latitude;
$model->u_location = $model->location;
?>



<div class="row">
    <div class="col-12">
        <?php $form = ActiveForm::begin([
            'enableClientValidation' => true,
            'enableAjaxValidation' => true,
            'validateOnChange' => true,
            
            
            ]); ?>
            
        <div class="card">
            <div class="card-body">
            
              <div class="card-title"><h4>Beneficiary Infomation</h4></div>
              <br />
            
                <div class="profile-index">

                    <div class="row">
                        <div class="col-md-6"><?= $form->field($model, 'fullname')->textInput() ?></div>

                   

                        <div class="col-md-3"><?= $form->field($model, 'age') ?></div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6"><?= $form->field($model, 'nric')->textInput() ?></div>
                             <div class="col-md-3">
                        <?= $form->field($model, 'email')->textInput() ?>
                         </div>

            
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6"><?= $form->field($model, 'biz_name')->textInput() ?></div>
                        <div class="col-md-3"><?= $form->field($model, 'phone') ?></div>
                    </div>
                    
                     <div class="row">
                        <div class="col-md-9"><?= $form->field($model, 'biz_info')->textarea(['rows' => '2'])?>
                        </div>
                    </div>


                    <div class="row">
                        <div class="col-md-9"><?= $form->field($model, 'address')->textarea(['rows' => '2'])?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-3">
                            <?= $form->field($model, 'postcode')->textInput(['maxlength' => true]) ?>
                        </div>
                        <div class="col-3">
                            <?= $form->field($model, 'city')->textInput(['maxlength' => true]) ?>
                        </div>
                        <div class="col-3">
                            <?= $form->field($model, 'state')->widget(Select2::classname(), [
                            'data' =>  ArrayHelper::map(Negeri::find()->all(),'id', 'negeri_name'),
                            'options' => ['placeholder' => 'Select...'],
                            'pluginOptions' => [
                                'allowClear' => true
                            ],
                            ]);?>
                        </div>
                    </div>
                    
                     <div class="row">
                        <div class="col-md-9"><?= $form->field($model, 'note')->textarea(['rows' => '3'])?>
                        </div>
                    </div>

             
                        
                </div>
            </div>
        </div>
        
        <br />
         <div class="card">
            <div class="card-body">
            <div class="card-title"><h4>Login Infomation</h4></div>
            <br />
                <div class="profile-index">

                    <div class="row">
                        <div class="col-md-5"><?= $form->field($model, 'username')->textInput() ?>
                        * Preferable i.c number without "-"<br />if N/A put email or phone     
                        </div>
                    </div>
                    
              
                    <div class="row">
                        <div class="col-md-5">
                        <input type="password" name="hidden" id="hidden" style="width: 0; height: 0; border: 0; padding: 0" />
                            <?php if($model->id): ?>
                               <?= $form->field($model, 'password')->passwordInput(['maxlength' => true, 'role' => 'presentation', 'autocomplete' => 'off'])->label('Reset Password (leave blank if is no change)') ?>
                            <?php else: ?>
                               <?= $form->field($model, 'password')->passwordInput(['maxlength' => true, 'role' => 'presentation', 'autocomplete' => 'off'])->label('Password') ?> 
                           <?php endif; ?>
                       </div>
                   </div>  
                   
                            
                        
                </div>
            </div>
        </div>
        
        
        <br/>
        <div class="form-group">
            <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
        </div>
            
        <?php ActiveForm::end(); ?>
    </div>
</div>


<?php 

$this->registerJs('

$("#user-rawpassword").val("");

');

?>