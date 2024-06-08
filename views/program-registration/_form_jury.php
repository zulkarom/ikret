<?php

use app\models\ProgramRubric;
use app\models\UserRole;
use kartik\date\DatePicker;
use kartik\select2\Select2;
use yii\bootstrap5\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;

/** @var yii\web\View $this */
/** @var app\models\ProgramRegistration $model */
/** @var yii\widgets\ActiveForm $form */
?>
<div class="program-registration-form">
    <?=$form->field($model, 'users')->widget(Select2::class, [
        'data' => UserRole::listJury(),
        'options' => ['multiple' => true, 'placeholder' => 'Select..'],
        'pluginOptions' => [
            'allowClear' => true
        ],
    ])->label('Select Jury (s)')?>

    <div class="row">

    <div class="col-md-9">
        <?php
        if($programSub){
            $list_rubrics = $programSub->programRubrics;
        }else{
            $list_rubrics = $program->programRubrics;
        }
        //print_r($list_rubrics);die();
        $rubricArray = ArrayHelper::map($list_rubrics, 'id', 'rubric.rubric_name');
        $prompt = [];
        if(count($rubricArray) > 1){
            $prompt = ['prompt' => 'Choose Rubric'];
        }
        ?>
        <?= $form->field($model, 'rubric_id')->dropDownList($rubricArray,$prompt) ?>
        </div>

        <?php if($program->programStages){
            $stagesArray = ArrayHelper::map($program->programStages, 'id', 'stage_name');
            $prompt = [];
            if(count($rubricArray) > 1){
                $prompt = ['prompt' => 'Choose Stage'];
            }
            ?>
        <div class="col-md-3">
        <?= $form->field($model, 'stage')->dropDownList($stagesArray,$prompt) ?>
        </div>
        <?php } ?>

        <?php 
        /*  <div class="col-md-4"><?= $form->field($model, 'method')->dropDownList($model->listMethod()) ?></div> */
        
        ?>
       
    </div>

    <div class="row">
        <div class="col-md-3"><?=$form->field($model, 'date_start')->widget(DatePicker::classname(), [
    'removeButton' => false,
    'pickerIcon' => '<i class="bi bi-calendar3"></i>',
    'pluginOptions' => [
        'autoclose'=>true,
        'format' => 'yyyy-mm-dd',
        'todayHighlight' => true,
        
    ],
]);
?></div>
        <div class="col-md-3"><?=$form->field($model, 'date_end')->widget(DatePicker::classname(), [
    'removeButton' => false,
    'pickerIcon' => '<i class="bi bi-calendar3"></i>',
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

    <?=$form->field($model, 'keep_data')->checkbox(['label'=>'Keep current data in this form after submitting']);?>
    <?=$form->field($model, 'keep_open')->checkbox(['label'=>'Keep this form open']);?>


    

    
<br />

    <div class="form-group">
        <?= Html::button('Assign Jury to Selected Participants', ['id'=>'btn-submit-jury', 'class' => 'btn btn-success']) ?> 
        
        <?= Html::button('Clear Form', ['id'=>'btn-clear-form', 'class' => 'btn btn-outline-success']) ?> <a href="javascript:void(0)" id="hide-jury-form">Hide this form</a>
    </div>


    

</div>

<?php

if($programSub){
    $url = Url::to(['manager-clear-form', 'id' => $program->id, 'sub' => $programSub->id],true);
}else{
    $url = Url::to(['manager-clear-form', 'id' => $program->id],true);
}

$this->registerJs('

$("#btn-clear-form").click(function(){
    var url = "'. $url .'";
    window.location.replace(url);

});

$("#btn-submit-jury").click(function(){
    var checkboxes = document.querySelectorAll(\'input[name="selection[]"]:checked\');
    if (checkboxes.length === 0) {
        alert("Please select participant(s) first before clicking the assign button");
    }else{
        $("#jury-assign-form").submit();
       // alert("submitting");
    }
});




');

?>