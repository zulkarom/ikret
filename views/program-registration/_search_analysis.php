<?php

use yii\bootstrap5\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\ProgramRegistrationSearch $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="program-registration-search">
    

    <?php
    $url = [$action, 'id' => $model->program_id];
    if($programSub){
        $url = [$action, 'id' => $model->program_id, 'sub' => $programSub->id];
    }
    $form = ActiveForm::begin([
        'action' => $url,
        'method' => 'get',
    ]); ?>
    <?= $form->field($model, 'fullnameSearch')->textInput(['placeholder' => 'Search Participant'])->label(false) ?>
    <div class="row">
        <div class="col-md-6"><?= $form->field($model, 'rubric')->dropDownList(ArrayHelper::map($rubrics, 'id', 'rubric.rubric_name'))->label(false) ?></div>

        <div class="col-md-6"><?= Html::submitButton('Apply Filter', ['class' => 'btn btn-primary']) ?></div>
 
    </div>


    <?php ActiveForm::end(); ?>

</div>
