<?php

use yii\helpers\Html;
use kartik\widgets\ActiveForm;
use backend\models\Sector;
use backend\models\Entrepreneur;
use yii\helpers\ArrayHelper;
use kartik\select2\Select2;
use yii\helpers\Url;
use yii\web\JsExpression;
/* @var $this yii\web\View */
/* @var $model backend\models\SectorEntrepreneur */
/* @var $form yii\widgets\ActiveForm */
?>

   <div class="card">
    <div class="card-body">
<div class="sector-entrepreneur-form">

    <?php $form = ActiveForm::begin(); ?>

    <?php
            $userDesc = empty($model->entrepreneur_id) ? '' : Entrepreneur::findOne($model->entrepreneur_id)->user->fullname;
            $url = Url::to(['/entrepreneur/entrepreneur-list-json']);
            echo $form->field($model, 'entrepreneur_id')->widget(Select2::classname(), [
                'initValueText' => $userDesc, // set the initial display text
                'options' => ['placeholder' => 'Search for a beneficiary ...'],
            'pluginOptions' => [
                'allowClear' => true,
                'minimumInputLength' => 3,
                'language' => [
                    'errorLoading' => new JsExpression("function () { return 'Waiting for results...'; }"),
                ],
                'ajax' => [
                    'url' => $url,
                    'dataType' => 'json',
                    'data' => new JsExpression('function(params) { 
                        return {
                                q:params.term
                                
                                }; 
                        
                        }')
                ],
                'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
                'templateResult' => new JsExpression('function(user) { return user.text; }'),
                'templateSelection' => new JsExpression('function (user) { return user.text; }'),
            ],
            ])->label('Beneficiary');

             ?>

    <?= $form->field($model, 'sector_id')->dropDownList(ArrayHelper::map(Sector::find()->all(), 'id', 'sector_name'), ['prompt' => 'Select']) ?>

    <?= $form->field($model, 'description')->textarea(['rows' => 6]) ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
  </div>
    </div>


