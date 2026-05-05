<?php

use kartik\widgets\ActiveForm;
use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\JuryRequirement $model */
/** @var array $programList */
/** @var array $subList */
/** @var array $sessionList */

$programList = $programList ?? [];
$subList = $subList ?? [];
$sessionList = $sessionList ?? [];

$form = ActiveForm::begin();

echo $form->field($model, 'program_id')->dropDownList($programList, ['prompt' => 'Please select']);

echo $form->field($model, 'program_sub_id')->dropDownList($subList, ['prompt' => 'N/A']);

echo $form->field($model, 'judging_session_id')->dropDownList($sessionList, ['prompt' => 'N/A']);

echo $form->field($model, 'is_required')->dropDownList([1 => 'YES', 0 => 'NO']);

echo $form->field($model, 'is_active')->dropDownList([1 => 'OPEN', 0 => 'CLOSED']);

echo $form->field($model, 'jury_limit')->textInput();

echo Html::submitButton('Save', ['class' => 'btn btn-primary']);

ActiveForm::end();
