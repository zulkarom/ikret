<?php

use yii\grid\GridView;
use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var yii\data\ActiveDataProvider $dataProvider */
/** @var bool $callForJuriesEnabled */

$this->title = 'Jury Requirements';
$this->params['breadcrumbs'][] = $this->title;

?>

<div class="pagetitle">
    <h1><?=$this->title?></h1>
</div>

</div><!-- End Page Title -->

<section class="section">
    <div class="card">
        <div class="card-body pt-4">

            <p>
                <?= Html::a('Create', ['create'], ['class' => 'btn btn-primary']) ?>
            </p>

            <p>
                <?php if(!empty($callForJuriesEnabled)): ?>
                    <?= Html::a('Public Form: ON (click to turn OFF)', ['toggle-call-for-juries'], [
                        'class' => 'btn btn-success',
                        'data' => ['method' => 'post'],
                    ]) ?>
                <?php else: ?>
                    <?= Html::a('Public Form: OFF (click to turn ON)', ['toggle-call-for-juries'], [
                        'class' => 'btn btn-danger',
                        'data' => ['method' => 'post'],
                    ]) ?>
                <?php endif; ?>
            </p>

            <div class="table-responsive">
                <?= GridView::widget([
                    'dataProvider' => $dataProvider,
                    'pager' => [
                        'class' => 'yii\bootstrap5\LinkPager',
                    ],
                    'columns' => [
                        ['class' => 'yii\grid\SerialColumn'],
                        'id',
                        [
                            'label' => 'Program',
                            'value' => function($model){
                                return $model->program ? $model->program->program_name : null;
                            }
                        ],
                        [
                            'label' => 'Program Sub',
                            'value' => function($model){
                                return $model->programSub ? $model->programSub->sub_name : null;
                            }
                        ],
                        [
                            'label' => 'Session',
                            'value' => function($model){
                                return $model->judgingSession ? $model->judgingSession->session_name : null;
                            }
                        ],
                        [
                            'attribute' => 'is_required',
                            'value' => function($model){
                                return (int)$model->is_required === 1 ? 'YES' : 'NO';
                            }
                        ],
                        [
                            'attribute' => 'is_active',
                            'value' => function($model){
                                return (int)$model->is_active === 1 ? 'OPEN' : 'CLOSED';
                            }
                        ],
                        'jury_limit',
                        [
                            'class' => 'yii\grid\ActionColumn',
                            'template' => '{update} {delete}',
                            'buttons' => [
                                'update' => function($url, $model){
                                    return Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-secondary btn-sm']);
                                },
                                'delete' => function($url, $model){
                                    return Html::a('Delete', ['delete', 'id' => $model->id], [
                                        'class' => 'btn btn-danger btn-sm',
                                        'data' => [
                                            'confirm' => 'Are you sure?',
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
