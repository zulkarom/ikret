<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var app\models\SessionAttendance $model */

$this->title = 'Attendance Record #' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Session Attendances', 'url' => ['attendance']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="session-attendance-view">

    <div class="pagetitle d-flex flex-wrap justify-content-between align-items-center gap-2">
        <div>
            <h1><?= Html::encode($this->title) ?></h1>
            <div class="text-muted">
                <?= Html::encode($model->session ? $model->session->session_name : 'Unknown session') ?>
            </div>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <?= Html::a('<i class="bi bi-arrow-left"></i> Back', ['attendance'], ['class' => 'btn btn-outline-secondary']) ?>
            <?= Html::a('<i class="bi bi-trash"></i> Delete', ['attendance-delete', 'id' => $model->id], [
                'class' => 'btn btn-outline-danger',
                'data' => [
                    'confirm' => 'Delete this attendance record?',
                    'method' => 'post',
                ],
            ]) ?>
        </div>
    </div>

    <div class="card">
        <div class="card-body pt-4">
            <?= DetailView::widget([
                'model' => $model,
                'options' => ['class' => 'table table-bordered detail-view mb-0'],
                'attributes' => [
                    'id',
                    [
                        'label' => 'Session',
                        'value' => $model->session ? $model->session->session_name : 'N/A',
                    ],
                    [
                        'label' => 'Participant',
                        'value' => $model->user ? $model->user->fullname : 'N/A',
                    ],
                    [
                        'attribute' => 'scanned_at',
                        'format' => ['datetime', 'php:d M Y h:i:s A'],
                    ],
                ],
            ]) ?>
        </div>
    </div>

</div>
