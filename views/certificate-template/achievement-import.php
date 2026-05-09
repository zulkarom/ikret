<?php

use yii\helpers\Html;
use yii\helpers\Url;

/** @var yii\web\View $this */
/** @var array $guidelines */
/** @var bool $hasWinnerCountColumn */

$this->title = 'Import Achievement CSV';
$this->params['breadcrumbs'][] = ['label' => 'Achievement Config', 'url' => ['achievement-config']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="pagetitle">
    <h1><?= Html::encode($this->title) ?></h1>
</div>

</div><!-- End Page Title -->

<section class="section dashboard">
    <div class="card mb-3">
        <div class="card-header">Upload CSV</div>
        <div class="card-body pt-3">
            <?= Html::beginForm(Url::to(['certificate-template/achievement-import']), 'post', ['enctype' => 'multipart/form-data', 'class' => 'row g-2 align-items-end']) ?>
            <?= Html::hiddenInput(Yii::$app->request->csrfParam, Yii::$app->request->getCsrfToken()) ?>
            <div class="col-12 col-md-9">
                <label class="form-label">CSV File</label>
                <?= Html::fileInput('csv_file', null, ['class' => 'form-control', 'accept' => '.csv,text/csv', 'required' => true]) ?>
                <div class="form-text">Columns: <code>program_id</code>, <code>program_sub</code>, <code>achievement_name</code>, <code>winner_count</code>. Leave <code>program_sub</code> blank for program-level achievements.</div>
                <div class="form-text">If <code>program_id</code> is blank, the importer will use the previous non-empty <code>program_id</code> above it.</div>
                <div class="form-text">If <code>program_sub</code> is blank, the importer will use the previous non-empty <code>program_sub</code> above it. When a new <code>program_id</code> is provided, the carried <code>program_sub</code> is reset.</div>
            </div>
            <div class="col-12 col-md-3">
                <?= Html::submitButton('Import CSV', ['class' => 'btn btn-primary w-100']) ?>
            </div>
            <?= Html::endForm() ?>
        </div>
    </div>

    <div class="card">
        <div class="card-header">Active Program/Sub IDs Guideline</div>
        <div class="card-body pt-3">
            <div class="table-responsive">
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>program_id</th>
                            <th>program_sub</th>
                            <th>Program</th>
                            <th>Program Sub</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php if($guidelines){ ?>
                        <?php foreach($guidelines as $row){ ?>
                            <tr>
                                <td><?= Html::encode((string)$row['program_id']) ?></td>
                                <td><?= Html::encode((string)$row['program_sub']) ?></td>
                                <td><?= Html::encode($row['program']) ?></td>
                                <td><?= Html::encode($row['sub'] ?? '') ?></td>
                            </tr>
                        <?php } ?>
                    <?php }else{ ?>
                        <tr><td colspan="4" class="text-muted">No active program/sub found.</td></tr>
                    <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>
