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
                            'label' => 'Program / Sub Program',
                            'value' => function($model){
                                $name = $model->program ? $model->program->program_name : null;
                                if($model->programSub){
                                    $sub = $model->programSub->sub_name;
                                    if($name){
                                        return $name . ' / ' . $sub;
                                    }
                                    return $sub;
                                }
                                return $name;
                            }
                        ],
                        [
                            'label' => 'Session',
                            'value' => function($model){
                                if(!$model->judgingSession){
                                    return null;
                                }

                                $s = $model->judgingSession;
                                $start = $s->datetime_start ? new \DateTime($s->datetime_start) : null;
                                $end = $s->datetime_end ? new \DateTime($s->datetime_end) : null;

                                $range = '';
                                if($start && $end){
                                    if($start->format('Y-m-d') === $end->format('Y-m-d')){
                                        $range = $start->format('d M Y') . ' ' . $start->format('H:i') . ' - ' . $end->format('H:i');
                                    }else{
                                        $range = $start->format('d M Y H:i') . ' - ' . $end->format('d M Y H:i');
                                    }
                                }elseif($start){
                                    $range = $start->format('d M Y H:i');
                                }

                                if($range !== ''){
                                    return $s->session_name . ' (' . $range . ')';
                                }

                                return $s->session_name;
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
