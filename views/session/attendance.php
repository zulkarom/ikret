<?php

use app\models\SessionAttendance;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var app\models\SessionAttendanceSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Session Attendances';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="session-attendance-index">
<div class="pagetitle">
<h1><?= Html::encode($this->title) ?></h1></div>

    </div>

    <div class="card">
<div class="card-body pt-4">

<?= GridView::widget([
        'dataProvider' => $dataProvider,
        //'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'session.session_name',
            'user.fullname',
            'scanned_at',

        ],
    ]); ?>

</div></div>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>




</div>
