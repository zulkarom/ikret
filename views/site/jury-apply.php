<?php

use kartik\widgets\ActiveForm;
use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\JuryApplyForm $model */

$this->title = 'Call for Juries';

$juryMatrix = $juryMatrix ?? [];
$declarationText = "I hereby declare that all information provided is true and accurate. I acknowledge that appointment as a Jury for THE INTERNATIONAL CONVENTION ON RESOURCEFUL ENTREPRENEURS ACHIEVING TOMORROW'S EXCELLENCE (I-CREATE) is subject to a first-come, first-served basis and availability of positions, up to the required maximum for each selected competition, date, time, and venue. I understand that only SELECTED applicants will be notified via email.";

$this->registerCss(<<<CSS
.jury-apply {
    max-width: 1180px;
    margin: 0 auto;
}

.jury-apply__intro {
    background: linear-gradient(135deg, #0f766e 0%, #1d4ed8 100%);
    border-radius: 8px;
    color: #fff;
    margin-bottom: 18px;
    padding: 24px 28px;
}

.jury-apply__intro h2 {
    color: #fff;
    font-size: 26px;
    font-weight: 700;
    letter-spacing: 0;
    margin: 0 0 6px;
}

.jury-apply__intro p {
    color: rgba(255, 255, 255, .88);
    margin: 0;
}

.jury-apply .card {
    border: 1px solid #dde5ef;
    border-radius: 8px;
    box-shadow: 0 12px 34px rgba(15, 23, 42, .07);
}

.jury-apply .card + .card {
    margin-top: 18px;
}

.jury-apply .card-title {
    border-bottom: 1px solid #edf1f7;
    color: #0f172a;
    font-size: 18px;
    font-weight: 700;
    margin-bottom: 22px;
    padding-bottom: 14px;
}

.jury-apply .form-group {
    margin-bottom: 18px;
}

.jury-apply .control-label {
    color: #334155;
    display: block;
    font-size: 12px;
    font-weight: 700;
    letter-spacing: .04em;
    margin-bottom: 7px;
    text-transform: uppercase;
}

.jury-apply .help-block,
.jury-apply .hint-block {
    color: #64748b;
    font-size: 12px;
    margin: 6px 0 0;
}

.jury-apply .form-control {
    border-color: #cbd5e1;
    border-radius: 6px;
    min-height: 42px;
}

.jury-apply .form-control:focus {
    border-color: #0f766e;
    box-shadow: 0 0 0 .2rem rgba(15, 118, 110, .14);
}

.jury-apply__options {
    border: 1px solid #dde5ef;
    border-radius: 8px;
    overflow: hidden;
}

.jury-apply__option-row {
    display: grid;
    grid-template-columns: minmax(220px, 36%) 1fr;
}

.jury-apply__option-row + .jury-apply__option-row {
    border-top: 1px solid #edf1f7;
}

.jury-apply__program {
    background: #f8fafc;
    color: #0f172a;
    font-weight: 700;
    padding: 16px;
}

.jury-apply__sessions {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    padding: 14px;
}

.jury-apply__session {
    align-items: flex-start;
    background: #fff;
    border: 1px solid #dbe4ef;
    border-radius: 8px;
    display: flex;
    gap: 9px;
    line-height: 1.35;
    margin: 0;
    max-width: 100%;
    padding: 10px 12px;
}

.jury-apply__session .form-check-input {
    flex: 0 0 auto;
    margin-left: 0;
    margin-top: 3px;
    position: static;
}

.jury-apply__session .form-check-label {
    color: #334155;
    font-size: 13px;
}

.jury-apply__empty {
    color: #64748b;
    padding: 16px;
}

.jury-apply__declaration {
    background: #f8fafc;
    border: 1px solid #dbe4ef;
    border-radius: 8px;
    margin-top: 20px;
    padding: 14px 16px;
}

.jury-apply__declaration .form-group {
    margin-bottom: 0;
}

.jury-apply__declaration label {
    color: #334155;
    line-height: 1.55;
}

.jury-apply__submit {
    margin-top: 18px;
}

@media (max-width: 767.98px) {
    .jury-apply__intro {
        padding: 20px;
    }

    .jury-apply__option-row {
        grid-template-columns: 1fr;
    }

    .jury-apply__program {
        border-bottom: 1px solid #edf1f7;
    }
}
CSS);

?>

<div class="pagetitle">
    <h1><?=$this->title?></h1>
</div>

</div><!-- End Page Title -->

<section class="section">
    <div class="jury-apply">
    <div class="jury-apply__intro">
        <h2>Jury Application</h2>
        <p>Please complete your profile and choose your preferred competition and judging session.</p>
    </div>

    <?php $form = ActiveForm::begin(['options' => ['class' => 'jury-apply__form']]); ?>

    <div class="card">
        <div class="card-body pt-4">
            <h5 class="card-title">Profile</h5>

            <div class="row">
                <div class="col-md-6">
                    <?= $form->field($model, 'fullname')->textInput(['style' => 'text-transform: uppercase'])->label('FULL NAME (CAPITAL LETTER) WITH TITLE')->hint('Prof/ Prof Madya/ Dr/ Dato/ Datin/ Encik/ Puan/ Cik') ?>
                    <?= $form->field($model, 'email')->textInput(['type' => 'email'])->label('Email Address') ?>
                    <?= $form->field($model, 'category')->radioList(['ACADEMIC' => 'ACADEMIC', 'INDUSTRY' => 'INDUSTRY'], ['inline' => true])->label('CATEGORY OF JURY') ?>
                    <?= $form->field($model, 'phone')->textInput()->label('CONTACT NUMBER') ?>
                </div>
                <div class="col-md-6">
                    <?= $form->field($model, 'institution')->textInput()->label('ORGANIZATION / INSTITUTION') ?>
                    <?= $form->field($model, 'designation')->textInput()->label('CURRENT DESIGNATION (IN YOUR ORGANIZATION)') ?>
                    <?= $form->field($model, 'address')->textarea(['rows' => 4])->label("ORGANIZATION/ INSTITUTION'S FULL ADDRESS") ?>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body pt-4">
            <h5 class="card-title">SELECT YOUR PREFERRED COMPETITION & JUDGING SESSION</h5>

            <?= Html::error($model, 'requirement_ids', ['class' => 'invalid-feedback d-block']) ?>

            <div class="jury-apply__options">
                <?php if($juryMatrix): ?>
                    <?php foreach($juryMatrix as $row): ?>
                        <div class="jury-apply__option-row">
                            <div class="jury-apply__program"><?= Html::encode($row['label'] ?? '') ?></div>
                            <div class="jury-apply__sessions">
                                <?php if(!empty($row['sessions'])): ?>
                                    <?php foreach($row['sessions'] as $s): ?>
                                        <?php
                                        $rid = (int)($s['requirement_id'] ?? 0);
                                        $disabled = !empty($s['disabled']);
                                        $used = (int)($s['used'] ?? 0);
                                        $limit = $s['limit'] ?? null;
                                        $sessionName = $s['session_name'] ?? '';
                                        $text = $sessionName;
                                        if($limit === null){
                                            $text .= ' (Unlimited)';
                                        }else{
                                            $avail = (int)$limit - (int)$used;
                                            if($avail < 0){
                                                $avail = 0;
                                            }
                                            $text .= ' (Available: ' . $avail . ')';
                                        }
                                        ?>

                                        <div class="form-check jury-apply__session">
                                            <input
                                                class="form-check-input"
                                                type="checkbox"
                                                name="JuryApplyForm[requirement_ids][]"
                                                id="req-<?= Html::encode((string)$rid) ?>"
                                                value="<?= Html::encode((string)$rid) ?>"
                                                <?= in_array($rid, (array)$model->requirement_ids, true) ? 'checked' : '' ?>
                                                <?= $disabled ? 'disabled' : '' ?>
                                            >
                                            <label class="form-check-label" for="req-<?= Html::encode((string)$rid) ?>"><?= Html::encode($text) ?></label>
                                        </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <span class="text-muted">No sessions configured</span>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="jury-apply__empty">No open jury application options.</div>
                <?php endif; ?>
            </div>

            <div class="jury-apply__declaration">
                <?= $form->field($model, 'declaration_accepted')->checkbox()->label($declarationText) ?>
            </div>

            <div class="form-group jury-apply__submit">
                <?= Html::submitButton('Submit Application', ['class' => 'btn btn-primary btn-lg']) ?>
            </div>
        </div>
    </div>

    <?php ActiveForm::end(); ?>
    </div>
</section>
