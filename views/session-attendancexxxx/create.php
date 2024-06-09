<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\SessionAttendance $model */

$this->title = 'Create Session Attendance';
$this->params['breadcrumbs'][] = ['label' => 'Session Attendances', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="session-attendance-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
