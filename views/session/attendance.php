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
        'pager' => [
            'class' => 'yii\bootstrap5\LinkPager',
        ],
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            [
                'label' =>'Session Name',
                'attribute' => 'session_id',
                'filter' => Html::activeDropDownList($searchModel, 'session_id', $searchModel->listSessions(),['class'=> 'form-control','prompt' => 'Choose Session']),
                'value' => function($model){
                    return $model->session->session_name;
                }
            ],
            [
                'label' =>'Participants',
                'attribute' => 'fullname',
                'value' => function($model){
                    return $model->user->fullname;
                }
            ],
            'scanned_at',
            [
                'class' => ActionColumn::class,
                'template' => '{view} {delete}',
                'urlCreator' => function ($action, SessionAttendance $model, $key, $index, $column) {
                    if ($action === 'view') {
                        return Url::to(['attendance-view', 'id' => $model->id]);
                    }

                    if ($action === 'delete') {
                        return Url::to(['attendance-delete', 'id' => $model->id]);
                    }

                    return '#';
                },
                'buttons' => [
                    'view' => function ($url, $model, $key) {
                        return Html::a('<i class="bi bi-eye"></i>', $url, [
                            'title' => 'View',
                            'aria-label' => 'View',
                            'class' => 'btn btn-sm btn-outline-primary',
                        ]);
                    },
                    'delete' => function ($url, $model, $key) {
                        return Html::a('<i class="bi bi-trash"></i>', $url, [
                            'title' => 'Delete',
                            'aria-label' => 'Delete',
                            'class' => 'btn btn-sm btn-outline-danger',
                            'data' => [
                                'confirm' => 'Delete this attendance record?',
                                'method' => 'post',
                            ],
                        ]);
                    },
                ],
                'contentOptions' => ['class' => 'text-nowrap'],
            ],

        ],
    ]); ?>

</div></div>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>




</div>
