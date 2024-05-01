<?php

use yii\helpers\Html;
use kartik\widgets\ActiveForm;
use backend\models\SocialImpactCategory;
use backend\models\Entrepreneur;
use yii\helpers\ArrayHelper;
use kartik\select2\Select2;
use yii\helpers\Url;
use yii\web\JsExpression;
/* @var $this yii\web\View */
/* @var $model backend\models\Competency */
/* @var $form yii\widgets\ActiveForm */
?>

   <div class="card">
    <div class="card-body">
<div class="competency-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="row">
        <div class="col-md-10">
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
        </div>
    </div>

    <div class="row">
        <div class="col-md-10">
        <?= $form->field($model, 'category_id')->dropDownList(
            ArrayHelper::map(SocialImpactCategory::find()->orderBy("id DESC")->all(),'id', 'category_name'), ['class'=>'form-control category', 'prompt' => 'Select' ]
        )?>
        </div>
    </div>

    <?php
    if($model->category_id == 1){
            $show = '';
        }else{
            $show = 'style="display:none"';
        }
    ?> 

    <div id="group-medium" <?=$show?>>
         <div class="row">
            <div class="col-10">
                <?= $form->field($model, 'other')->textInput(['maxlength' => true])->label(\Yii::t('app', 'Please State'))?>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-10">
            <?= $form->field($model, 'description')->textarea(['rows' => 3]) ?>
        </div>
    </div>

    <div class="form-group">
        <?= Html::submitButton(\Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

  </div>
    </div>

    <?php 
$this->registerJs('

$(".category").change(function(){
    var val = $(this).val();
    if(val == 1){
        $("#group-medium").slideDown();
    }else{
        $("#group-medium").slideUp();
    }
});
');
?>


