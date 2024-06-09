<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\SessionAttendance $model */

$this->title = 'Update Session Attendance: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Session Attendances', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="session-attendance-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
