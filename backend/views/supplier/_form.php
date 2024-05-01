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
?>



<div class="row">
    <div class="col-12">
        <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>
            
        <div class="card">
            <div class="card-body">
                <div class="profile-index">

                    <div class="row">
                        <div class="col-md-5"><?= $form->field($modelUser, 'fullname')->textInput() ?></div>

                        <div class="col-md-3">
                        <?= $form->field($modelUser, 'email')->textInput() ?>
                         </div>

                        <div class="col-md-1"><?= $form->field($model, 'age') ?></div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-5"><?= $form->field($model, 'biz_name')->textInput() ?></div>
                        <div class="col-md-3"><?= $form->field($model, 'phone') ?></div>
                    </div>


                    <div class="row">
                        <div class="col-md-9"><?= $form->field($model, 'address')->textarea(['rows' => '3'])->label('Alamat Surat Menyurat')?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-3">
                            <?= $form->field($model, 'postcode')->textInput(['maxlength' => true]) ?>
                        </div>
                        <div class="col-3">
                            <?= $form->field($model, 'city')->widget(Select2::classname(), [
                            'data' =>  ArrayHelper::map(Daerah::find()->all(),'id', 'daerah_name'),
                            'options' => ['placeholder' => 'Select...'],
                            'pluginOptions' => [
                                'allowClear' => true
                            ],
                            ]);?>
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
                        <div class="col-6">
                            <?php if($modelUser->id): ?>
                               <?= $form->field($modelUser, 'rawPassword')->passwordInput(['maxlength' => true])->label('Reset Password (leave blank if no change)') ?>
                            <?php else: ?>
                               <?= $form->field($modelUser, 'rawPassword')->passwordInput(['maxlength' => true])->label('Password') ?> 
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