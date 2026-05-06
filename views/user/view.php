<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var app\models\User $model */
/** @var array $deleteBlockers */
/** @var app\models\UserRole[] $userRoles */
/** @var app\models\UserRole[] $committeeRoles */
/** @var app\models\JuryProfile|null $juryProfile */
/** @var app\models\JuryApplication[] $juryApplications */
/** @var app\models\JuryAssign[] $juryAssignments */
/** @var array $relatedData */

$this->title = $model->fullname;
$this->params['breadcrumbs'][] = ['label' => 'All Users', 'url' => ['all']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="pagetitle">
<h1><?=Html::encode($this->title)?></h1></div>

    </div><!-- End Page Title -->

    <section class="section dashboard">

    <div class="card">
            <div class="card-body pt-4">

    <?php if(Yii::$app->user->identity->isAdmin): ?>
        <p>
            <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
            <?= Html::a('Login As', ['login-as', 'id' => $model->id], ['class' => 'btn btn-warning']) ?>
        </p>
    <?php endif; ?>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'fullname',
            'username',
            'email:email',
            'phone',
            'isInternalText:text:Category',
            'isStudentText:text:Student Status',
            'matric',
            'institution',
            'status',
            [
                'label' => 'Created At',
                'value' => $model->created_at ? date('Y-m-d H:i:s', $model->created_at) : '',
            ],
            [
                'label' => 'Updated At',
                'value' => $model->updated_at ? date('Y-m-d H:i:s', $model->updated_at) : '',
            ],
        ],
    ]) ?>

    <h5 class="mt-4">Summary</h5>
    <?php if(!empty($deleteBlockers)): ?>
        <div class="alert alert-warning">
            User cannot be deleted because related records exist:
            <?= Html::encode(implode(', ', $deleteBlockers)) ?>
        </div>
    <?php else: ?>
        <div class="alert alert-success">
            No related records found. This user can be deleted from the update page.
        </div>
    <?php endif; ?>

    <h5 class="mt-4">User Role Access</h5>
    <?php if(empty($userRoles)): ?>
        <p>No role access found.</p>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-bordered table-sm">
                <thead>
                    <tr>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Program</th>
                        <th>Competition</th>
                        <th>Committee</th>
                        <th>Requested</th>
                        <th>Approved</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($userRoles as $role): ?>
                        <tr>
                            <td><?= Html::encode($role->roleText) ?></td>
                            <td><?= $role->statusLabel ?></td>
                            <td><?= Html::encode($role->program ? $role->program->program_name : '') ?></td>
                            <td><?= Html::encode($role->programSub ? $role->programSub->sub_name : '') ?></td>
                            <td><?= Html::encode($role->committee ? $role->committee->com_name_en : '') ?></td>
                            <td><?= Html::encode($role->request_at) ?></td>
                            <td><?= Html::encode($role->approve_at) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>

    <h5 class="mt-4">Committee</h5>
    <?php if(empty($committeeRoles)): ?>
        <p>No committee membership found.</p>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-bordered table-sm">
                <thead>
                    <tr>
                        <th>Committee</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Requested</th>
                        <th>Approved</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($committeeRoles as $role): ?>
                        <tr>
                            <td><?= Html::encode($role->committee ? $role->committee->com_name_en : '') ?></td>
                            <td><?= Html::encode($role->is_leader == 1 ? 'Leader' : 'Member') ?></td>
                            <td><?= $role->statusLabel ?></td>
                            <td><?= Html::encode($role->request_at) ?></td>
                            <td><?= Html::encode($role->approve_at) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>

    <h5 class="mt-4">Jury Application</h5>
    <?php if($juryProfile): ?>
        <p>
            <b>Profile:</b>
            <?= Html::encode($juryProfile->fullname) ?>
            <?= $juryProfile->category ? '(' . Html::encode($juryProfile->category) . ')' : '' ?>
            <?= $juryProfile->institution ? '- ' . Html::encode($juryProfile->institution) : '' ?>
        </p>
    <?php endif; ?>

    <?php if(empty($juryApplications)): ?>
        <p>No jury application found.</p>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-bordered table-sm">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Program</th>
                        <th>Competition</th>
                        <th>Judging Session</th>
                        <th>Status</th>
                        <th>Created</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($juryApplications as $application): ?>
                        <tr>
                            <td><?= (int)$application->id ?></td>
                            <td><?= Html::encode($application->program ? $application->program->program_name : '') ?></td>
                            <td><?= Html::encode($application->programSub ? $application->programSub->sub_name : '') ?></td>
                            <td><?= Html::encode($application->judgingSession ? $application->judgingSession->session_name : '') ?></td>
                            <td><span class="badge bg-secondary"><?= Html::encode($application->statusText) ?></span></td>
                            <td><?= $application->created_at ? date('Y-m-d H:i:s', $application->created_at) : '' ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>

    <h5 class="mt-4">Jury Assignment</h5>
    <?php if(empty($juryAssignments)): ?>
        <p>No jury assignment found.</p>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-bordered table-sm">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Registration</th>
                        <th>Program</th>
                        <th>Competition</th>
                        <th>Stage</th>
                        <th>Rubric</th>
                        <th>Judging Session</th>
                        <th>Status</th>
                        <th>Score</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($juryAssignments as $assignment): ?>
                        <?php $registration = $assignment->registration; ?>
                        <tr>
                            <td><?= (int)$assignment->id ?></td>
                            <td>
                                <?php if($registration): ?>
                                    <?= Html::encode($registration->group_name ?: $registration->project_name ?: $registration->id) ?>
                                <?php endif; ?>
                            </td>
                            <td><?= Html::encode($registration && $registration->program ? $registration->program->program_name : '') ?></td>
                            <td><?= Html::encode($registration && $registration->programSub ? $registration->programSub->sub_name : '') ?></td>
                            <td><?= Html::encode($assignment->stageText) ?></td>
                            <td><?= Html::encode($assignment->rubric ? $assignment->rubric->rubric_name : '') ?></td>
                            <td><?= Html::encode($assignment->judgingSession ? $assignment->judgingSession->session_name : '') ?></td>
                            <td><?= $assignment->statusLabel ?></td>
                            <td><?= Html::encode($assignment->score) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>

    <h5 class="mt-4">Related Data</h5>
    <?php if(empty($relatedData)): ?>
        <p>No related records found.</p>
    <?php endif; ?>

    <?php foreach($relatedData as $group): ?>
        <h6 class="mt-3"><?= Html::encode($group['label']) ?> <span class="badge bg-secondary"><?= (int)$group['count'] ?></span></h6>
        <div class="table-responsive">
            <table class="table table-bordered table-sm">
                <thead>
                    <tr>
                        <?php foreach($group['columns'] as $column): ?>
                            <th><?= Html::encode($column) ?></th>
                        <?php endforeach; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($group['rows'] as $row): ?>
                        <tr>
                            <?php foreach($group['columns'] as $column): ?>
                                <td><?= Html::encode($row[$column]) ?></td>
                            <?php endforeach; ?>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php if($group['count'] > count($group['rows'])): ?>
            <p><small>Showing latest <?= count($group['rows']) ?> of <?= (int)$group['count'] ?> records.</small></p>
        <?php endif; ?>
    <?php endforeach; ?>

</div>
            </div>
        </div>

    </section>
