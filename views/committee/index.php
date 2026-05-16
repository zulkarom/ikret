<?php

use app\models\Committee;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Committees';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="pagetitle">
    <h1><?= Html::encode($this->title) ?></h1>
</div>

<section class="section dashboard">
    <div class="card">
        <div class="card-body pt-4">
            <p>
                <?= Html::a('Create Committee', ['create'], ['class' => 'btn btn-success']) ?>
                <?= Html::a('Import CSV', ['import'], ['class' => 'btn btn-primary']) ?>
            </p>

            <div class="table-responsive">
                <?= GridView::widget([
                    'dataProvider' => $dataProvider,
                    'pager' => [
                        'class' => 'yii\bootstrap5\LinkPager',
                    ],
                    'columns' => [
                        ['class' => 'yii\grid\SerialColumn'],
                        'com_name_en',
                        'com_name',
                        [
                            'attribute' => 'is_jawatankuasa',
                            'value' => function($model){
                                return Committee::yesNoOptions()[(int)$model->is_jawatankuasa] ?? 'No';
                            },
                        ],
                        [
                            'attribute' => 'is_student',
                            'value' => function($model){
                                return Committee::yesNoOptions()[(int)$model->is_student] ?? 'No';
                            },
                        ],
                        [
                            'label' => 'Members',
                            'value' => function($model){
                                return $model->getUserRoles()->count();
                            },
                        ],
                        [
                            'class' => ActionColumn::class,
                            'contentOptions' => ['style' => 'width: 22%'],
                            'template' => '{view} {update} {delete}',
                            'buttons' => [
                                'view' => function ($url, $model) {
                                    return Html::a('<span class="bi bi-eye"></span> View', ['view', 'id' => $model->id], ['class' => 'btn btn-primary btn-sm']);
                                },
                                'update' => function ($url, $model) {
                                    return Html::a('<span class="bi bi-pencil"></span> Update', ['update', 'id' => $model->id], ['class' => 'btn btn-warning btn-sm']);
                                },
                                'delete' => function ($url, $model) {
                                    return Html::a('<span class="bi bi-trash"></span>', ['delete', 'id' => $model->id], [
                                        'class' => 'btn btn-danger btn-sm',
                                        'title' => 'Delete',
                                        'aria-label' => 'Delete',
                                        'data' => [
                                            'confirm' => 'Are you sure you want to delete this committee?',
                                            'method' => 'post',
                                        ],
                                    ]);
                                },
                            ],
                        ],
                    ],
                ]); ?>
            </div>
        </div>
    </div>
</section>
