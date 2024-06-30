<?php

use yii\bootstrap5\ActiveForm;
use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\ProgramRegistrationSearch $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="program-registration-search">

    <?php 
    $url = [$action, 'id' => $model->program_id];
    //buat utk sub
    if($programSub){
        $url = [$action, 'id' => $model->program_id, 'sub' => $programSub->id];
    }
    
    $form = ActiveForm::begin([
        'action' => $url,
        'method' => 'get',
    ]); ?>
    <?= $form->field($model, 'fullnameSearch')->textInput(['placeholder' => 'Search Participant'])->label(false) ?>
    <div class="row">
        <div class="col-md-4">
        <?= $form->field($model, 'group_code')->textInput(['placeholder' => 'Search Group ID'])->label(false) ?>
        </div>
        <div class="col-md-4">
        <?= $form->field($model, 'group_name')->textInput(['placeholder' => 'Search Group Name'])->label(false) ?>
        </div>
        <div class="col-md-4">
        <?= $form->field($model, 'booth_number')->textInput(['placeholder' => 'Search Booth Number'])->label(false) ?>
        </div>
    </div>
<br />
    <div class="form-group">
        <?= Html::submitButton('Apply Filter', ['class' => 'btn btn-primary']) ?>
     
    </div>

    <?php ActiveForm::end(); ?>

</div>
