<?php

use kartik\widgets\ActiveForm;
use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\JuryApplyForm $model */

$this->title = 'Call for Juries';

$juryMatrix = $juryMatrix ?? [];

?>

<div class="pagetitle">
    <h1><?=$this->title?></h1>
</div>

</div><!-- End Page Title -->

<section class="section">
    <?php $form = ActiveForm::begin(); ?>

    <div class="card">
        <div class="card-body pt-4">
            <h5 class="card-title">Profile</h5>

            <div class="row">
                <div class="col-md-6">
                    <?= $form->field($model, 'email')->textInput(['type' => 'email']) ?>
                    <?= $form->field($model, 'fullname')->textInput() ?>
                    <?= $form->field($model, 'category')->radioList(['ACADEMIC' => 'ACADEMIC', 'INDUSTRY' => 'INDUSTRY'], ['inline' => true]) ?>
                    <?= $form->field($model, 'phone')->textInput() ?>
                </div>
                <div class="col-md-6">
                    <?= $form->field($model, 'institution')->textInput() ?>
                    <?= $form->field($model, 'designation')->textInput() ?>
                    <?= $form->field($model, 'address')->textarea(['rows' => 6]) ?>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body pt-4">
            <h5 class="card-title">Program Related</h5>

            <?= $form->field($model, 'requirement_ids')->error() ?>

            <div class="table-responsive">
                <table class="table table-striped table-bordered">
                    <thead>
                    <tr>
                        <th style="width:40%">Program / Sub Program</th>
                        <th>Sessions</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php if($juryMatrix): ?>
                        <?php foreach($juryMatrix as $row): ?>
                            <tr>
                                <td><?= Html::encode($row['label'] ?? '') ?></td>
                                <td>
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

                                            <div class="form-check form-check-inline">
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
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="2" class="text-muted">No open jury application options.</td>
                        </tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <?= $form->field($model, 'declaration_accepted')->checkbox()->label('I hereby declare that all information provided is true and accurate.') ?>

            <div class="form-group">
                <?= Html::submitButton('Submit Application', ['class' => 'btn btn-primary']) ?>
            </div>
        </div>
    </div>

    <?php ActiveForm::end(); ?>
</section>
