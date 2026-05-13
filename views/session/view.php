<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var app\models\Session $model */

$this->title = $model->session_name;
$this->params['breadcrumbs'][] = ['label' => 'Sessions', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
$attendanceCount = (int)$model->getSessionAttendances()->count();
$canDelete = (Yii::$app->user->identity->isManager || Yii::$app->user->identity->isAdminRegistration) && $attendanceCount === 0;
$canUpdate = Yii::$app->user->identity->isManager || Yii::$app->user->identity->isAdminRegistration;
\yii\web\YiiAsset::register($this);
?>
<div class="session-view">

    <div class="pagetitle d-flex flex-wrap justify-content-between align-items-center gap-2">
        <div>
            <h1><?= Html::encode($model->session_name) ?></h1>
            <div class="text-muted">
                <?= Html::encode($model->programNameShort ?: 'General session') ?> ·
                <?= Html::encode($attendanceCount . ' attendance record' . ($attendanceCount === 1 ? '' : 's')) ?>
            </div>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <?= Html::a('<i class="bi bi-arrow-left"></i> Back', ['index'], ['class' => 'btn btn-outline-secondary']) ?>
            <?= Html::a('<i class="bi bi-qr-code"></i> QR Code', ['qrpdf', 'id' => $model->id], ['class' => 'btn btn-danger', 'target' => '_blank']) ?>
            <?php if($canUpdate): ?>
                <?= Html::a('<i class="bi bi-pencil-square"></i> Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
            <?php endif; ?>
            <?php if($canDelete): ?>
                <?= Html::a('<i class="bi bi-trash"></i> Delete', ['delete', 'id' => $model->id], [
                    'class' => 'btn btn-outline-danger',
                    'data' => [
                        'confirm' => 'Delete this session? This cannot be undone.',
                        'method' => 'post',
                    ],
                ]) ?>
            <?php endif; ?>
        </div>
    </div>

    <?php if(!$canDelete && $attendanceCount > 0): ?>
        <div class="alert alert-info">
            This session already has attendance records, so it cannot be deleted.
        </div>
    <?php endif; ?>

    <div class="card">
        <div class="card-body pt-4">
            <?= DetailView::widget([
                'model' => $model,
                'options' => ['class' => 'table table-bordered detail-view mb-0'],
                'attributes' => [
                    'id',
                    'session_name',
                    [
                        'attribute' => 'speaker',
                        'value' => $model->speaker ?: 'N/A',
                    ],
                    [
                        'label' => 'Program',
                        'value' => $model->programNameShort ?: 'N/A',
                    ],
                    [
                        'attribute' => 'datetime_start',
                        'value' => $model->formatLocalDateTime('datetime_start'),
                    ],
                    [
                        'attribute' => 'datetime_end',
                        'value' => $model->formatLocalDateTime('datetime_end'),
                    ],
                    [
                        'label' => 'Attendance Records',
                        'value' => $attendanceCount,
                    ],
                    [
                        'attribute' => 'allow_scan_outside_duration',
                        'value' => $model->allow_scan_outside_duration ? 'Yes' : 'No',
                    ],
                    [
                        'attribute' => 'allow_scan_1_hour_after_event',
                        'value' => $model->allow_scan_1_hour_after_event ? 'Yes' : 'No',
                    ],
                    'token:ntext',
                ],
            ]) ?>
        </div>
    </div>

</div>
