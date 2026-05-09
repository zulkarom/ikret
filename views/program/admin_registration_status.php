<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\Program[] $programs */

$this->title = 'All Program/Sub';
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
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(empty($programs)){ ?>
                            <tr>
                                <td colspan="2" class="text-muted">No programs found.</td>
                            </tr>
                        <?php } ?>
                        <?php foreach($programs as $program){ ?>
                            <?php
                            $isClosed = (int)$program->getAttribute('reg_closed') === 1;
                            $statusClass = $isClosed ? 'text-danger' : 'text-success';
                            $statusText = $isClosed ? 'Closed' : 'Open';
                            ?>
                            <tr>
                                <td>
                                    <strong><?= Html::encode($program->program_name) ?></strong>
                                    <?php if(!empty($program->program_abbr)){ ?>
                                        <div class="text-muted small"><?= Html::encode($program->program_abbr) ?></div>
                                    <?php } ?>
                                    <?php if((int)$program->has_sub === 1 && $program->programSubs){ ?>
                                        <div class="small mt-2">
                                            <?php foreach($program->programSubs as $sub){ ?>
                                                <div><?= Html::encode($sub->sub_name) ?></div>
                                            <?php } ?>
                                        </div>
                                    <?php } ?>
                                </td>
                                <td>
                                    <?= Html::beginForm(['program/admin-registration-status'], 'post', ['class' => 'd-flex align-items-center gap-2']) ?>
                                        <?= Html::hiddenInput('program_id', $program->id) ?>
                                        <?= Html::hiddenInput('reg_closed', $isClosed ? 0 : 1) ?>
                                        <span class="<?= $statusClass ?>"><?= $statusText ?></span>
                                        <?= Html::submitButton($isClosed ? 'Open Registration' : 'Close Registration', [
                                            'class' => $isClosed ? 'btn btn-success btn-sm' : 'btn btn-outline-danger btn-sm',
                                        ]) ?>
                                    <?= Html::endForm() ?>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>
