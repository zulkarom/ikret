<?php

use kartik\export\ExportMenu;
use kartik\form\ActiveForm;
//use yii\bootstrap5\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
use app\models\JuryAssign;
use app\models\ProgramRubric;
use app\models\RubricJudgingSession;

/** @var yii\web\View $this */
/** @var app\models\ProgramRegistrationSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */
/** @var array $registrationSummary */
/** @var array $juryStats */
/** @var app\models\JuryApplication[] $appliedJuries */
$program = $role->program;
$sub_str = $programSub? ' / ' . $programSub->sub_abbr  : '';
$this->title = 'Registration ('.$program->program_abbr . $sub_str.')';
$this->params['breadcrumbs'][] = [
    'label' => $program->program_abbr . $sub_str,
    'url' => ['/program-registration/manager-dashboard', 'id' => $program->id, 'sub' => $programSub ? $programSub->id : null]
];
$this->params['breadcrumbs'][] = $this->title;

$modeList = RubricJudgingSession::listMode();
$formatDatetimeRange = function($startValue, $endValue){
    if(!$startValue && !$endValue){
        return '';
    }
    if(!$startValue){
        return date('d M Y, h:i A', strtotime($endValue));
    }
    if(!$endValue){
        return date('d M Y, h:i A', strtotime($startValue));
    }

    $startTime = strtotime($startValue);
    $endTime = strtotime($endValue);
    if(date('Y-m-d', $startTime) === date('Y-m-d', $endTime)){
        return date('d M Y, h:i A', $startTime) . ' - ' . date('h:i A', $endTime);
    }

    return date('d M Y, h:i A', $startTime) . ' - ' . date('d M Y, h:i A', $endTime);
};
$formatSessionDetails = function($session) use ($formatDatetimeRange, $modeList){
    if(!$session){
        return '<span class="text-muted">No session assigned</span>';
    }

    $range = $formatDatetimeRange($session->datetime_start, $session->datetime_end);
    $location = trim((string)$session->location);
    $mode = $modeList[(int)$session->mode] ?? '';
    $details = [];

    if($range !== ''){
        $details[] = Html::encode($range);
    }
    if($location !== ''){
        $details[] = Html::encode($location);
    }
    if($mode !== ''){
        $details[] = '(' . Html::encode($mode) . ')';
    }

    $html = '<div class="session-cell-main">' . Html::encode($session->session_name) . '</div>';
    if($details){
        $html .= '<div class="session-cell-detail">' . implode('<br>', $details) . '</div>';
    }
    return $html;
};

$this->registerCss(<<<CSS
.manager-summary-grid {
    display: grid;
    grid-template-columns: repeat(6, minmax(0, 1fr));
    gap: 0.75rem;
    margin-bottom: 1rem;
}

.jury-summary-card {
    display: block;
    padding: 1rem;
    border: 1px solid #e5e7eb;
    border-radius: 0.9rem;
    background: #fff;
    color: inherit;
    text-decoration: none;
    box-shadow: 0 0.35rem 1rem rgba(15, 23, 42, 0.06);
    transition: transform 0.15s ease, box-shadow 0.15s ease, border-color 0.15s ease;
}

.jury-summary-card:hover {
    color: inherit;
    text-decoration: none;
    transform: translateY(-1px);
    box-shadow: 0 0.5rem 1.25rem rgba(15, 23, 42, 0.1);
}

.jury-summary-card.active {
    border-color: #0d6efd;
    box-shadow: 0 0.5rem 1.25rem rgba(13, 110, 253, 0.16);
}

.jury-summary-card.urgent {
    border-color: #dc3545;
    background: #fff5f5;
    box-shadow: 0 0.5rem 1.25rem rgba(220, 53, 69, 0.12);
}

.jury-summary-label {
    color: #6c757d;
    font-size: 0.85rem;
    font-weight: 700;
    letter-spacing: 0.04em;
    text-transform: uppercase;
}

.jury-summary-count {
    margin-top: 0.25rem;
    font-size: 2rem;
    font-weight: 800;
    line-height: 1;
}

.participant-cell-main {
    font-weight: 700;
}

.participant-cell-group {
    display: inline-block;
    margin-bottom: 0.25rem;
    padding: 0.22rem 0.55rem;
    border-radius: 999px;
    background: #0d6efd;
    color: #fff;
    font-size: 0.78rem;
    font-weight: 700;
    line-height: 1.2;
}

.participant-cell-members {
    margin: 0.35rem 0 0 1.1rem;
    padding: 0;
    font-size: 0.86rem;
    line-height: 1.35;
    color: #6c757d;
}

.session-cell-main {
    font-weight: 700;
}

.session-cell-detail {
    margin-top: 0.2rem;
    color: #6c757d;
    font-size: 0.88rem;
    line-height: 1.35;
}

@media (max-width: 575.98px) {
    .manager-summary-grid {
        grid-template-columns: 1fr;
    }
}
CSS);
?>
  <div class="pagetitle">
<h1><?=$this->title?></h1>
</div>

    </div><!-- End Page Title -->

    <section class="section dashboard">
        <?php
        $statusList = JuryAssign::getStatusArray();
        $statusColors = JuryAssign::getStatusColor();
        $selectedStatus = $searchModel->jury_status;
        $isUnassignedActive = (int)$searchModel->unassigned === 1;
        ?>
        <div class="manager-summary-grid">
            <div class="jury-summary-card">
                <div class="jury-summary-label">Total Participants</div>
                <div class="jury-summary-count text-primary"><?= Html::encode($registrationSummary['participantCount'] ?? 0) ?></div>
            </div>
            <div class="jury-summary-card">
                <div class="jury-summary-label">Total Groups</div>
                <div class="jury-summary-count text-success"><?= Html::encode($registrationSummary['groupCount'] ?? 0) ?></div>
            </div>
            <?php
            $unassignedUrlParams = Yii::$app->request->queryParams;
            unset($unassignedUrlParams['page'], $unassignedUrlParams['per-page']);
            $unassignedUrlParams[0] = 'manager';
            $unassignedUrlParams['id'] = $program->id;
            if($programSub){
                $unassignedUrlParams['sub'] = $programSub->id;
            }
            unset($unassignedUrlParams[$searchModel->formName()]['jury_status'], $unassignedUrlParams[$searchModel->formName()]['jurySearch']);
            $unassignedUrlParams[$searchModel->formName()]['unassigned'] = 1;
            $unassignedCount = (int)($registrationSummary['unassignedCount'] ?? 0);
            $unassignedClass = $unassignedCount > 0 ? ' urgent' : '';
            $unassignedTextClass = $unassignedCount > 0 ? 'text-danger' : 'text-success';
            ?>
            <?= Html::a(
                '<div class="jury-summary-label">Total Unassigned</div>' .
                '<div class="jury-summary-count ' . $unassignedTextClass . '">' . Html::encode($unassignedCount) . '</div>',
                Url::to($unassignedUrlParams),
                ['class' => 'jury-summary-card' . $unassignedClass . ($isUnassignedActive ? ' active' : '')]
            ) ?>
            <?php foreach($statusList as $status => $label): ?>
                <?php
                $summaryUrlParams = Yii::$app->request->queryParams;
                unset($summaryUrlParams['page'], $summaryUrlParams['per-page']);
                $summaryUrlParams[0] = 'manager';
                $summaryUrlParams['id'] = $program->id;
                if($programSub){
                    $summaryUrlParams['sub'] = $programSub->id;
                }
                unset($summaryUrlParams[$searchModel->formName()]['unassigned']);
                $summaryUrlParams[$searchModel->formName()]['jury_status'] = $status;
                $isActive = (string)$selectedStatus !== '' && (int)$selectedStatus === (int)$status;
                ?>
                <?= Html::a(
                    '<div class="jury-summary-label">' . Html::encode($label) . '</div>' .
                    '<div class="jury-summary-count text-' . Html::encode($statusColors[$status] ?? 'secondary') . '">' . Html::encode($juryStatusSummary[$status] ?? 0) . '</div>',
                    Url::to($summaryUrlParams),
                    ['class' => 'jury-summary-card' . ($isActive ? ' active' : '')]
                ) ?>
            <?php endforeach; ?>
        </div>

        <?php if((string)$selectedStatus !== '' || $isUnassignedActive): ?>
            <div class="mb-3">
                <?= Html::a('Clear Filter', ['manager', 'id' => $program->id, 'sub' => $programSub ? $programSub->id : null], ['class' => 'btn btn-outline-secondary btn-sm']) ?>
            </div>
        <?php endif; ?>

        <div class="form-group"><?=Html::button('Filter Form',['id' => 'btn-filter-form','class' => 'btn btn-info'])?> 
        <?=Html::button('Jury Assignment Form',['id' => 'btn-jury-form', 'class' => 'btn btn-primary'])?> 
        <?=Html::button('Juries Stats',['id' => 'btn-jury-stats', 'class' => 'btn btn-secondary'])?> 
        <?=Html::button('Applied Juries',['id' => 'btn-applied-juries', 'class' => 'btn btn-outline-primary'])?> 
        <?=Html::a('<i class="bi bi-bar-chart-line"></i> Raw Input', ['jury-result', 'id' => $program->id, 'sub' => $programSub ? $programSub->id : null], ['class' => 'btn btn-outline-secondary'])?> 
        <?=Html::a('<i class="bi bi-trophy"></i> Analysis', ['manager-analysis', 'id' => $program->id, 'sub' => $programSub ? $programSub->id : null], ['class' => 'btn btn-outline-success'])?> 
        <?=Html::a('<i class="bi bi-download"></i> Export Excel', ['manager-export-assignments', 'id' => $program->id, 'sub' => $programSub ? $programSub->id : null], ['class' => 'btn btn-success'])?> 
    </div> 

        <?php
        $this->registerJs('
        $("#dwl-exl").click(function(){
            $("#w0-xls")[0].click();
        });
        ');
        ?>

        <?php
        $this->registerJs('
            function toggleManagerCard(target){
                var cards = $("#con-filter-form, #con-jury-form, #con-jury-stats, #con-applied-juries");
                var card = $(target);
                if(card.is(":visible")){
                    card.slideUp();
                    return;
                }
                cards.not(card).slideUp();
                card.slideDown();
            }

            $("#btn-jury-form").click(function(){
                toggleManagerCard("#con-jury-form");
            });
            $("#hide-jury-form").click(function(){
                $("#con-jury-form").slideUp();
              
            });

            $("#btn-filter-form").click(function(){
                toggleManagerCard("#con-filter-form");
            });
            $("#hide-filter-form").click(function(){
                $("#con-filter-form").slideUp();
            });

            $("#btn-jury-stats").click(function(){
                toggleManagerCard("#con-jury-stats");
            });

            $("#btn-applied-juries").click(function(){
                toggleManagerCard("#con-applied-juries");
            });
        ');
        
        ?>
    
    <br />

    <div class="card" style="display:none" id="con-filter-form">
    <div class="card-header">Filter Form</div>
    <div class="card-body pt-4">
    <?= $this->render('_search', [
        'model' => $searchModel,
        'programSub' => $programSub,
        'action' => 'manager'
    ]) ?>
</div></div>

    <div class="card" style="display:none" id="con-jury-stats">
    <div class="card-header">Juries Stats</div>
    <div class="card-body pt-4">
        <div class="table-responsive">
            <table class="table table-bordered table-striped align-middle">
                <thead>
                    <tr>
                        <th>Names</th>
                        <th class="text-center">Assigned</th>
                        <th class="text-center">Judging</th>
                        <th class="text-center">Complete</th>
                    </tr>
                </thead>
                <tbody>
                <?php if(empty($juryStats)): ?>
                    <tr>
                        <td colspan="4" class="text-muted text-center">No jury assignments found.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach($juryStats as $juryStat): ?>
                        <?php
                        $juryFilterParams = [
                            'manager',
                            'id' => $program->id,
                        ];
                        if($programSub){
                            $juryFilterParams['sub'] = $programSub->id;
                        }
                        $juryFilterParams[$searchModel->formName()]['jurySearch'] = $juryStat['fullname'];
                        ?>
                        <tr>
                            <td><?= Html::a(Html::encode($juryStat['fullname']), $juryFilterParams) ?></td>
                            <td class="text-center"><?= Html::encode((int)$juryStat['assigned']) ?></td>
                            <td class="text-center"><?= Html::encode((int)$juryStat['judging']) ?></td>
                            <td class="text-center"><?= Html::encode((int)$juryStat['complete']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div></div>

    <?php
    $appliedRubricQuery = ProgramRubric::find()
        ->alias('pr')
        ->where(['pr.program_id' => (int)$program->id]);
    if($programSub){
        $appliedRubricQuery->andWhere(['pr.program_sub' => (int)$programSub->id]);
    }else{
        $appliedRubricQuery->andWhere(['or', ['pr.program_sub' => null], ['pr.program_sub' => 0]]);
    }
    $appliedRubrics = $appliedRubricQuery->all();
    $appliedRubricArray = ArrayHelper::map($appliedRubrics, 'rubric_id', 'rubric.rubric_name');
    $appliedRubricIds = array_keys($appliedRubricArray);
    $appliedSessionArray = [];
    if($appliedRubricIds){
        $appliedSessions = RubricJudgingSession::find()
            ->where(['rubric_id' => $appliedRubricIds])
            ->orderBy(['datetime_start' => SORT_ASC, 'session_name' => SORT_ASC])
            ->all();
        foreach($appliedSessions as $appliedSession){
            $labelParts = [$appliedSession->session_name];
            $range = $formatDatetimeRange($appliedSession->datetime_start, $appliedSession->datetime_end);
            if($range !== ''){
                $labelParts[] = $range;
            }
            if($appliedSession->location){
                $labelParts[] = $appliedSession->location;
            }
            $appliedSessionArray[(int)$appliedSession->id] = implode(' | ', $labelParts);
        }
    }
    ?>
    <div class="card" style="display:none" id="con-applied-juries">
    <div class="card-header">Applied Juries</div>
    <div class="card-body pt-4">
        <?= Html::beginForm(['manager', 'id' => $program->id, 'sub' => $programSub ? $programSub->id : null], 'post', ['id' => 'applied-jury-form']) ?>
        <div class="row">
            <div class="col-md-4">
                <label class="form-label" for="applied-rubric-id">Rubric</label>
                <?= Html::dropDownList('applied_rubric_id', null, $appliedRubricArray, [
                    'id' => 'applied-rubric-id',
                    'class' => 'form-select',
                    'prompt' => 'Choose Rubric',
                ]) ?>
            </div>
            <div class="col-md-4">
                <label class="form-label" for="applied-judging-session-id">Judging Session</label>
                <?= Html::dropDownList('applied_judging_session_id', null, $appliedSessionArray, [
                    'id' => 'applied-judging-session-id',
                    'class' => 'form-select',
                    'prompt' => 'No Session',
                ]) ?>
                <div class="form-text">
                    Used as a fallback only. If an applied jury already has a session set in the list below, that session will be used for assignment.
                </div>
            </div>
            <?php if($program->programStages): ?>
                <div class="col-md-2">
                    <label class="form-label" for="applied-stage">Stage</label>
                    <?= Html::dropDownList('applied_stage', 0, ArrayHelper::map($program->programStages, 'id', 'stage_name'), [
                        'id' => 'applied-stage',
                        'class' => 'form-select',
                    ]) ?>
                </div>
            <?php else: ?>
                <?= Html::hiddenInput('applied_stage', 0) ?>
            <?php endif; ?>
            <div class="col-md-2">
                <label class="form-label" for="applied-juries-per-group">Juries / Group</label>
                <?= Html::dropDownList('applied_juries_per_group', 1, [1 => 1, 2 => 2, 3 => 3, 4 => 4, 5 => 5], [
                    'id' => 'applied-juries-per-group',
                    'class' => 'form-select',
                ]) ?>
            </div>
        </div>

        <div class="alert alert-info mt-3">
            <strong>Manual assign:</strong> select applied juries and participant rows first.
            <br>
            <strong>Auto Assign Evenly:</strong> select applied juries only. Participants do not need to be checked; the system assigns across all participant groups in this program/category.
        </div>

        <div class="table-responsive mt-3">
            <table class="table table-bordered table-striped align-middle">
                <thead>
                    <tr>
                        <th style="width:40px"><?= Html::checkbox('select_all_applied_juries', false, ['id' => 'select-all-applied-juries']) ?></th>
                        <th>Name</th>
                        <th>Category</th>
                        <th>Session Applied</th>
                        <th style="width:140px">Action</th>
                    </tr>
                </thead>
                <tbody>
                <?php if(empty($appliedJuries)): ?>
                    <tr>
                        <td colspan="5" class="text-muted text-center">No approved jury applications found for this program/category.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach($appliedJuries as $application): ?>
                        <?php
                        $profile = $application->juryProfile;
                        ?>
                        <tr>
                            <td><?= Html::checkbox('jury_application_ids[]', false, ['class' => 'applied-jury-check', 'value' => (int)$application->id]) ?></td>
                            <td><?= Html::encode($profile ? $profile->fullname : '') ?></td>
                            <td><?= Html::encode($profile ? $profile->category : '') ?></td>
                            <td><?= $formatSessionDetails($application->judgingSession) ?></td>
                            <td>
                                <?= Html::button('Update Session', [
                                    'type' => 'button',
                                    'class' => 'btn btn-outline-secondary btn-sm btn-update-applied-session',
                                    'data-id' => (int)$application->id,
                                    'data-name' => $profile ? $profile->fullname : '',
                                    'data-session' => $application->judging_session_id ? (int)$application->judging_session_id : '',
                                ]) ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="form-group">
            <?= Html::submitButton('Assign Applied Juries to Selected Participants', [
                'class' => 'btn btn-success',
                'name' => 'action',
                'value' => 'assign_applied_juries',
                'id' => 'btn-assign-applied-juries',
            ]) ?>
            <?= Html::submitButton('Auto Assign Evenly', [
                'class' => 'btn btn-danger',
                'name' => 'action',
                'value' => 'auto_assign_applied_juries',
                'id' => 'btn-auto-assign-applied-juries',
            ]) ?>
            <a href="javascript:void(0)" id="hide-applied-juries">Hide this form</a>
        </div>
        <?= Html::endForm() ?>
    </div></div>

    <div class="modal fade" id="applied-jury-session-modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <?= Html::beginForm(['manager', 'id' => $program->id, 'sub' => $programSub ? $programSub->id : null], 'post') ?>
                <div class="modal-header">
                    <h5 class="modal-title">Update Applied Jury Session</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <?= Html::hiddenInput('action', 'update_applied_jury_session') ?>
                    <?= Html::hiddenInput('application_id', null, ['id' => 'applied-session-application-id']) ?>
                    <div class="mb-3">
                        <label class="form-label">Jury</label>
                        <div id="applied-session-jury-name" class="fw-bold"></div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="applied-session-id">Judging Session</label>
                        <?= Html::dropDownList('application_judging_session_id', null, $appliedSessionArray, [
                            'id' => 'applied-session-id',
                            'class' => 'form-select',
                            'prompt' => 'No Session',
                        ]) ?>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <?= Html::submitButton('Update Session', ['class' => 'btn btn-primary']) ?>
                </div>
                <?= Html::endForm() ?>
            </div>
        </div>
    </div>

    <?php 
    $fstyle = 'style="display:none"';
    $session = Yii::$app->session;
    if ($session->has('keep-open') && $session->get('keep-open') == 1){
        $fstyle = '';
    }
    $form = ActiveForm::begin(['id' => 'jury-assign-form']); ?>
    <div class="card" <?=$fstyle?> id="con-jury-form">
    <div class="card-header">Jury Assignment Form</div>
    <div class="card-body pt-4">
    <?= $this->render('_form_jury', [
        'model' => $model,
        'form' => $form,
        'program' => $program,
        'programSub' => $programSub,
    ]) ?>
</div></div>




    <div class="card">
            <div class="card-body pt-4">
            <div class="table-responsive">

    <?php
    $colums[] = ['class' => 'yii\grid\CheckboxColumn'];
    $colums[] = ['class' => 'yii\grid\SerialColumn'];

    /* $colums[] = [
        'label' =>'Date Time',
        'attribute' => 'dateSearch',
        'value' => function($model){
            return $model->submitted_at;
        }
    ]; */
    

    if(true){ //$program->id == 1
        $colums[] = [
            'label' =>'Participants',
            'attribute' => 'fullnameSearch',
            'format' => 'html',
            'value' => function($model){
                $html = '';
                if($model->flag == 1){
                    $html .= '<i class="bi bi-flag-fill" style="color:blue"></i> ';
                }

                if($model->user){
                    $leaderName = trim((string)$model->user->fullname);
                    $leaderMatric = trim((string)$model->user->matric);
                }else if(!empty($model->contact_person)){
                    $leaderName = trim((string)$model->contact_person);
                    $leaderMatric = '';
                }else if(!empty($model->contact_email)){
                    $leaderName = trim((string)$model->contact_email);
                    $leaderMatric = '';
                }else{
                    $leaderName = 'Participant';
                    $leaderMatric = '';
                }

                if(!empty($model->group_name)){
                    $html .= '<div><span class="participant-cell-group">' . Html::encode($model->group_name) . '</span></div>';
                }
                $leaderText = $leaderName;
                if($leaderMatric !== ''){
                    $leaderText .= ' (' . $leaderMatric . ')';
                }
                $html .= '<div class="participant-cell-main">' . Html::encode($leaderText) . '</div>';

                $memberItems = [];
                foreach($model->members as $member){
                    $memberName = trim((string)$member->member_name);
                    $memberMatric = trim((string)$member->member_matric);
                    if($memberName === ''){
                        continue;
                    }
                    if($leaderMatric !== '' && $memberMatric !== '' && strcasecmp($leaderMatric, $memberMatric) === 0){
                        continue;
                    }
                    if(strcasecmp($leaderName, $memberName) === 0){
                        continue;
                    }
                    $memberLabel = $memberName;
                    if($memberMatric !== ''){
                        $memberLabel .= ' (' . $memberMatric . ')';
                    }
                    $memberItems[] = '<li>' . Html::encode($memberLabel) . '</li>';
                }

                if($memberItems){
                    $html .= '<ul class="participant-cell-members">' . implode('', $memberItems) . '</ul>';
                }

                return $html;
            }
        ];

        $colums[] = [
            'label' =>'Assigned Juries',
            'format' => 'raw',
            'value' => function($model){
                $juries = $model->juries;
                $html = '';
                if($juries){
                    $html .= '<ul>';
                    foreach($juries as $jury){
                        $html .= $jury->infoHtml(true);
                    }
                    $html .= '<ul>';
                }
                return $html;
            }
        ];
    }

    $colums[] = ['class' => 'yii\grid\ActionColumn',
'template' => '{view} {flag}',
//'visible' => false,
'buttons'=>[
    'view'=>function ($url, $model) {
        $url = ['manager-view', 'id' => $model->id];
        if($model->programSub){
            $url = ['manager-view', 'id' => $model->id, 'sub' => $model->programSub->id];
        }
        return Html::a('<span class="bi bi-eye"></span> View',$url,['class'=>'btn btn-primary btn-sm']);
    },
    'flag'=>function ($url, $model) {
        if($model->flag == 0){
            $url = ['manager-flag', 'id' => $model->id];
            if($model->programSub){
                $url = ['manager-flag', 'id' => $model->id, 'flag' => 1, 'sub' => $model->programSub->id];
            }
            return Html::a('<span class="bi bi-flag"></span> Flag', $url,['class'=>'btn btn-warning btn-sm']);
        }else if($model->flag == 1){
            $url = ['manager-flag', 'id' => $model->id];
            if($model->programSub){
                $url = ['manager-flag', 'id' => $model->id,'flag' => 0, 'sub' => $model->programSub->id];
            }
            return Html::a('<span class="bi bi-flag"></span> Unflag', $url,['class'=>'btn btn-outline-warning btn-sm']);
        }
    },
],

];
    
    echo GridView::widget([
        'dataProvider' => $dataProvider,
                'pager' => [
            'class' => 'yii\bootstrap5\LinkPager',
        ],
                'pager' => [
            'class' => 'yii\bootstrap5\LinkPager',
        ],
        //'filterModel' => $searchModel,
        'columns' => $colums,
    ]); ?>

    <div class="mt-3">
        <?= Html::button('Delete Selected Judging Input', [
            'id' => 'btn-reset-judging-input',
            'class' => 'btn btn-outline-warning',
        ]) ?>
        <?= Html::button('Delete Selected Jury Assignments', [
            'id' => 'btn-delete-jury-assignments',
            'class' => 'btn btn-outline-danger',
        ]) ?>
    </div>

</div>
            </div>
        </div>


        <?php ActiveForm::end(); ?>
        <?= Html::beginForm(['manager', 'id' => $program->id, 'sub' => $programSub ? $programSub->id : null], 'post', ['id' => 'reset-judging-input-form', 'style' => 'display:none']) ?>
            <?= Html::hiddenInput('action', 'reset_judging_input') ?>
        <?= Html::endForm() ?>
        <?= Html::beginForm(['manager', 'id' => $program->id, 'sub' => $programSub ? $programSub->id : null], 'post', ['id' => 'delete-jury-assignments-form', 'style' => 'display:none']) ?>
            <?= Html::hiddenInput('action', 'delete_jury_assignments') ?>
        <?= Html::endForm() ?>
        <?php
        $this->registerJs(<<<JS
function getSelectedParticipants(){
    return $("input[name='selection[]']:checked").map(function(){
        return $(this).val();
    }).get();
}

function appendSelectedParticipants(form, selected){
    form.find("input[name='selection[]']").remove();
    selected.forEach(function(id){
        $("<input>", {
            type: "hidden",
            name: "selection[]",
            value: id
        }).appendTo(form);
    });
}

function getSelectedAppliedJuries(){
    return $("input[name='jury_application_ids[]']:checked").map(function(){
        return $(this).val();
    }).get();
}

$("#btn-reset-judging-input").on("click", function(){
    var selected = getSelectedParticipants();

    if(selected.length === 0){
        alert("Please select at least one participant first.");
        return false;
    }

    if(!confirm("Delete judging input for selected participants and revert their jury assignments to ASSIGNED?")){
        return false;
    }

    var form = $("#reset-judging-input-form");
    appendSelectedParticipants(form, selected);
    form[0].submit();
});

$("#btn-delete-jury-assignments").on("click", function(){
    var selected = getSelectedParticipants();

    if(selected.length === 0){
        alert("Please select at least one participant first.");
        return false;
    }

    if(!confirm("Delete ALL ASSIGNED jury assignments for selected participants? Participants with Judging or Complete assignments will be skipped.")){
        return false;
    }

    var form = $("#delete-jury-assignments-form");
    appendSelectedParticipants(form, selected);
    form[0].submit();
});

$("#select-all-applied-juries").on("change", function(){
    $(".applied-jury-check").prop("checked", $(this).is(":checked"));
});

$(".applied-jury-check").on("change", function(){
    var total = $(".applied-jury-check").length;
    var checked = $(".applied-jury-check:checked").length;
    $("#select-all-applied-juries").prop("checked", total > 0 && total === checked);
});

$("#hide-applied-juries").on("click", function(){
    $("#con-applied-juries").slideUp();
});

$(".btn-update-applied-session").on("click", function(){
    $("#applied-session-application-id").val($(this).data("id"));
    $("#applied-session-jury-name").text($(this).data("name"));
    $("#applied-session-id").val($(this).data("session"));

    var modalEl = document.getElementById("applied-jury-session-modal");
    if(window.bootstrap && bootstrap.Modal){
        bootstrap.Modal.getOrCreateInstance(modalEl).show();
    }else{
        $("#applied-jury-session-modal").modal("show");
    }
});

$("#btn-assign-applied-juries").on("click", function(){
    var selectedParticipants = getSelectedParticipants();
    if(selectedParticipants.length === 0){
        alert("Please select participant(s) first before assigning applied juries.");
        return false;
    }

    if(getSelectedAppliedJuries().length === 0){
        alert("Please select at least one applied jury first.");
        return false;
    }

    appendSelectedParticipants($("#applied-jury-form"), selectedParticipants);
});

$("#btn-auto-assign-applied-juries").on("click", function(){
    if(getSelectedAppliedJuries().length === 0){
        alert("Please select at least one applied jury first.");
        return false;
    }

    if(!confirm("Auto assign selected applied juries evenly to participant groups in this program/category?")){
        return false;
    }

    $("#applied-jury-form").find("input[name='selection[]']").remove();
});
JS);
        ?>
        <i>* if you want to select multiple participants, but they are in different pages, flag them first to be on top of the list first.</i>
    </section>
