<?php

use yii\bootstrap5\ActiveForm;
use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\ProgramRegistrationSearch $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="program-registration-search">

    <?php $form = ActiveForm::begin([
        'action' => ['manager', 'id' => $model->program_id],
        'method' => 'get',
    ]); ?>
    <?= $form->field($model, 'fullnameSearch')->textInput(['placeholder' => 'Search Participant'])->label(false) ?>
    
<br />
    <div class="form-group">
        <?= Html::submitButton('Apply Filter', ['class' => 'btn btn-primary']) ?>
     
    </div>

    <?php ActiveForm::end(); ?>

</div>
