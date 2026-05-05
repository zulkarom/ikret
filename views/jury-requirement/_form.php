<?php

use kartik\widgets\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;

/** @var yii\web\View $this */
/** @var app\models\JuryRequirement $model */
/** @var array $programSubCombinedList */
/** @var array $sessionList */

$programSubCombinedList = $programSubCombinedList ?? [];
$sessionList = $sessionList ?? [];

$form = ActiveForm::begin();

$selectedKey = null;
if($model->program_sub_id){
    $selectedKey = 's:' . $model->program_sub_id;
}elseif($model->program_id){
    $selectedKey = 'p:' . $model->program_id;
}

echo Html::label('Program / Sub Program', 'program-sub-combined', ['class' => 'form-label']);
echo Html::dropDownList('program_sub_combined', $selectedKey, $programSubCombinedList, [
    'id' => 'program-sub-combined',
    'class' => 'form-select',
    'prompt' => 'Please select',
]);

echo $form->field($model, 'program_id')->hiddenInput()->label(false);
echo $form->field($model, 'program_sub_id')->hiddenInput()->label(false);

echo $form->field($model, 'judging_session_id')->dropDownList($sessionList, ['prompt' => 'N/A']);

echo $form->field($model, 'is_required')->dropDownList([1 => 'YES', 0 => 'NO']);

echo $form->field($model, 'is_active')->dropDownList([1 => 'OPEN', 0 => 'CLOSED']);

echo $form->field($model, 'jury_limit')->textInput();

echo Html::submitButton('Save', ['class' => 'btn btn-primary']);

ActiveForm::end();

$route = [Yii::$app->controller->id . '/' . Yii::$app->controller->action->id];
if(Yii::$app->controller->action->id === 'update' && !$model->isNewRecord){
    $route['id'] = $model->id;
}
$baseUrl = Url::to($route);

$this->registerJs('
function parseProgramSubCombined(val){
    var res = {program_id: "", program_sub_id: ""};
    if(!val){
        return res;
    }
    if(val.indexOf("p:") === 0){
        res.program_id = val.substring(2);
    }else if(val.indexOf("s:") === 0){
        res.program_sub_id = val.substring(2);
    }
    return res;
}

function resolveProgramIdForSub(subId){
    if(!subId){
        return "";
    }
    var opt = document.querySelector("#program-sub-combined option[value=\"s:" + subId + "\"]");
    if(!opt){
        return "";
    }
    var text = (opt.textContent || "");
    var programName = text.split(" / ")[0];
    var options = document.querySelectorAll("#program-sub-combined option");
    for(var i=0;i<options.length;i++){
        var o = options[i];
        if(o.value && o.value.indexOf("p:") === 0 && (o.textContent || "") === programName){
            return o.value.substring(2);
        }
    }
    return "";
}

function setHiddenFields(val){
    var parsed = parseProgramSubCombined(val);
    if(parsed.program_id){
        $("#juryrequirement-program_id").val(parsed.program_id);
        $("#juryrequirement-program_sub_id").val("");
    }else if(parsed.program_sub_id){
        $("#juryrequirement-program_sub_id").val(parsed.program_sub_id);
        $("#juryrequirement-program_id").val(resolveProgramIdForSub(parsed.program_sub_id));
    }else{
        $("#juryrequirement-program_id").val("");
        $("#juryrequirement-program_sub_id").val("");
    }
}

setHiddenFields($("#program-sub-combined").val());

$("#program-sub-combined").on("change", function(){
    setHiddenFields(this.value);
    var programId = $("#juryrequirement-program_id").val();
    var programSubId = $("#juryrequirement-program_sub_id").val();
    var url = "' . $baseUrl . '";
    if(programId){
        url += (url.indexOf("?") === -1 ? "?" : "&") + "program_id=" + encodeURIComponent(programId);
    }
    if(programSubId){
        url += (url.indexOf("?") === -1 ? "?" : "&") + "program_sub_id=" + encodeURIComponent(programSubId);
    }
    window.location.href = url;
});
');
