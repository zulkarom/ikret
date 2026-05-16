<?php

use app\models\CertificateTemplate;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\CertificateTemplate $model */

$this->title = 'Update Certificate Config';

$textConfig = [
    'section' => 'Additional Text',
    'name' => 'Recipient / Member Names',
    'fields' => [
        1 => ['label' => 'Additional Text 1', 'hint' => 'Vertical position/top spacing for secondary text.'],
        2 => ['label' => 'Additional Text 2', 'hint' => 'Vertical position/top spacing for additional text.'],
        3 => ['label' => 'Additional Text 3', 'hint' => 'Vertical position/top spacing for third text.'],
    ],
];

if ((int)$model->id === 4) {
    $textConfig = [
        'section' => 'Achievement Certificate Text',
        'name' => 'Participant / Group Member Names',
        'fields' => [
            1 => ['label' => 'Achievement Sentence', 'hint' => 'Example: have achieved FIRST PLACE in.'],
            2 => ['label' => 'Achievement Name', 'hint' => 'Example: THE MOST ATTRACTIVE BOOTH COMPETITION.'],
            3 => ['label' => 'Program / Sub Program Name', 'hint' => 'Program and sub program printed below the achievement name.'],
        ],
    ];
} elseif ((int)$model->id === 5) {
    $textConfig = [
        'section' => 'Medal Certificate Text',
        'name' => 'Participant / Group Member Names',
        'fields' => [
            1 => ['label' => 'Medal Award Name', 'hint' => 'Example: GOLD AWARD, SILVER AWARD, or BRONZE AWARD.'],
            2 => ['label' => 'Program / Sub Program Name', 'hint' => 'Program and sub program printed below the medal award.'],
        ],
    ];
} elseif ((int)$model->id === 7) {
    $textConfig = [
        'section' => 'Session Certificate Text',
        'name' => 'Participant Name',
        'fields' => [
            1 => ['label' => 'Session Name and Speaker', 'hint' => 'Session name and speaker line.'],
            2 => ['label' => 'Program Name', 'hint' => 'Program name printed below the session details.'],
        ],
    ];
}

$renderTextFieldPair = static function($form, $model, $fieldNo, $config){
    $topAttribute = 'field' . $fieldNo . '_mt';
    $sizeAttribute = 'field' . $fieldNo . '_size';
    $label = $config['label'];
    $hint = $config['hint'];

    return '<div class="col-md-3">'
        . $form->field($model, $topAttribute)->textInput(['type' => 'number', 'step' => '0.1'])->label($label . ' Top')->hint($hint)
        . '</div><div class="col-md-3">'
        . $form->field($model, $sizeAttribute)->textInput(['type' => 'number', 'step' => '0.1'])->label($label . ' Size')
        . '</div>';
};
?>

<div class="certificate-template-update">
    <div class="row">
        <div class="col-lg-9">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title"><?= Html::encode(ucwords((string)$model->template_name)) ?></h5>

                    <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>

                    <?= $form->errorSummary($model, ['class' => 'alert alert-danger']) ?>

                    <div class="form-group mb-3">
                        <?= Html::submitButton('Save', ['class' => 'btn btn-success', 'name' => 'submit_action', 'value' => 'save']) ?>
                        <?= Html::submitButton('Save and Back', ['class' => 'btn btn-primary', 'name' => 'submit_action', 'value' => 'save-back']) ?>
                        <?= Html::a('Back', ['index'], ['class' => 'btn btn-secondary']) ?>
                    </div>

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

                    <?php if($model->hasAttribute('hide_background')): ?>
                        <div class="row">
                            <div class="col-md-4">
                                <?= $form->field($model, 'hide_background')->checkbox()->hint('Generate certificate text and overlays without the uploaded background image.') ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($model->template_file)): ?>
                        <div class="mb-3">
                            <div class="text-muted mb-1">Current template</div>
                            <?= Html::img(Yii::getAlias('@web') . '/images/' . ltrim($model->template_file, '/'), ['style' => 'max-width:100%; max-height:260px; object-fit:contain; border:1px solid #ddd;']) ?>
                            <div class="small text-muted mt-1"><?= Html::encode($model->template_file) ?></div>
                        </div>
                    <?php endif; ?>

                    <hr>

                    <h5 class="card-title"><?= Html::encode($textConfig['name']) ?></h5>
                    <div class="row">
                        <div class="<?= $model->hasAttribute('name_limit_y') ? 'col-md-3' : 'col-md-4' ?>">
                            <?= $form->field($model, 'margin_left')->textInput(['type' => 'number', 'step' => '0.1'])->hint('Horizontal X position.') ?>
                        </div>
                        <div class="<?= $model->hasAttribute('name_limit_y') ? 'col-md-3' : 'col-md-4' ?>">
                            <?= $form->field($model, 'name_mt')->textInput(['type' => 'number', 'step' => '0.1'])->label($textConfig['name'] . ' Top')->hint('Vertical position/top spacing for names.') ?>
                        </div>
                        <?php if($model->hasAttribute('name_limit_y')): ?>
                            <div class="col-md-3">
                                <?= $form->field($model, 'name_limit_y')->textInput(['type' => 'number', 'step' => '0.1'])->hint('Lowest Y position allowed for wrapped names. Leave blank to use the first text field top.') ?>
                            </div>
                        <?php endif; ?>
                        <div class="<?= $model->hasAttribute('name_limit_y') ? 'col-md-3' : 'col-md-4' ?>">
                            <?= $form->field($model, 'name_size')->textInput(['type' => 'number', 'step' => '0.1'])->label($textConfig['name'] . ' Size') ?>
                        </div>
                    </div>
                    <?php if($model->hasAttribute('show_name_border')): ?>
                        <div class="row">
                            <div class="col-md-4">
                                <?= $form->field($model, 'show_name_border')->checkbox()->hint('Development aid: draws a red box around the allowed name area in generated PDFs.') ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <h5 class="card-title"><?= Html::encode($textConfig['section']) ?></h5>
                    <div class="row">
                        <?php foreach($textConfig['fields'] as $fieldNo => $fieldConfig): ?>
                            <?= $renderTextFieldPair($form, $model, $fieldNo, $fieldConfig) ?>
                        <?php endforeach; ?>
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
