<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\UserRole $role */
/** @var app\models\Program $program */
/** @var app\models\ProgramSub|null $managerSub */
/** @var array $importableFields */
/** @var array $unsupportedFields */
/** @var array $extraColumns */
/** @var array $sampleHeaders */
/** @var array $sampleRows */
/** @var app\models\ProgramSub[] $availableProgramSubs */

$subText = $managerSub ? ' / ' . $managerSub->sub_abbr : '';
$this->title = 'Import Participant (' . $program->program_abbr . ')';

$breadcrumbUrl = ['/program-registration/manager-dashboard', 'id' => $program->id, 'sub' => $managerSub ? $managerSub->id : null];
if((int)$program->has_sub === 1 && !$managerSub){
    $breadcrumbUrl = ['/program-registration/manager-parent', 'id' => $program->id];
}

$this->params['breadcrumbs'][] = [
    'label' => $program->program_abbr . $subText,
    'url' => $breadcrumbUrl,
];
$this->params['breadcrumbs'][] = $this->title;

$sampleCsv = '';
if($sampleHeaders){
    $sampleCsv = implode(',', $sampleHeaders) . "\n";
    foreach($sampleRows as $row){
        $sampleCsv .= implode(',', $row) . "\n";
    }
    $sampleCsv = rtrim($sampleCsv, "\n"); // Remove trailing newline
}

$requiredCount = 0;
foreach($importableFields as $field){
    if(!empty($field['required'])){
        $requiredCount++;
    }
}
foreach($extraColumns as $column){
    if(!empty($column['required'])){
        $requiredCount++;
    }
}
?>

<div class="pagetitle">
    <h1><?= Html::encode($this->title) ?></h1>
</div>

</div><!-- End Page Title -->

<section class="section dashboard">
    <div class="card mb-4">
        <div class="card-body pt-4">
            <div class="row g-3 align-items-center">
                <div class="col-lg-8">
                    <h5 class="card-title pt-0 mb-2">CSV Upload</h5>
                    <p class="text-muted mb-2">
                        Upload one CSV file for <?= Html::encode($program->program_name) ?>.
                        <strong>Group-based import:</strong> Multiple rows with the same <code>group_name</code> will be combined into one registration.
                        A user account will be created for the first member of each group. 
                        <br><strong>Login credentials:</strong> Username = matric (as entered in CSV), Password = matric
                        <?php if($managerSub){ ?><br>This page was opened from <?= Html::encode($managerSub->sub_name) ?>, but import remains at program level, so <code>program_sub</code> must be included in the CSV.<?php } ?>
                    </p>
                    <p class="mb-0">
                        <span class="badge bg-primary"><?= count($importableFields) ?></span> importable field columns
                        <span class="badge bg-warning text-dark ms-2"><?= $requiredCount ?></span> required columns
                        <?php if($unsupportedFields){ ?>
                            <span class="badge bg-secondary ms-2"><?= count($unsupportedFields) ?></span> manual-only fields
                        <?php } ?>
                    </p>
                </div>
                <div class="col-lg-4">
                    <?= Html::beginForm(['program-registration/import-participants', 'id' => $program->id, 'sub' => $managerSub ? $managerSub->id : null], 'post', ['enctype' => 'multipart/form-data']) ?>
                    <label for="csv_file" class="form-label fw-semibold">CSV File</label>
                    <input type="file" class="form-control mb-3" id="csv_file" name="csv_file" accept=".csv,text/csv">
                    <div class="d-flex gap-2">
                        <?= Html::submitButton('Upload CSV', ['class' => 'btn btn-primary']) ?>
                        <?= Html::a('Back', $breadcrumbUrl, ['class' => 'btn btn-outline-secondary']) ?>
                    </div>
                    <?= Html::endForm() ?>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header">Accepted CSV Columns</div>
        <div class="card-body pt-4">
            <div class="table-responsive">
                <table class="table table-bordered align-middle">
                    <thead>
                        <tr>
                            <th style="width: 25%;">Column Name</th>
                            <th style="width: 30%;">Field Label</th>
                            <th style="width: 25%;">Data Type</th>
                            <th style="width: 20%;">Required</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($importableFields as $fieldName => $field): ?>
                            <tr>
                                <td><code><?= Html::encode($fieldName) ?></code></td>
                                <td><?= Html::encode($field['label']) ?></td>
                                <td><span class="badge bg-info text-dark"><?= Html::encode($field['type']) ?></span></td>
                                <td class="text-center"><?= !empty($field['required']) ? '<span class="badge bg-danger">Yes</span>' : '<span class="badge bg-light text-dark">No</span>' ?></td>
                            </tr>
                        <?php endforeach; ?>
                        <?php foreach($extraColumns as $fieldName => $field): ?>
                            <tr>
                                <td><code><?= Html::encode($fieldName) ?></code></td>
                                <td><?= Html::encode($field['label']) ?></td>
                                <td><span class="badge bg-info text-dark"><?= Html::encode($field['type']) ?></span></td>
                                <td class="text-center"><?= !empty($field['required']) ? '<span class="badge bg-danger">Yes</span>' : '<span class="badge bg-light text-dark">No</span>' ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div class="small text-muted">
                <strong>Important:</strong>
                <ul class="mb-0 mt-2">
                    <li>CSV header names must match exactly (case-sensitive)</li>
                    <li>Column order is flexible - you can arrange columns in any order</li>
                    <li>Values in each row must correspond to the column headers in that row</li>
                    <li>Integer option/select fields expect numeric values used by the registration form</li>
                </ul>
            </div>
        </div>
    </div>

    <?php if($unsupportedFields){ ?>
        <div class="card mb-4">
            <div class="card-header">Enabled But Not Importable By CSV</div>
            <div class="card-body pt-4">
                <div class="table-responsive">
                    <table class="table table-bordered align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Field</th>
                                <th>Label</th>
                                <th>Form Type</th>
                                <th>Reason</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($unsupportedFields as $fieldName => $field): ?>
                                <tr>
                                    <td><code><?= Html::encode($fieldName) ?></code></td>
                                    <td><?= Html::encode($field['label']) ?></td>
                                    <td><?= Html::encode($field['type']) ?></td>
                                    <td>
                                        <?php if($fieldName === 'group_member'): ?>
                                            Each team member is represented by one CSV row with the same <code>group_name</code>. Use <code>member_names</code><?= array_key_exists('member_matrics', $extraColumns) ? ' and <code>member_matrics</code>' : '' ?> in each row.
                                        <?php else: ?>
                                            This field still needs manual handling after import.
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    <?php } ?>

    <?php if(!empty($availableProgramSubs)){ ?>
        <div class="card mb-4">
            <div class="card-header">Available Program Sub IDs</div>
            <div class="card-body pt-4">
                <p class="text-muted mb-3">
                    Use these IDs in the <code>program_sub</code> column when importing participants for specific sub-programs.
                    Only active sub-programs are shown.
                </p>
                <div class="table-responsive">
                    <table class="table table-bordered align-middle mb-0">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Abbreviation</th>
                                <th>Sub Program Name</th>
                                <th>Advisor</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($availableProgramSubs as $sub): ?>
                                <tr>
                                    <td><code><?= Html::encode($sub->id) ?></code></td>
                                    <td><code><?= Html::encode($sub->sub_abbr ?: 'N/A') ?></code></td>
                                    <td><?= Html::encode($sub->sub_name) ?></td>
                                    <td><?= Html::encode($sub->advisor ?: 'N/A') ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    <?php } ?>

    <div class="card">
        <div class="card-header">Sample CSV</div>
        <div class="card-body pt-4">
            <p class="text-muted">
                Sample shows a team with 3 members (same <code>group_name</code> across rows).
                Each row represents one team member. Remove columns you do not need only if they are not marked required above.
            </p>
            <pre class="bg-light border rounded p-3 mb-0"><code><?= Html::encode($sampleCsv) ?></code></pre>
        </div>
    </div>
</section>
