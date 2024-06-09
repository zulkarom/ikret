<?php

use app\models\Program;
use kartik\datetime\DateTimePicker;
use kartik\form\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;

/** @var yii\web\View $this */
/** @var app\models\Session $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="card">
<div class="card-body pt-4">
        <div class="session-form">

<?php $form = ActiveForm::begin(); ?>

<?= $form->field($model, 'session_name')->textInput() ?>

<?=$form->field($model, 'datetime_start')->widget(DateTimePicker::classname(), [
        'options' => ['placeholder' => 'Enter start time ...'],
        'pickerIcon' => '<i class="bi bi-calendar2-range"></i>',
        'removeIcon' => '<i class="bi bi-x-circle"></i>',
        'pluginOptions' => [
            'autoclose' => true
        ]
    ]);
    ?>

<?=$form->field($model, 'datetime_end')->widget(DateTimePicker::classname(), [
        'options' => ['placeholder' => 'Enter end time ...'],
        'pickerIcon' => '<i class="bi bi-calendar2-range"></i>',
        'removeIcon' => '<i class="bi bi-x-circle"></i>',
        'pluginOptions' => [
            'autoclose' => true
        ]
    ]);
    ?>

<?= $form->field($model, 'program_id')->dropDownList(Program::listPrograms(), ['prompt' => 'Select Program','onchange'=>'
                         $.get("'.Url::to(['/user/sub-program-options', 'program' => '']).
                       '"+$(this).val(),function( data ) 
                        {$( "select#session-program_sub" ).html( data );
                             });'])->label("Program (if any)") ?>


<?= $form->field($model, 'program_sub')->dropDownList([0=> ''],['prompt' => 'Select Competition'])->label("Competition (if any)") ?>




<div class="form-group">
    <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
</div>

<?php ActiveForm::end(); ?>

</div>
        </div>
    </div>


