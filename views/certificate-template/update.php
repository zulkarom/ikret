<?php

use app\models\CertificateTemplate;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\CertificateTemplate $model */

$this->title = 'Update Certificate Config';

$field1Hint = 'Vertical position/top spacing for secondary text.';
$field2Hint = 'Vertical position/top spacing for additional text.';
if ((int)$model->id === 7) {
    $field1Hint = 'Vertical position/top spacing for session name and speaker.';
    $field2Hint = 'Vertical position/top spacing for program name.';
}
?>

<div class="certificate-template-update">
    <div class="row">
        <div class="col-lg-9">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title"><?= Html::encode(ucwords((string)$model->template_name)) ?></h5>

                    <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>

                    <?= $form->errorSummary($model, ['class' => 'alert alert-danger']) ?>

                    <div class="row">
                        <div class="col-md-6">
                            <?= $form->field($model, 'template_name')->textInput(['maxlength' => true]) ?>
                        </div>
                        <div class="col-md-3">
                            <?= $form->field($model, 'is_portrait')->dropDownList(CertificateTemplate::orientationOptions()) ?>
                        </div>
                        <div class="col-md-3">
                            <?= $form->field($model, 'align')->dropDownList(CertificateTemplate::alignOptions()) ?>
                        </div>
                    </div>

                    <?= $form->field($model, 'template_upload')->fileInput()->hint('Upload JPG or PNG. Current file is kept when no new file is selected.') ?>

                    <?php if (!empty($model->template_file)): ?>
                        <div class="mb-3">
                            <div class="text-muted mb-1">Current template</div>
                            <?= Html::img(Yii::getAlias('@web') . '/images/' . ltrim($model->template_file, '/'), ['style' => 'max-width:100%; max-height:260px; object-fit:contain; border:1px solid #ddd;']) ?>
                            <div class="small text-muted mt-1"><?= Html::encode($model->template_file) ?></div>
                        </div>
                    <?php endif; ?>

                    <hr>

                    <h5 class="card-title">Name Text</h5>
                    <div class="row">
                        <div class="col-md-4">
                            <?= $form->field($model, 'margin_left')->textInput(['type' => 'number', 'step' => '0.1'])->hint('Horizontal X position.') ?>
                        </div>
                        <div class="col-md-4">
                            <?= $form->field($model, 'name_mt')->textInput(['type' => 'number', 'step' => '0.1'])->hint('Vertical position/top spacing for the name.') ?>
                        </div>
                        <div class="col-md-4">
                            <?= $form->field($model, 'name_size')->textInput(['type' => 'number', 'step' => '0.1']) ?>
                        </div>
                    </div>

                    <h5 class="card-title">Other Text</h5>
                    <div class="row">
                        <div class="col-md-3">
                            <?= $form->field($model, 'field1_mt')->textInput(['type' => 'number', 'step' => '0.1'])->hint($field1Hint) ?>
                        </div>
                        <div class="col-md-3">
                            <?= $form->field($model, 'field1_size')->textInput(['type' => 'number', 'step' => '0.1']) ?>
                        </div>
                        <div class="col-md-3">
                            <?= $form->field($model, 'field2_mt')->textInput(['type' => 'number', 'step' => '0.1'])->hint($field2Hint) ?>
                        </div>
                        <div class="col-md-3">
                            <?= $form->field($model, 'field2_size')->textInput(['type' => 'number', 'step' => '0.1']) ?>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <?= $form->field($model, 'margin_right')->textInput(['type' => 'number', 'step' => '0.1']) ?>
                        </div>
                        <div class="col-md-4">
                            <?= $form->field($model, 'published')->checkbox() ?>
                        </div>
                    </div>

                    <div class="form-group">
                        <?= Html::submitButton('Save', ['class' => 'btn btn-success', 'name' => 'submit_action', 'value' => 'save']) ?>
                        <?= Html::submitButton('Save and Back', ['class' => 'btn btn-primary', 'name' => 'submit_action', 'value' => 'save-back']) ?>
                        <?= Html::a('Back', ['index'], ['class' => 'btn btn-secondary']) ?>
                    </div>

                    <?php ActiveForm::end(); ?>
                </div>
            </div>
        </div>
    </div>
</div>
