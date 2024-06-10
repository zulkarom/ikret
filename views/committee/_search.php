<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\ProgramRegistrationSearch $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="program-registration-search">

    <?php $form = ActiveForm::begin([
        'action' => ['request'],
        'method' => 'get',
        'id' => 'form-role'
    ]); ?>

    <div class="row">
        <div class="col-md-6"><?= $form->field($model, 'fullname')->textInput(['placeholder' => 'Search Name'])->label(false)?></div>
        <div class="col-md-3">
        <?= $form->field($model, 'status')->dropDownList($model->statusArray,['prompt' => 'Select Status'])->label(false)?>
        </div>
        <div class="col-md-3">
        <?= $form->field($model, 'role_name')->dropDownList($model->listRoles(),['prompt' => 'Select Role'])->label(false)?>
        </div>
    </div>
<br />
    


    <?php ActiveForm::end(); ?>

</div>

<?php 

$this->registerJs('

$("#rolerequestsearch-status").change(function(){
    $("#form-role").submit();
});

$("#rolerequestsearch-role_name").change(function(){
    $("#form-role").submit();
});

');


?>