<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = 'Registration Fields';
$programSub = $programSub ?? null;
$sub = $sub ?? null;
$fieldTypes = \app\models\ProgramRegistration::availableRegistrationFieldTypes();

$this->params['breadcrumbs'][] = [
    'label' => $program->program_abbr . ($programSub ? ' / ' . $programSub->sub_abbr : ''),
    'url' => ['/program-registration/manager-dashboard', 'id' => $program->id, 'sub' => $programSub ? $programSub->id : null]
];
$this->params['breadcrumbs'][] = $this->title;

$this->registerCss(<<<CSS
.field-toggle-card {
    border: 0;
    border-radius: 20px;
    box-shadow: 0 14px 28px rgba(16, 37, 66, 0.08);
    overflow: hidden;
}
.field-toggle-head {
    background: linear-gradient(135deg, #12355b 0%, #1d5d8f 100%);
    color: #fff;
    padding: 1.25rem 1.5rem;
}
.field-toggle-grid {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 1rem;
}
.field-toggle-item {
    border: 1px solid #dde7f1;
    border-radius: 16px;
    padding: 1rem 1.1rem;
    background: linear-gradient(180deg, #ffffff 0%, #f7fafc 100%);
}
.field-toggle-label {
    color: #12355b;
    font-weight: 700;
    margin-bottom: .2rem;
}
.field-toggle-key {
    color: #6f8398;
    font-size: .83rem;
}
.field-toggle-type {
    display: inline-flex;
    align-items: center;
    margin-top: .45rem;
    padding: .18rem .55rem;
    border-radius: 999px;
    background: #e9f2fb;
    color: #17466f;
    font-size: .74rem;
    font-weight: 600;
}
.field-toggle-switch .form-check-input {
    width: 2.9rem;
    height: 1.45rem;
}
@media (max-width: 767.98px) {
    .field-toggle-grid {
        grid-template-columns: 1fr;
    }
}
CSS);

?>

<div class="program-reg-field-index">

    <div class="card field-toggle-card">
        <div class="field-toggle-head">
            <h4 class="mb-1"><?=$this->title?></h4>
            <div class="opacity-75">
                <?= Html::encode($program->program_name) ?>
                <?php if($programSub){ ?>
                    / <?= Html::encode($programSub->sub_name) ?>
                <?php } ?>
            </div>
        </div>
        <div class="card-body pt-4">
            <?php $form = ActiveForm::begin(); ?>

            <div class="mb-3 text-muted">
                Available fields in the system. Toggle each field on or off for this program.
            </div>

            <div class="field-toggle-grid">
                <?php foreach($available as $fieldName => $label){
                    $row = array_key_exists($fieldName, $existing) ? $existing[$fieldName] : null;
                    $enabled = $row ? (int)$row->is_enabled === 1 : 0;
                ?>
                    <div class="field-toggle-item">
                        <div class="d-flex align-items-start justify-content-between gap-3">
                            <div>
                                <div class="field-toggle-label"><?=Html::encode($label)?></div>
                                <div class="field-toggle-key"><?=Html::encode($fieldName)?></div>
                                <div class="field-toggle-type"><?=Html::encode($fieldTypes[$fieldName] ?? 'Input')?></div>
                            </div>
                            <div class="form-check form-switch field-toggle-switch m-0">
                                <input class="form-check-input" type="checkbox" name="Field[enabled][<?=$fieldName?>]" value="1" <?=$enabled ? 'checked' : ''?>>
                            </div>
                        </div>
                    </div>
                <?php } ?>
            </div>

            <div class="form-group">
                <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
            </div>

            <?php ActiveForm::end(); ?>

        </div>
    </div>

</div>
