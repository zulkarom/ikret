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

$minimalCsvHeaders = [];
if((int)$program->has_sub === 1 && in_array('program_sub', $sampleHeaders, true)){
    $minimalCsvHeaders[] = 'program_sub';
}
foreach(['group_name', 'member_names', 'member_matrics'] as $header){
    if(in_array($header, $sampleHeaders, true)){
        $minimalCsvHeaders[] = $header;
    }
}
$minimalCsvRows = [];
if($minimalCsvHeaders){
    $minimalValues = [
        [
            'program_sub' => $availableProgramSubs ? (string)$availableProgramSubs[0]->id : '16',
            'group_name' => 'AL01',
            'member_names' => 'ABDUL HADI BIN KHAIRILIZA',
            'member_matrics' => 'A23A1902',
        ],
        [
            'program_sub' => '',
            'group_name' => '',
            'member_names' => 'AIDA NABILA BINTI NORDIN',
            'member_matrics' => 'A23A1935',
        ],
        [
            'program_sub' => '',
            'group_name' => '',
            'member_names' => '',
            'member_matrics' => '',
        ],
        [
            'program_sub' => '',
            'group_name' => 'AL02',
            'member_names' => 'HIDAYATI BINTI MASNGON',
            'member_matrics' => 'A23A2349',
        ],
        [
            'program_sub' => '',
            'group_name' => '',
            'member_names' => 'FATIN FARZANA BINTI AHMAD MURAD',
            'member_matrics' => 'A23A1534',
        ],
    ];
    foreach($minimalValues as $values){
        $row = [];
        foreach($minimalCsvHeaders as $header){
            $row[$header] = $values[$header] ?? '';
        }
        $minimalCsvRows[] = $row;
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
                        <?= Html::a('Delete Participants', ['program-registration/cleanup-participants', 'id' => $program->id, 'sub' => $managerSub ? $managerSub->id : null], ['class' => 'btn btn-outline-danger']) ?>
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
                    <li>CSV header names must match exactly (case-sensitive).</li>
                    <li>Column order is flexible - you can arrange columns in any order.</li>
                    <li>Values in each row must correspond to the column headers in that row.</li>
                    <li>Integer option/select fields expect numeric values used by the registration form.</li>
                    <li>The first non-empty row for each <code>group_name</code> is used for registration details such as <code>program_sub</code>, category, project, contact, institution, and advisor fields.</li>
                    <li>If this program has sub-programs, a blank <code>program_sub</code> cell will reuse the previous non-empty <code>program_sub</code> value.</li>
                    <li>Rows after the first member may leave <code>group_name</code> empty; the importer will treat them as part of the previous group.</li>
                    <li>A completely empty row is ignored. A row without <code>group_name</code> before any group has started is also ignored.</li>
                    <li>Empty optional cells are saved as blank values. Empty member-name rows are not added as members.</li>
                    <li>The first member row in every group must contain both <code>member_names</code> and <code>member_matrics</code> because that member becomes the login user for the registration.</li>
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

    <?php if($minimalCsvHeaders){ ?>
        <div class="card mb-4">
            <div class="card-header">Minimal CSV Example</div>
            <div class="card-body pt-4">
                <p class="text-muted">
                    This table represents the smallest practical CSV layout for group member import.
                    Blank cells are intentional: blank <code>group_name</code> continues the previous group, blank <code>program_sub</code> continues the previous sub-program, and a fully empty data row is skipped.
                </p>
                <div class="table-responsive">
                    <table class="table table-bordered align-middle mb-0">
                        <thead>
                            <tr>
                                <?php foreach($minimalCsvHeaders as $header): ?>
                                    <th><code><?= Html::encode($header) ?></code></th>
                                <?php endforeach; ?>
                                <th>Importer Behavior</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($minimalCsvRows as $index => $row): ?>
                                <tr>
                                    <?php foreach($minimalCsvHeaders as $header): ?>
                                        <td><?= $row[$header] === '' ? '<span class="text-muted">(empty)</span>' : Html::encode($row[$header]) ?></td>
                                    <?php endforeach; ?>
                                    <td class="text-muted">
                                        <?php if($index === 0): ?>
                                            Starts group AL01 and sets the current sub-program.
                                        <?php elseif($index === 1): ?>
                                            Adds another member to AL01; uses previous group and sub-program.
                                        <?php elseif($index === 2): ?>
                                            Fully empty row is ignored.
                                        <?php elseif($index === 3): ?>
                                            Starts group AL02; blank sub-program reuses the previous sub-program.
                                        <?php else: ?>
                                            Adds another member to AL02; uses previous group and sub-program.
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

    <div class="card">
        <div class="card-header">Sample CSV</div>
        <div class="card-body pt-4">
            <p class="text-muted">
                Sample shows one team with 3 members. The first row starts the group and contains the registration values.
                The next rows leave <code>group_name</code> and registration-only fields empty, so they continue under the previous group.
                For sub-program imports, blank <code>program_sub</code> cells continue using the previous <code>program_sub</code>.
                You may keep blank separator rows between groups; they will be ignored.
            </p>
            <pre class="bg-light border rounded p-3 mb-0"><code><?= Html::encode($sampleCsv) ?></code></pre>
        </div>
    </div>
</section>
