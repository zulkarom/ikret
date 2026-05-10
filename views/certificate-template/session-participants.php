<?php

use app\models\SessionAttendance;
use yii\grid\GridView;
use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\SessionAttendanceSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'All Session Participants';
$this->params['breadcrumbs'][] = ['label' => 'Certificate Config', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="pagetitle">
    <h1><?= Html::encode($this->title) ?></h1>
</div>

</div><!-- End Page Title -->

<section class="section dashboard">
    <div class="card">
        <div class="card-body pt-4">
            <div class="table-responsive">
                <?= GridView::widget([
                    'dataProvider' => $dataProvider,
                    'filterModel' => $searchModel,
                    'pager' => [
                        'class' => 'yii\bootstrap5\LinkPager',
                    ],
                    'columns' => [
                        ['class' => 'yii\grid\SerialColumn'],
                        [
                            'label' => 'Session Name',
                            'attribute' => 'session_id',
                            'filter' => Html::activeDropDownList($searchModel, 'session_id', $searchModel->listSessions(), ['class' => 'form-control', 'prompt' => 'Choose Session']),
                            'value' => function(SessionAttendance $model){
                                return $model->session ? $model->session->session_name : '';
                            },
                        ],
                        [
                            'label' => 'Participant',
                            'attribute' => 'fullname',
                            'value' => function(SessionAttendance $model){
                                return $model->user ? $model->user->fullname : '';
                            },
                        ],
                        [
                            'label' => 'Program',
                            'value' => function(SessionAttendance $model){
                                return $model->session ? $model->session->programNameShort : '';
                            },
                        ],
                        'scanned_at',
                        [
                            'label' => 'Certificates',
                            'format' => 'raw',
                            'value' => function(SessionAttendance $model){
                                $links = [];

                                if($model->session && (int)$model->session->has_session_certificate === 1){
                                    $links[] = Html::a('Certificate of Participation Session', ['/session/attendance-cert', 'id' => $model->id], ['class' => 'btn btn-primary btn-sm mb-1', 'target' => '_blank']);
                                }else{
                                    $links[] = '<span class="text-muted me-1">No session cert</span>';
                                }
                                $links[] = Html::a('Certificate of Participation QR', ['/session/cert-qr', 'u' => $model->user_id], ['class' => 'btn btn-outline-primary btn-sm mb-1', 'target' => '_blank']);

                                return implode(' ', $links);
                            },
                            'contentOptions' => ['class' => 'text-nowrap'],
                        ],
                    ],
                ]) ?>
            </div>
        </div>
    </div>
</section>
