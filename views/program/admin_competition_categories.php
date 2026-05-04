<?php

use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'Program Sub Programs';

$programTable = Yii::$app->db->schema->getTableSchema(app\models\Program::tableName());
$hasStatus = $programTable && $programTable->getColumn('status');
$hasIsActive = $programTable && $programTable->getColumn('is_active');
$showEnableCol = $hasStatus || $hasIsActive;

$subTable = Yii::$app->db->schema->getTableSchema(app\models\ProgramSub::tableName());
$subHasIsActive = $subTable && $subTable->getColumn('is_active');

?>
<div class="pagetitle">
<h1><?=$this->title?></h1></div>

</div><!-- End Page Title -->

<section class="section dashboard">

<div class="card mb-3">
    <div class="card-header">Add Program</div>
    <div class="card-body pt-3">
        <?= Html::beginForm(Url::to(['program/admin-program-add']), 'post', ['class' => 'row g-2', 'style' => 'max-width: 720px;']) ?>
        <?= Html::hiddenInput(Yii::$app->request->csrfParam, Yii::$app->request->getCsrfToken()) ?>
        <div class="col-12 col-md-7">
            <input type="text" name="program_name" class="form-control" placeholder="Program name" required>
        </div>
        <div class="col-12 col-md-3">
            <input type="text" name="program_abbr" class="form-control" placeholder="Abbreviation (optional)">
        </div>
        <div class="col-12 col-md-2">
            <button type="submit" class="btn btn-primary w-100">Add</button>
        </div>
        <?= Html::endForm() ?>
    </div>
</div>

<div class="card">
    <div class="card-body pt-4">
        <div class="table-responsive">
            <table class="table">
                <tbody>
                    <tr>
                        <th style="width: 40px;">No.</th>
                        <th>Program</th>
                        <?php if($showEnableCol){ ?>
                        <th style="width: 220px;">Enable Program</th>
                        <?php } ?>
                        <th>Sub Programs</th>
                    </tr>
                    <?php $i = 1; foreach($programs as $program){ ?>
                        <tr>
                            <td><?=$i?>.</td>
                            <td>
                                <?= Html::beginForm(Url::to(['program/admin-program-update-name', 'id' => $program->id]), 'post', ['class' => 'row g-2', 'style' => 'max-width: 560px; margin:0;']) ?>
                                <?= Html::hiddenInput(Yii::$app->request->csrfParam, Yii::$app->request->getCsrfToken()) ?>
                                <div class="col-9">
                                    <input type="text" name="program_name" class="form-control form-control-sm" value="<?= Html::encode($program->program_name) ?>">
                                </div>
                                <div class="col-3">
                                    <button type="submit" class="btn btn-sm btn-primary w-100">Save</button>
                                </div>
                                <?= Html::endForm() ?>
                                <?php if($program->program_abbr){ ?>
                                    <br /><span class="text-muted small"><?= Html::encode($program->program_abbr) ?></span>
                                <?php } ?>
                            </td>
                            <?php if($showEnableCol){ ?>
                            <td>
                                <?php
                                    $enabled = true;
                                    if($hasStatus){
                                        $enabled = ((int)$program->getAttribute('status') === 10);
                                    }else if($hasIsActive){
                                        $enabled = ((int)$program->getAttribute('is_active') === 1);
                                    }
                                ?>
                                <?= Html::beginForm(Url::to(['program/admin-program-subs']), 'post', ['style' => 'margin:0;']) ?>
                                <?= Html::hiddenInput(Yii::$app->request->csrfParam, Yii::$app->request->getCsrfToken()) ?>
                                <?= Html::hiddenInput('toggle_active', 1) ?>
                                <?= Html::hiddenInput('program_id', (int)$program->id) ?>
                                <?php if($hasStatus){ ?>
                                    <input type="hidden" name="status" value="0">
                                <?php }else if($hasIsActive){ ?>
                                    <input type="hidden" name="is_active" value="0">
                                <?php } ?>
                                <div class="form-check form-switch">
                                    <?php if($hasStatus){ ?>
                                        <input class="form-check-input" type="checkbox" name="status" value="10" <?=$enabled ? 'checked' : ''?> onchange="this.form.submit()">
                                    <?php }else if($hasIsActive){ ?>
                                        <input class="form-check-input" type="checkbox" name="is_active" value="1" <?=$enabled ? 'checked' : ''?> onchange="this.form.submit()">
                                    <?php } ?>
                                </div>
                                <?= Html::endForm() ?>
                            </td>
                            <?php } ?>
                            <td>
                                <?php $subs = array_key_exists((int)$program->id, $subsByProgram) ? $subsByProgram[(int)$program->id] : []; ?>

                                <?php if($subs){ ?>
                                    <div class="mb-2">
                                        <?php foreach($subs as $sub){ ?>
                                            <div class="d-flex align-items-center justify-content-between" style="max-width: 560px;">
                                                <div>
                                                    <?= Html::beginForm(Url::to(['program/admin-program-sub-update', 'id' => $sub->id]), 'post', ['class' => 'row g-2', 'style' => 'margin:0;']) ?>
                                                    <?= Html::hiddenInput(Yii::$app->request->csrfParam, Yii::$app->request->getCsrfToken()) ?>
                                                    <div class="col-6">
                                                        <input type="text" name="sub_name" class="form-control form-control-sm" value="<?= Html::encode($sub->sub_name) ?>">
                                                    </div>
                                                    <div class="col-4">
                                                        <input type="text" name="advisor" class="form-control form-control-sm" value="<?= Html::encode($sub->advisor) ?>" placeholder="PIC">
                                                    </div>
                                                    <div class="col-2">
                                                        <button type="submit" class="btn btn-sm btn-primary w-100">Save</button>
                                                    </div>
                                                    <?= Html::endForm() ?>
                                                </div>
                                                <div>
                                                    <?php if($subHasIsActive){ ?>
                                                        <?php $enabledSub = ((int)$sub->getAttribute('is_active') === 1); ?>
                                                        <?= Html::beginForm(Url::to(['program/admin-program-sub-toggle', 'id' => $sub->id]), 'post', ['style' => 'margin:0;']) ?>
                                                        <?= Html::hiddenInput(Yii::$app->request->csrfParam, Yii::$app->request->getCsrfToken()) ?>
                                                        <input type="hidden" name="is_active" value="0">
                                                        <div class="form-check form-switch" style="margin:0;">
                                                            <input class="form-check-input" type="checkbox" name="is_active" value="1" <?=$enabledSub ? 'checked' : ''?> onchange="this.form.submit()">
                                                        </div>
                                                        <?= Html::endForm() ?>
                                                    <?php }else{ ?>
                                                        <?= Html::beginForm(Url::to(['program/admin-program-sub-delete', 'id' => $sub->id]), 'post', ['style' => 'margin:0;']) ?>
                                                        <?= Html::hiddenInput(Yii::$app->request->csrfParam, Yii::$app->request->getCsrfToken()) ?>
                                                        <?= Html::submitButton('Remove', ['class' => 'btn btn-sm btn-outline-danger', 'data-confirm' => 'Remove this sub program?']) ?>
                                                        <?= Html::endForm() ?>
                                                    <?php } ?>
                                                </div>
                                            </div>
                                        <?php } ?>
                                    </div>
                                <?php }else{ ?>
                                    <div class="text-muted small mb-2">No sub programs</div>
                                <?php } ?>

                                <div style="max-width: 560px;">
                                    <?= Html::beginForm(Url::to(['program/admin-program-subs']), 'post', ['class' => 'row g-2']) ?>
                                    <?= Html::hiddenInput(Yii::$app->request->csrfParam, Yii::$app->request->getCsrfToken()) ?>
                                    <?= Html::hiddenInput('program_id', (int)$program->id) ?>
                                    <div class="col-5">
                                        <input type="text" name="sub_name" class="form-control" placeholder="Sub program name">
                                    </div>
                                    <div class="col-4">
                                        <input type="text" name="advisor" class="form-control" placeholder="Advisor (optional)">
                                    </div>
                                    <div class="col-3">
                                        <button type="submit" class="btn btn-primary w-100">Add</button>
                                    </div>
                                    <?= Html::endForm() ?>
                                </div>
                            </td>
                        </tr>
                    <?php $i++; } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

</section>
