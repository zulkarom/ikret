<?php

use app\models\UserRole;
use kartik\date\DatePicker;
use kartik\select2\Select2;
use yii\bootstrap5\ActiveForm;
use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\ProgramRegistration $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="program-registration-form">



    <?=$form->field($model, 'users')->widget(Select2::classname(), [
        'data' => UserRole::listJury(),
        'options' => ['multiple' => true,'placeholder' => 'Search for jury ...'],
        'pluginOptions' => [
            'allowClear' => true
        ],
    ])?>

    <div class="row">

    <div class="col-md-5">
        <?= $form->field($model, 'rubric_id')->dropDownList($model->listRubrics(),['prompt' => 'Choose Rubric']) ?>
        </div>

        <div class="col-md-3">
        <?= $form->field($model, 'stage')->dropDownList($model->listStage()) ?>
        </div>
        <div class="col-md-4"><?= $form->field($model, 'method')->dropDownList($model->listMethod()) ?></div>
    </div>

    <div class="row">
        <div class="col-md-3"><?=$form->field($model, 'date_start')->widget(DatePicker::classname(), [
    'removeButton' => false,
    'pickerIcon' => '<i class="bi bi-calendar"></i>',
    'pluginOptions' => [
        'autoclose'=>true,
        'format' => 'yyyy-mm-dd',
        'todayHighlight' => true,
        
    ],
]);
?></div>
        <div class="col-md-3"><?=$form->field($model, 'date_end')->widget(DatePicker::classname(), [
    'removeButton' => false,
    'pickerIcon' => '<i class="bi bi-calendar"></i>',
    'pluginOptions' => [
        'autoclose'=>true,
        'format' => 'yyyy-mm-dd',
        'todayHighlight' => true,
        
    ],
    
    
]);
?></div>
        <div class="col-md-6"><?= $form->field($model, 'location')->textInput() ?></div>
    </div>

    <div class="row">
        <div class="col-md-6"><?= $form->field($model, 'note')->textarea(['rows' => 3]) ?></div>
        <div class="col-md-6"><?= $form->field($model, 'link')->textarea(['rows' => 3]) ?></div>
    </div>
    <?php 
    /*  <label for="keep-data" style="margin-bottom: 10px;"><input type="checkbox" name="keep-data" id="keep-data"> Keep current data in this form after submitting</label>*/
    ?>
    
<br />

    <div class="form-group">
        <?= Html::submitButton('Assign Jury to Selected Participants', ['class' => 'btn btn-success']) ?> <a href="javascript:void(0)" id="hide-jury-form">Hide this form</a>
    </div>

    

</div>
