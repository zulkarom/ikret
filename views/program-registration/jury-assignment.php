<?php

use yii\helpers\Html;
use yii\grid\GridView;
use app\models\RubricJudgingSession;

/** @var yii\web\View $this */
/** @var app\models\ProgramRegistrationSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Jury Assignments';
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

$this->registerCss(<<<CSS
.participant-cell-main {
    font-weight: 700;
}

.participant-cell-group {
    display: inline-block;
    margin-bottom: 0.35rem;
    padding: 0.28rem 0.65rem;
    border-radius: 999px;
    font-size: 0.8rem;
    font-weight: 700;
    line-height: 1.2;
    color: #fff;
    background: #198754;
    box-shadow: inset 0 -1px 0 rgba(0, 0, 0, 0.12);
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

@media (min-width: 768px) {
    .jury-assignment-grid .jury-assignment-action {
        width: 13%;
    }
}

@media (max-width: 767.98px) {
    .jury-assignment-grid table,
    .jury-assignment-grid thead,
    .jury-assignment-grid tbody,
    .jury-assignment-grid tr,
    .jury-assignment-grid th,
    .jury-assignment-grid td {
        display: block;
        width: 100%;
    }

    .jury-assignment-grid thead {
        display: none;
    }

    .jury-assignment-grid tbody tr {
        margin-bottom: 1rem;
        border: 1px solid #dee2e6;
        border-radius: 0.5rem;
        background: #fff;
        overflow: hidden;
    }

    .jury-assignment-grid tbody td {
        display: grid;
        grid-template-columns: 9.5rem minmax(0, 1fr);
        gap: 0.75rem;
        border: 0;
        border-bottom: 1px solid #f1f3f5;
        padding: 0.75rem;
        text-align: left;
    }

    .jury-assignment-grid tbody td:last-child {
        border-bottom: 0;
    }

    .jury-assignment-grid tbody td::before {
        content: attr(data-label);
        grid-column: 1;
        color: #6c757d;
        font-size: 0.78rem;
        font-weight: 700;
        text-transform: uppercase;
    }

    .jury-assignment-grid tbody td > * {
        grid-column: 2;
    }

    .jury-assignment-grid .participant-cell-group,
    .jury-assignment-grid .badge,
    .jury-assignment-grid .btn {
        width: auto;
        justify-self: start;
    }

    .jury-assignment-grid tbody td[data-label="#"] {
        display: none;
    }
}
CSS);
?>
  <div class="pagetitle">
<h1><?=$this->title?></h1></div>

    </div><!-- End Page Title -->

    <section class="section dashboard">

    <div class="card">
            <div class="card-body pt-4">
            <div class="table-responsive">

    <?= GridView::widget([
        'options' => ['class' => 'grid-view jury-assignment-grid'],
        'dataProvider' => $dataProvider,
                'pager' => [
            'class' => 'yii\bootstrap5\LinkPager',
        ],
       // 'filterModel' => $searchModel,
        'columns' => [
            [
                'class' => 'yii\grid\SerialColumn',
                'contentOptions' => ['data-label' => '#'],
            ],
            [
                'label' =>'Competition',
                'contentOptions' => ['data-label' => 'Competition'],
                'value' => function($model){
                    $registration = $model->registration;
                    $programText = $registration && $registration->program ? $registration->program->program_abbr : '';
                    if($registration && $registration->programSub){
                        $programText .= ' / ' . $registration->programSub->sub_name;
                    }
                    return $programText;
                }
            ],
            [
                'label' =>'Participants',
                'format' => 'html',
                'contentOptions' => ['data-label' => 'Participants'],
                'value' => function($model){
                    $registration = $model->registration;
                    if(!$registration){
                        return '';
                    }

                    $html = '';
                    if($registration->flag == 1){
                        $html .= '<i class="bi bi-flag-fill" style="color:blue"></i> ';
                    }

                    if($registration->user){
                        $text = trim((string)$registration->user->fullname);
                    }else if(!empty($registration->contact_person)){
                        $text = trim((string)$registration->contact_person);
                    }else if(!empty($registration->contact_email)){
                        $text = trim((string)$registration->contact_email);
                    }else{
                        $text = 'Participant';
                    }

                    if(!empty($registration->group_name)){
                        $html .= '<div><span class="participant-cell-group">' . Html::encode($registration->group_name) . '</span></div>';
                    }
                    $html .= '<div class="participant-cell-main">' . Html::encode($text) . '</div>';

                    return $html;
                }
            ],
            [
                'label' => 'Session',
                'format' => 'html',
                'contentOptions' => ['data-label' => 'Session'],
                'value' => function($model) use ($formatDatetimeRange, $modeList){
                    $session = $model->judgingSession;
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
                },
            ],
            [
                'attribute' => 'statusLabel',
                'label' => 'Status',
                'format' => 'html',
                'contentOptions' => ['data-label' => 'Status'],
            ],
            [
                'label' =>'Result',
                'format' => 'html',
                'contentOptions' => ['data-label' => 'Result'],
                'value' => function($model){
                    if((int)$model->is_nullified === 1){
                        return '<span class="badge bg-danger">Nullified</span>';
                    }
                    if($model->score){
                        return $model->score;
                    }else{
                        return 0;
                    }
                }
            ],

            ['class' => 'yii\grid\ActionColumn',
                'contentOptions' => ['class' => 'jury-assignment-action', 'data-label' => 'Action'],
            'template' => '{view}',
            //'visible' => false,
            'buttons'=>[
                'view'=>function ($url, $model) {
                    return Html::a('<i class="bi bi-pencil-square"></i> Judge',['jury-judge', 'id' => $model->id],['class'=>'btn btn-primary btn-sm']);
                },
            ],
        
        ],
  
        ],
    ]); ?>

</div>
            </div>
        </div>



    </section>
