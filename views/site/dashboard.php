<?php

use app\models\JuryAssign;
use app\models\UserRole;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $user app\models\User */
/* @var $roles app\models\UserRole[] */
/* @var $participantStats array */
/* @var $juryStats array|null */
/* @var $juryAssignments app\models\JuryAssign[] */

$this->title = 'Dashboard';

$roleNames = [];
foreach ($roles as $role) {
    $roleNames[] = $role->roleText ?: $role->role_name;
}

$statCard = function ($label, $value, $icon, $color = 'primary') {
    return '<div class="col-md-3 col-sm-6">
        <div class="card info-card">
            <div class="card-body">
                <h5 class="card-title">' . Html::encode($label) . '</h5>
                <div class="d-flex align-items-center">
                    <div class="card-icon rounded-circle d-flex align-items-center justify-content-center text-' . Html::encode($color) . '">
                        <i class="' . Html::encode($icon) . '"></i>
                    </div>
                    <div class="ps-3">
                        <h6>' . Html::encode((string)$value) . '</h6>
                    </div>
                </div>
            </div>
        </div>
    </div>';
};
?>

<div class="pagetitle">
    <h1>Dashboard</h1>
</div>

<section class="section dashboard">
    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body pt-4">
                    <h5 class="card-title">Welcome, <?= Html::encode($user->fullname ?: $user->username) ?></h5>
                    <div class="mb-2">
                        <?php if ($roleNames): ?>
                            <?php foreach ($roleNames as $name): ?>
                                <span class="badge bg-primary me-1"><?= Html::encode($name) ?></span>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <span class="text-muted">No active role found.</span>
                        <?php endif; ?>
                    </div>
                    <div class="text-muted"><?= Html::encode($user->email) ?></div>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card">
                <div class="card-body pt-4">
                    <h5 class="card-title">Quick Actions</h5>
                    <div class="d-grid gap-2">
                        <?= Html::a('<i class="bi bi-card-list"></i> Program Registration', ['/program/public-programs'], ['class' => 'btn btn-outline-primary']) ?>
                        <?= Html::a('<i class="bi bi-upc-scan"></i> Attendance & Certificate', ['/session/participant'], ['class' => 'btn btn-outline-primary']) ?>
                        <?php if ($user->isJury): ?>
                            <?= Html::a('<i class="bi bi-file-earmark-medical"></i> Jury Assignments', ['/program-registration/jury-assignment'], ['class' => 'btn btn-primary']) ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <?= $statCard('Active Roles', count($roles), 'bi bi-person-badge', 'primary') ?>
        <?= $statCard('My Registrations', $participantStats['registrations'] ?? 0, 'bi bi-card-checklist', 'success') ?>
        <?= $statCard('Completed Registrations', $participantStats['complete'] ?? 0, 'bi bi-check2-circle', 'success') ?>
        <?php if ($juryStats): ?>
            <?= $statCard('Jury Assignments', $juryStats['total'] ?? 0, 'bi bi-clipboard-check', 'warning') ?>
        <?php endif; ?>
    </div>

    <?php if ($juryStats): ?>
        <div class="row">
            <?= $statCard('Assigned', $juryStats['assigned'] ?? 0, 'bi bi-hourglass-split', 'warning') ?>
            <?= $statCard('Judging', $juryStats['judging'] ?? 0, 'bi bi-pencil-square', 'primary') ?>
            <?= $statCard('Complete', $juryStats['complete'] ?? 0, 'bi bi-check-circle', 'success') ?>
        </div>

        <div class="card">
            <div class="card-body pt-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="card-title m-0">Jury Assignments</h5>
                    <?= Html::a('View All', ['/program-registration/jury-assignment'], ['class' => 'btn btn-outline-primary btn-sm']) ?>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>Program</th>
                                <th>Participant</th>
                                <th>Session</th>
                                <th>Status</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($juryAssignments): ?>
                                <?php foreach ($juryAssignments as $assignment): ?>
                                    <?php $registration = $assignment->registration; ?>
                                    <tr>
                                        <td><?= Html::encode($registration && $registration->program ? $registration->programNameShort : '') ?></td>
                                        <td><?= Html::encode($registration ? ($registration->group_name ?: $registration->project_name) : '') ?></td>
                                        <td><?= Html::encode($assignment->judgingSession ? $assignment->judgingSession->session_name : ($assignment->rubric ? $assignment->rubric->rubric_name : '')) ?></td>
                                        <td><?= $assignment->statusLabel ?></td>
                                        <td class="text-end">
                                            <?php if ((int)$assignment->status < 20): ?>
                                                <?= Html::a('Judge', ['/program-registration/jury-judge', 'id' => $assignment->id], ['class' => 'btn btn-primary btn-sm']) ?>
                                            <?php else: ?>
                                                <?= Html::a('View', ['/program-registration/view-result', 'id' => $assignment->id], ['class' => 'btn btn-outline-secondary btn-sm']) ?>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="text-muted">No jury assignment found.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    <?php endif; ?>
</section>
