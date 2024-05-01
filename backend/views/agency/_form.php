<?php

use yii\helpers\Html;
use kartik\widgets\ActiveForm;
use kartik\date\DatePicker;
use kartik\select2\Select2;
use yii\helpers\Url;
use yii\web\JsExpression;
use backend\models\Entrepreneur;
/* @var $this yii\web\View */
/* @var $model backend\models\Competency */
/* @var $form yii\widgets\ActiveForm */
?>

   <div class="card">
    <div class="card-body">
<div class="competency-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'nama_agensi')->textInput() ?>

    

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

             <?=$form->field($model, 'tarikh_terima')->widget(DatePicker::classname(), [
        'removeButton' => false,
        'pluginOptions' => [
            'autoclose'=>true,
            'format' => 'yyyy-mm-dd',
            'todayHighlight' => true,
        ],
    ]);
    ?>

    <?= $form->field($model, 'description')->textarea(['rows' => 3]) ?>

    <div class="form-group">
        <?= Html::submitButton(\Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

  </div>
    </div>


