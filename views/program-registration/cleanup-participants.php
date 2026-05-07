<?php

use yii\helpers\ArrayHelper;
use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\UserRole $role */
/** @var app\models\Program $program */
/** @var app\models\ProgramSub|null $managerSub */
/** @var app\models\ProgramSub[] $availableProgramSubs */
/** @var string $selectedSub */
/** @var array $stats */

$subText = $managerSub ? ' / ' . $managerSub->sub_abbr : '';
$this->title = 'Delete Participants (' . $program->program_abbr . ')';

$breadcrumbUrl = ['/program-registration/manager-dashboard', 'id' => $program->id, 'sub' => $managerSub ? $managerSub->id : null];
if((int)$program->has_sub === 1 && !$managerSub){
    $breadcrumbUrl = ['/program-registration/manager-parent', 'id' => $program->id];
}

$this->params['breadcrumbs'][] = [
    'label' => $program->program_abbr . $subText,
    'url' => $breadcrumbUrl,
];
$this->params['breadcrumbs'][] = $this->title;

$subOptions = ['all' => 'All sub-programs'];
if((int)$program->has_sub === 1){
    $subOptions += ArrayHelper::map($availableProgramSubs, 'id', function($sub){
        $abbr = $sub->sub_abbr ? $sub->sub_abbr . ' - ' : '';
        return '#' . $sub->id . ' ' . $abbr . $sub->sub_name;
    });
}

$selectedLabel = $selectedSub === 'all' ? 'all sub-programs' : ($subOptions[$selectedSub] ?? ('sub #' . $selectedSub));
$hasRegistrations = (int)($stats['registrations'] ?? 0) > 0;
$hasJuryAssignments = (int)($stats['jury_assignments'] ?? 0) > 0;
?>

<div class="pagetitle">
    <h1><?= Html::encode($this->title) ?></h1>
</div>

</div><!-- End Page Title -->

<section class="section dashboard">
    <div class="card mb-4">
        <div class="card-body pt-4">
            <div class="row g-3 align-items-end">
                <div class="col-lg-7">
                    <h5 class="card-title pt-0 mb-2">Participant Cleanup</h5>
                    <p class="text-muted mb-0">
                        Delete participant registrations for <?= Html::encode($program->program_name) ?>.
                        This removes registration records and related member, mentor, and achievement records.
                        Participants with jury assignments cannot be deleted from this page. User accounts are not deleted.
                    </p>
                </div>
                <div class="col-lg-5">
                    <?= Html::beginForm(['program-registration/cleanup-participants', 'id' => $program->id, 'sub' => $managerSub ? $managerSub->id : null], 'get') ?>
                    <label for="program_sub" class="form-label fw-semibold">Scope</label>
                    <div class="d-flex gap-2">
                        <?= Html::dropDownList('program_sub', $selectedSub, $subOptions, ['id' => 'program_sub', 'class' => 'form-select']) ?>
                        <?= Html::submitButton('Check', ['class' => 'btn btn-outline-primary']) ?>
                    </div>
                    <?= Html::endForm() ?>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header">Records Found For <?= Html::encode($selectedLabel) ?></div>
        <div class="card-body pt-4">
            <div class="table-responsive">
                <table class="table table-bordered align-middle mb-0">
                    <tbody>
                        <tr>
                            <th style="width: 45%;">Participant registrations</th>
                            <td><span class="badge bg-primary"><?= Html::encode($stats['registrations'] ?? 0) ?></span></td>
                        </tr>
                        <tr>
                            <th>Members</th>
                            <td><?= Html::encode($stats['members'] ?? 0) ?></td>
                        </tr>
                        <tr>
                            <th>Mentors</th>
                            <td><?= Html::encode($stats['mentors'] ?? 0) ?></td>
                        </tr>
                        <tr>
                            <th>Jury assignments</th>
                            <td><?= Html::encode($stats['jury_assignments'] ?? 0) ?></td>
                        </tr>
                        <tr>
                            <th>Achievements</th>
                            <td><?= Html::encode($stats['achievements'] ?? 0) ?></td>
                        </tr>
                        <tr>
                            <th>User accounts</th>
                            <td>0 deleted</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">Confirm Delete</div>
        <div class="card-body pt-4">
            <?php if($hasRegistrations && !$hasJuryAssignments): ?>
                <?= Html::beginForm(['program-registration/cleanup-participants', 'id' => $program->id, 'sub' => $managerSub ? $managerSub->id : null], 'post') ?>
                <?= Html::hiddenInput('program_sub', $selectedSub) ?>
                <label for="cleanup_confirm" class="form-label fw-semibold">Type DELETE to confirm</label>
                <input type="text" name="cleanup_confirm" id="cleanup_confirm" class="form-control mb-3" autocomplete="off">
                <div class="d-flex gap-2">
                    <?= Html::submitButton('Delete Participants', [
                        'class' => 'btn btn-danger',
                        'data-confirm' => 'Delete participant registrations for this scope? User accounts will not be deleted.',
                    ]) ?>
                    <?= Html::a('Back to Import', ['program-registration/import-participants', 'id' => $program->id, 'sub' => $managerSub ? $managerSub->id : null], ['class' => 'btn btn-outline-secondary']) ?>
                </div>
                <?= Html::endForm() ?>
            <?php elseif($hasJuryAssignments): ?>
                <div class="alert alert-warning">
                    This scope has jury assignments. Delete is disabled until the assignments are removed.
                </div>
                <?= Html::a('Back to Import', ['program-registration/import-participants', 'id' => $program->id, 'sub' => $managerSub ? $managerSub->id : null], ['class' => 'btn btn-outline-secondary']) ?>
            <?php else: ?>
                <p class="text-muted mb-3">No participant registrations found for this scope.</p>
                <?= Html::a('Back to Import', ['program-registration/import-participants', 'id' => $program->id, 'sub' => $managerSub ? $managerSub->id : null], ['class' => 'btn btn-outline-secondary']) ?>
            <?php endif; ?>
        </div>
    </div>
</section>
