<?php

use dosamigos\tinymce\TinyMce;
use kartik\date\DatePicker;
use yii\bootstrap5\ActiveForm;
use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\ProgramRegistration $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="program-registration-form">

    <?php $form = ActiveForm::begin(); ?>


    <?= $form->field($model, 'program_name')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'program_abbr')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'reg_info')->widget(TinyMce::className(), [
    'options' => ['rows' => 14],
    'language' => 'en',
    'clientOptions' => [
        'plugins' => [
            "advlist autolink lists link charmap",
            "searchreplace visualblocks code fullscreen",
            "paste"
        ],
        'menubar' => false,
        'toolbar' => "undo redo | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent"
    ]
]);?>

<div class="row">
    <div class="col-md-6">
    <?=$form->field($model, 'date_start')->widget(DatePicker::classname(), [
    'removeButton' => false,
    'pickerIcon' => '<i class="bi bi-calendar"></i>',
    'pluginOptions' => [
        'autoclose'=>true,
        'format' => 'yyyy-mm-dd',
        'todayHighlight' => true,
        
    ],
]);
?>
    </div>
    <div class="col-md-6">
    <?=$form->field($model, 'date_end')->widget(DatePicker::classname(), [
    'removeButton' => false,
    'pickerIcon' => '<i class="bi bi-calendar"></i>',
    'pluginOptions' => [
        'autoclose'=>true,
        'format' => 'yyyy-mm-dd',
        'todayHighlight' => true,
        
    ],
]);
?>
    </div>
</div>

    <br>
    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
