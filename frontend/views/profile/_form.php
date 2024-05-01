<?php

use yii\helpers\Html;
use yii\helpers\Url;
use kartik\widgets\ActiveForm;
use kartik\widgets\FileInput;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use backend\models\Daerah;
use backend\models\Negeri;
/* @var $this yii\web\View */
/* @var $model common\models\User */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="user-form">
<div class="card">
    <div class="card-body">
    <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]) ?>
        
        <div class="row">
                <div class="col-3">
                    <?= $form->field($model, 'phone1')->textInput(['maxlength' => true])->label('HP') ?>
                </div>
                <div class="col-3">
                    <?= $form->field($model, 'phone2')->textInput(['maxlength' => true])->label('PHONE') ?>
                </div>
                <div class="col-3">
                     <?= $form->field($model, 'user_email')->textInput(['maxlength' => true]) ?>
                </div>
            </div>

            
             <div class="row">
                <div class="col-9">
                    <?= $form->field($model, 'address')->textArea(['rows' => '2'])->label('ADDRESS')?>
                </div>
            </div>

            <div class="row">
                <div class="col-3">
                    <?= $form->field($model, 'postcode')->textInput(['maxlength' => true])->label('POSTCODE') ?>
                </div>
                <div class="col-3">
                    <?= $form->field($model, 'city')->widget(Select2::classname(), [
                    'data' =>  ArrayHelper::map(Daerah::find()->all(),'id', 'daerah_name'),
                    'options' => ['placeholder' => 'Select...'],
                    'pluginOptions' => [
                        'allowClear' => true
                    ],
                    ])->label('CITY');?>
                </div>
                <div class="col-3">
                    <?= $form->field($model, 'state')->widget(Select2::classname(), [
                    'data' =>  ArrayHelper::map(Negeri::find()->all(),'id', 'negeri_name'),
                    'options' => ['placeholder' => 'Select...'],
                    'pluginOptions' => [
                        'allowClear' => true
                    ],
                    ])->label('STATE');?>
                </div>
            </div>

        <div class="row">
        <div class="col-md-4">
            <label>PROFILE PICTURE</label>
            <?= $form->field($model, 'upload_image')->widget(FileInput::classname(), [
                'pluginOptions' => [
                    'allowedFileExtensions' => ['jpg', 'png', 'jpeg'],
                    'showPreview' => true,
                    'showCaption' => true,
                    'showRemove' => true,
                    'showUpload' => false,
                    'initialPreview' => [
                        $model->upload_image ? '<img src="'.Url::to(['profile/profile-image', 'id' => Yii::$app->user->identity->id]).'" class="file-preview-image">' : null,
                    ],
                ]
            ])->label(false)?>
          </div>
        </div>

        <div class="form-group">
            <?= Html::submitButton('<span class="ti-save"></span> SAVE PROFILE', ['class' => 'btn btn-info']) ?>
        </div>


    <?php ActiveForm::end(); ?>
    </div>
</div>
</div>
