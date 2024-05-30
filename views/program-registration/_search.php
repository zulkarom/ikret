<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\ProgramRegistrationSearch $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="program-registration-search">

    <?php $form = ActiveForm::begin([
        'action' => ['manager', 'id' => $model->programx_id],
        'method' => 'get',
    ]); ?>
    <?= $form->field($model, 'fullnameSearch') ?>
<br />
    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
     
    </div>

    <?php ActiveForm::end(); ?>

</div>
