<?php

use yii\helpers\Html;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var app\models\SessionSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Sessions';
$this->params['breadcrumbs'][] = $this->title;
$canCreateSession = Yii::$app->user->identity->isManager || Yii::$app->user->identity->isAdminRegistration;
$canUpdateSession = Yii::$app->user->identity->isManager || Yii::$app->user->identity->isAdminRegistration;
?>
<div class="session-index">

    <div class="pagetitle d-flex flex-wrap justify-content-between align-items-center gap-2">
        <div>
            <h1><?= Html::encode($this->title) ?></h1>
            <div class="text-muted">Manage event sessions, QR codes, and attendance scan windows.</div>
        </div>
        <?php if($canCreateSession): ?>
            <?= Html::a('<i class="bi bi-plus-circle"></i> Create Session', ['create'], ['class' => 'btn btn-success']) ?>
        <?php endif; ?>
    </div>

    <div class="card mt-3">
        <div class="card-body pt-4">
            <?= $this->render('_search', [
                'model' => $searchModel,
            ]) ?>
            <hr>
            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'layout' => "{summary}\n<div class=\"table-responsive\">{items}</div>\n{pager}",
                'tableOptions' => ['class' => 'table table-hover align-middle'],
                'columns' => [
                    ['class' => 'yii\grid\SerialColumn'],
                    [
                        'attribute' => 'session_name',
                        'format' => 'raw',
                        'value' => function($model){
                            $program = $model->programNameShort;
                            $html = Html::a(Html::encode($model->session_name), ['view', 'id' => $model->id], ['class' => 'fw-semibold']);
                            if($program){
                                $html .= '<div class="small text-muted">' . Html::encode($program) . '</div>';
                            }
                            return $html;
                        },
                    ],
                    [
                        'attribute' => 'datetime_start',
                        'label' => 'Date / Time',
                        'format' => 'raw',
                        'value' => function($model){
                            $startValue = trim((string)$model->datetime_start);
                            $endValue = trim((string)$model->datetime_end);
                            if($startValue === '' || $endValue === ''){
                                return Html::encode($model->formatLocalDateTime('datetime_start') ?: $model->formatLocalDateTime('datetime_end'));
                            }

                            try {
                                $timezone = new \DateTimeZone('Asia/Kuala_Lumpur');
                                $start = new \DateTimeImmutable($startValue, $timezone);
                                $end = new \DateTimeImmutable($endValue, $timezone);

                                if($start->format('Y-m-d') === $end->format('Y-m-d')){
                                    return Html::encode($start->format('d M Y'))
                                        . '<div class="small text-muted">'
                                        . Html::encode($start->format('h:i A') . ' - ' . $end->format('h:i A'))
                                        . '</div>';
                                }

                                return Html::encode($start->format('d M Y h:i A'))
                                    . '<div class="small text-muted">'
                                    . Html::encode($end->format('d M Y h:i A'))
                                    . '</div>';
                            } catch(\Exception $e) {
                                return Html::encode($model->formatLocalDateTime('datetime_start'))
                                    . '<div class="small text-muted">'
                                    . Html::encode($model->formatLocalDateTime('datetime_end'))
                                    . '</div>';
                            }
                        },
                    ],
                    [
                        'label' => 'Scan Window',
                        'format' => 'raw',
                        'value' => function($model){
                            if((int)$model->allow_scan_outside_duration === 1){
                                return '<span class="badge bg-danger">Any time</span>';
                            }
                            if((int)$model->allow_scan_1_hour_after_event === 1){
                                return '<span class="badge bg-warning text-dark">Until 1 hour after end</span>';
                            }
                            return '<span class="badge bg-secondary">Event duration only</span>';
                        },
                    ],
                    [
                        'attribute' => 'has_session_certificate',
                        'label' => 'Special Cert.',
                        'format' => 'raw',
                        'value' => function($model){
                            if((int)$model->has_session_certificate === 1){
                                return '<span class="badge bg-success">Yes</span>';
                            }
                            return '<span class="badge bg-secondary">No</span>';
                        },
                    ],
                    [
                        'label' => 'Attendance',
                        'value' => function($model){
                            return (int)$model->getSessionAttendances()->count();
                        },
                    ],
                    [
                        'label' => '',
                        'format' => 'raw',
                        'contentOptions' => ['class' => 'text-end text-nowrap'],
                        'value' => function($model) use ($canUpdateSession){
                            $buttons = [
                                Html::a('<i class="bi bi-qr-code"></i> QR', ['qrpdf', 'id' => $model->id], ['class' => 'btn btn-danger btn-sm', 'target' => '_blank']),
                                Html::a('<i class="bi bi-eye"></i> View', ['view', 'id' => $model->id], ['class' => 'btn btn-outline-primary btn-sm']),
                            ];
                            if($canUpdateSession){
                                $buttons[] = Html::a('<i class="bi bi-pencil-square"></i> Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary btn-sm']);
                            }
                            return implode(' ', $buttons);
                        },
                    ],
                ],
            ]); ?>
        </div>
    </div>


</div>
