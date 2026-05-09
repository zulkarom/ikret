<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\Program[] $programs */
/** @var array $subsByProgram */
/** @var bool $hasProgramEditDeadline */
/** @var app\models\Setting|null $setting */

$this->title = 'All Program/Sub';
$subsByProgram = $subsByProgram ?? [];
$hasProgramEditDeadline = $hasProgramEditDeadline ?? false;
$setting = $setting ?? null;
?>

<div class="pagetitle">
    <h1><?= Html::encode($this->title) ?></h1>
</div>

<section class="section dashboard">
    <div class="card">
        <div class="card-body pt-4">
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th>Program/Sub</th>
                            <th style="width: 220px;">Registration</th>
                            <th style="width: 280px;">Last Date to Edit Registration</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(empty($programs)){ ?>
                            <tr>
                                <td colspan="3" class="text-muted">No programs found.</td>
                            </tr>
                        <?php } ?>
                        <?php foreach($programs as $program){ ?>
                            <?php
                            $isClosed = (int)$program->getAttribute('reg_closed') === 1;
                            $statusClass = $isClosed ? 'text-danger' : 'text-success';
                            $statusText = $isClosed ? 'Closed' : 'Open';
                            $subs = $subsByProgram[(int)$program->id] ?? [];
                            ?>
                            <tr>
                                <td>
                                    <strong><?= Html::encode($program->program_name) ?></strong>
                                    <?php if(!empty($program->program_abbr)){ ?>
                                        <div class="text-muted small"><?= Html::encode($program->program_abbr) ?></div>
                                    <?php } ?>
                                    <?php if((int)$program->has_sub === 1 && $subs){ ?>
                                        <div class="small mt-2">
                                            <?php foreach($subs as $sub){ ?>
                                                <div><?= Html::encode($sub->sub_name) ?></div>
                                            <?php } ?>
                                        </div>
                                    <?php } ?>
                                </td>
                                <td>
                                    <?= Html::beginForm(['program/admin-registration-status'], 'post', ['class' => 'd-flex align-items-center gap-2']) ?>
                                        <?= Html::hiddenInput('program_id', $program->id) ?>
                                        <?= Html::hiddenInput('action', 'registration-status') ?>
                                        <?= Html::hiddenInput('reg_closed', $isClosed ? 0 : 1) ?>
                                        <span class="<?= $statusClass ?>"><?= $statusText ?></span>
                                        <?= Html::submitButton($isClosed ? 'Open Registration' : 'Close Registration', [
                                            'class' => $isClosed ? 'btn btn-success btn-sm' : 'btn btn-outline-danger btn-sm',
                                        ]) ?>
                                    <?= Html::endForm() ?>
                                </td>
                                <td>
                                    <?php if($hasProgramEditDeadline){ ?>
                                        <?= Html::beginForm(['program/admin-registration-status'], 'post', ['class' => 'd-flex align-items-center gap-2']) ?>
                                            <?= Html::hiddenInput('program_id', $program->id) ?>
                                            <?= Html::hiddenInput('action', 'edit-deadline') ?>
                                            <?= Html::input('date', 'allow_edit_reg_until', $program->getAttribute('allow_edit_reg_until'), ['class' => 'form-control form-control-sm']) ?>
                                            <?= Html::submitButton('Save', ['class' => 'btn btn-outline-primary btn-sm']) ?>
                                        <?= Html::endForm() ?>
                                    <?php }else{ ?>
                                        <span class="text-muted">Database column not available.</span>
                                    <?php } ?>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="card mt-3">
        <div class="card-body pt-4">
            <h5 class="card-title">General Last Date to Edit Registration</h5>
            <?= Html::beginForm(['program/admin-registration-status'], 'post', ['class' => 'row g-2 align-items-end']) ?>
                <?= Html::hiddenInput('action', 'general-edit-deadline') ?>
                <div class="col-12 col-md-4">
                    <label class="form-label">Last Date</label>
                    <?= Html::input('date', 'allow_edit_reg_until', $setting ? $setting->allow_edit_reg_until : null, ['class' => 'form-control']) ?>
                </div>
                <div class="col-12 col-md-auto">
                    <?= Html::submitButton('Save General Setting', ['class' => 'btn btn-primary']) ?>
                </div>
            <?= Html::endForm() ?>
        </div>
    </div>

</section>
