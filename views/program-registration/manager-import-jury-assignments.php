<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\UserRole $role */
/** @var app\models\Program $program */
/** @var app\models\ProgramSub|null $programSub */
/** @var app\models\ProgramRubric[] $rubrics */
/** @var app\models\RubricJudgingSession[] $availableSessions */
/** @var array $sessionReferenceRows */
/** @var array $stages */
/** @var int $selectedStage */

$subText = $programSub ? ' / ' . $programSub->sub_abbr : '';
$this->title = 'Import Jury Assignment (' . $program->program_abbr . $subText . ')';
$this->params['breadcrumbs'][] = [
    'label' => $program->program_abbr . $subText,
    'url' => ['/program-registration/manager-dashboard', 'id' => $program->id, 'sub' => $programSub ? $programSub->id : null],
];
$this->params['breadcrumbs'][] = [
    'label' => 'Participants & Juries Assignment',
    'url' => ['/program-registration/manager', 'id' => $program->id, 'sub' => $programSub ? $programSub->id : null],
];
$this->params['breadcrumbs'][] = $this->title;

$sampleHeaders = ['jury_name', 'jury_email', 'group_name', 'rubric_id', 'session_id'];
$sampleRows = [
    ['Dr. Jury One', 'jury1@example.com', 'AL01 - AL04', '12', '31'],
    ['Dr. Jury Two', 'jury2@example.com', 'AL05', '12', '32'],
];
$sampleCsv = implode(',', $sampleHeaders) . "\n";
foreach($sampleRows as $row){
    $sampleCsv .= implode(',', $row) . "\n";
}
$sampleCsv = rtrim($sampleCsv, "\n");

$csvTableRows = [
    [
        'jury_name' => 'DR ZAMINOR BINTI ZAMZAMIR',
        'jury_email' => 'zaminor@umk.edu.my',
        'group_name' => 'AL01 - AL04',
        'rubric_id' => '24',
        'session_id' => '13',
        'behavior' => 'Creates one assignment for every existing group from AL01 through AL04.',
    ],
    [
        'jury_name' => '',
        'jury_email' => '',
        'group_name' => 'AL05 - AL08',
        'rubric_id' => '',
        'session_id' => '',
        'behavior' => 'Reuses the previous jury, rubric, and session; assigns AL05 through AL08.',
    ],
    [
        'jury_name' => 'DR SITI FARIHA BINTI MUHAMAD',
        'jury_email' => 'fariha@umk.edu.my',
        'group_name' => 'AL 09',
        'rubric_id' => '24',
        'session_id' => '13',
        'behavior' => 'Single group row. AL 09, AL09, and AL9 are treated as the same group.',
    ],
    [
        'jury_name' => 'DR MUHAMMAD FIRDAUS BIN ZAKARIA',
        'jury_email' => 'mfirdaus.z@umk.edu.my',
        'group_name' => 'Next-Gen',
        'rubric_id' => '24',
        'session_id' => '13',
        'behavior' => 'Not a range because both sides do not share a numbered pattern; matched as one group name.',
    ],
];

$stageOptions = [0 => 'Not Applicable'];
if($stages){
    foreach($stages as $stage){
        $stageOptions[(int)$stage->id] = $stage->stage_name;
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
            <div class="row g-3 align-items-start">
                <div class="col-lg-7">
                    <h5 class="card-title pt-0 mb-2">CSV Upload</h5>
                    <p class="text-muted mb-2">
                        Upload one row per jury assignment. The jury must already exist as an active user with the <code>jury</code> role,
                        and the participant group must already exist in <?= Html::encode($program->program_name) ?><?= $programSub ? ' / ' . Html::encode($programSub->sub_name) : '' ?>.
                    </p>
                    <ul class="mb-0 text-muted">
                        <li>CSV header names must be <code>jury_name</code>, <code>jury_email</code>, <code>group_name</code>, <code>rubric_id</code>, and <code>session_id</code>.</li>
                        <li>Use the same email stored in the jury user account. If the email does not exist yet, the import will create the user, jury role, and jury profile automatically.</li>
                        <li>Newly created users use the email string as their default password.</li>
                        <li>Auto-created jury profiles use category <code>General</code>.</li>
                        <li><code>jury_name</code> must match the existing jury full name when that user already has a name.</li>
                        <li><code>group_name</code> can be one group or a range such as <code>AL01 - AL04</code>. Spaces, punctuation, and number leading zeroes are ignored, so <code>AL1</code>, <code>AL01</code>, and <code>AL 01</code> match the same group.</li>
                        <li>A dash is treated as a range only when both sides have the same prefix/suffix and valid numbers, for example <code>AL01 - AL04</code> or <code>20 - 30</code>. A name like <code>Next-Gen</code> remains a single group name.</li>
                        <li>Blank <code>jury_name</code>, <code>jury_email</code>, <code>rubric_id</code>, or <code>session_id</code> cells reuse the previous non-empty value.</li>
                        <li>Each row must provide its own <code>group_name</code> or group range.</li>
                        <li>If a group or range does not match any participant registration in this scope, that CSV row is skipped and reported after import.</li>
                        <li><code>stage</code> is still controlled by this page and applies to every imported row.</li>
                        <li>If a rubric has no sessions, leave <code>session_id</code> blank or use <code>0</code>.</li>
                        <?php if($programSub){ ?><li>Because this page was opened from a sub program, the reference lists below include other active sibling sub programs under the same parent.</li><?php } ?>
                    </ul>
                </div>
                <div class="col-lg-5">
                    <?= Html::beginForm(['program-registration/manager-import-jury-assignments', 'id' => $program->id, 'sub' => $programSub ? $programSub->id : null], 'post', ['enctype' => 'multipart/form-data']) ?>
                    <label class="form-label fw-semibold">Stage</label>
                    <?= Html::dropDownList('stage', $selectedStage, $stageOptions, ['class' => 'form-select mb-3']) ?>

                    <label for="csv_file" class="form-label fw-semibold">CSV File</label>
                    <input type="file" class="form-control mb-3" id="csv_file" name="csv_file" accept=".csv,text/csv">

                    <div class="d-flex gap-2">
                        <?= Html::submitButton('Import CSV', ['class' => 'btn btn-primary']) ?>
                        <?= Html::a('Back', ['/program-registration/manager', 'id' => $program->id, 'sub' => $programSub ? $programSub->id : null], ['class' => 'btn btn-outline-secondary']) ?>
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
                <table class="table table-bordered align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Column</th>
                            <th>Description</th>
                            <th>Required</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><code>jury_name</code></td>
                            <td>Must match the jury user full name exactly.</td>
                            <td><span class="badge bg-danger">Yes</span></td>
                        </tr>
                        <tr>
                            <td><code>jury_email</code></td>
                            <td>Used to find the existing jury user account.</td>
                            <td><span class="badge bg-danger">Yes</span></td>
                        </tr>
                        <tr>
                            <td><code>group_name</code></td>
                            <td>Used to find participant registrations in the current program/sub scope. Accepts one group or a range such as <code>AL01 - AL04</code>.</td>
                            <td><span class="badge bg-danger">Yes</span></td>
                        </tr>
                        <tr>
                            <td><code>rubric_id</code></td>
                            <td>Rubric ID to assign for that row. Use the reference table below.</td>
                            <td><span class="badge bg-danger">Yes</span></td>
                        </tr>
                        <tr>
                            <td><code>session_id</code></td>
                            <td>Judging session ID for that row. Must belong to the selected rubric. Use <code>0</code> or blank only when the rubric has no sessions.</td>
                            <td><span class="badge bg-danger">Yes</span></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header">CSV Format Guide</div>
        <div class="card-body pt-4">
            <div class="row g-3">
                <div class="col-lg-6">
                    <h6 class="fw-semibold">How Group Matching Works</h6>
                    <ul class="text-muted mb-0">
                        <li>Single group: <code>AL01</code> assigns only the matched participant group.</li>
                        <li>Range group: <code>AL01 - AL04</code> assigns all existing groups from <code>AL01</code> until <code>AL04</code>.</li>
                        <li>Flexible spelling: <code>AL1</code>, <code>AL01</code>, and <code>AL 01</code> are treated as the same group.</li>
                        <li>Numeric range: <code>20 - 30</code> matches numeric group names only, not <code>AL20</code>.</li>
                        <li>Non-range dash: <code>Next-Gen</code> is treated as one group name.</li>
                    </ul>
                </div>
                <div class="col-lg-6">
                    <h6 class="fw-semibold">How Blank Cells Work</h6>
                    <ul class="text-muted mb-0">
                        <li>Blank <code>jury_name</code> reuses the previous jury name.</li>
                        <li>Blank <code>jury_email</code> reuses the previous jury email.</li>
                        <li>Blank <code>rubric_id</code> reuses the previous rubric.</li>
                        <li>Blank <code>session_id</code> reuses the previous session.</li>
                        <li><code>group_name</code> is never carried forward; every row must set a group or range.</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header">Available Rubrics</div>
        <div class="card-body pt-4">
            <div class="table-responsive">
                <table class="table table-bordered align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Rubric ID</th>
                            <th>Sub Program</th>
                            <th>Rubric Name</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if($rubrics){ foreach($rubrics as $rubricLink){ ?>
                            <tr>
                                <td><code><?= Html::encode($rubricLink->rubric_id) ?></code></td>
                                <td><?= Html::encode($rubricLink->programSub ? $rubricLink->programSub->sub_name : ($programSub ? 'Parent Scope' : 'Program')) ?></td>
                                <td><?= Html::encode($rubricLink->rubric ? $rubricLink->rubric->rubric_name : '') ?></td>
                            </tr>
                        <?php } }else{ ?>
                            <tr><td colspan="3" class="text-muted">No rubric available in this scope.</td></tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header">Available Sessions</div>
        <div class="card-body pt-4">
            <div class="table-responsive">
                <table class="table table-bordered align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Session ID</th>
                            <th>Rubric ID</th>
                            <th>Sub Program</th>
                            <th>Session Name</th>
                            <th>Start</th>
                            <th>End</th>
                            <th>Location</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if($sessionReferenceRows){ foreach($sessionReferenceRows as $row){ $session = $row['session']; ?>
                            <tr>
                                <td><code><?= Html::encode($session->id) ?></code></td>
                                <td><code><?= Html::encode($session->rubric_id) ?></code></td>
                                <td>
                                    <?php
                                    $sessionSubLabel = 'Program';
                                    if(!empty($row['program_sub_id'])){
                                        foreach($rubrics as $rubricLink){
                                            if((int)$rubricLink->rubric_id === (int)$session->rubric_id && (int)$rubricLink->program_sub === (int)$row['program_sub_id']){
                                                $sessionSubLabel = $rubricLink->programSub ? $rubricLink->programSub->sub_name : ('Sub #' . (int)$row['program_sub_id']);
                                                break;
                                            }
                                        }
                                    }
                                    ?>
                                    <?= Html::encode($sessionSubLabel) ?>
                                </td>
                                <td><?= Html::encode($session->session_name) ?></td>
                                <td><?= Html::encode($session->datetime_start ?: '') ?></td>
                                <td><?= Html::encode($session->datetime_end ?: '') ?></td>
                                <td><?= Html::encode($session->location ?: '') ?></td>
                            </tr>
                        <?php } }else{ ?>
                            <tr><td colspan="7" class="text-muted">No session available in this scope.</td></tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header">CSV Table Representation</div>
        <div class="card-body pt-4">
            <p class="text-muted">
                This table shows how a CSV can be prepared. Empty cells shown below can be left blank in the file when you want to reuse the previous jury, rubric, or session value.
            </p>
            <div class="table-responsive">
                <table class="table table-bordered align-middle mb-0">
                    <thead>
                        <tr>
                            <?php foreach($sampleHeaders as $header): ?>
                                <th><code><?= Html::encode($header) ?></code></th>
                            <?php endforeach; ?>
                            <th>Importer Behavior</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($csvTableRows as $row): ?>
                            <tr>
                                <?php foreach($sampleHeaders as $header): ?>
                                    <td><?= $row[$header] === '' ? '<span class="text-muted">(empty)</span>' : Html::encode($row[$header]) ?></td>
                                <?php endforeach; ?>
                                <td class="text-muted"><?= Html::encode($row['behavior']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">Sample CSV</div>
        <div class="card-body pt-4">
            <pre class="bg-light border rounded p-3 mb-0"><code><?= Html::encode($sampleCsv) ?></code></pre>
        </div>
    </div>
</section>
