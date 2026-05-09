<?php

use app\models\ProgramRubric;
use app\models\UserRole;
use app\models\RubricJudgingSession;
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
        $programRubricQuery = ProgramRubric::find()
            ->alias('pr')
            ->where(['pr.program_id' => (int)$program->id]);

        if($programSub){
            $subTable = Yii::$app->db->schema->getTableSchema(\app\models\ProgramSub::tableName());
            $hasSubActiveColumn = $subTable && $subTable->getColumn('is_active');

            if($hasSubActiveColumn && (int)$programSub->getAttribute('is_active') !== 1){
                $programRubricQuery->andWhere('0=1');
            }else{
                $programRubricQuery->andWhere(['pr.program_sub' => (int)$programSub->id]);
            }
        }else{
            $programRubricQuery->andWhere(['or', ['pr.program_sub' => null], ['pr.program_sub' => 0]]);
        }

        $list_rubrics = $programRubricQuery->all();
        $rubricArray = ArrayHelper::map($list_rubrics, 'rubric_id', 'rubric.rubric_name');
        $prompt = [];
        if(count($rubricArray) > 1){
            $prompt = ['prompt' => 'Choose Rubric'];
        }
        ?>
        <?= $form->field($model, 'rubric_id')->dropDownList($rubricArray,$prompt) ?>
        </div>

        <div class="col-md-3">
        <?php
        $sessionUrl = Url::to(['judging-session-list-json'], true);
        $sessionData = [];
        if($model->judging_session_id){
            $s = RubricJudgingSession::findOne((int)$model->judging_session_id);
            if($s){
                $parts = [];
                $parts[] = $s->session_name;
                if($s->datetime_start && $s->datetime_end){
                    $startTime = strtotime($s->datetime_start);
                    $endTime = strtotime($s->datetime_end);
                    if(date('Y-m-d', $startTime) === date('Y-m-d', $endTime)){
                        $parts[] = date('d M Y, h:i A', $startTime) . ' - ' . date('h:i A', $endTime);
                    }else{
                        $parts[] = date('d M Y, h:i A', $startTime) . ' - ' . date('d M Y, h:i A', $endTime);
                    }
                }else if($s->datetime_start){
                    $parts[] = date('d M Y, h:i A', strtotime($s->datetime_start));
                }else if($s->datetime_end){
                    $parts[] = date('d M Y, h:i A', strtotime($s->datetime_end));
                }
                if($s->location){
                    $parts[] = $s->location;
                }
                $sessionData[(int)$s->id] = implode(' | ', $parts);
            }
        }
        ?>
        <?= $form->field($model, 'judging_session_id')->widget(Select2::class, [
            'data' => $sessionData,
            'options' => ['placeholder' => 'Select..'],
            'pluginOptions' => [
                'allowClear' => true,
                'ajax' => [
                    'url' => $sessionUrl,
                    'dataType' => 'json',
                    'data' => new \yii\web\JsExpression('function(params){ return { rubric_id: $("#juryassign-rubric_id").val(), program_id: ' . (int)$program->id . ', sub: ' . ($programSub ? (int)$programSub->id : 'null') . ' }; }'),
                    'delay' => 250,
                ],
            ],
        ]) ?>
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

    <?=$form->field($model, 'keep_data')->checkbox(['label'=>'Keep current data in this form after submitting']);?>
    <?=$form->field($model, 'keep_open')->checkbox(['label'=>'Keep this form open']);?>


    

    
<br />

    <div class="form-group">
        <?= Html::button('Assign Jury to Selected Participants', ['id'=>'btn-submit-jury', 'class' => 'btn btn-success']) ?> 
        <?= Html::a('Import Jury Assignment CSV', ['manager-import-jury-assignments', 'id' => $program->id, 'sub' => $programSub ? $programSub->id : null], ['class' => 'btn btn-outline-primary']) ?>
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

function clearJudgingSession(){
    var el = $("#juryassign-judging_session_id");
    if(el.length){
        el.val(null).trigger("change");
    }
}

$(document).on("change", "#juryassign-rubric_id", function(){
    clearJudgingSession();
});

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
