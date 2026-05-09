<?php

use yii\grid\GridView;
use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'All Participants';
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
                    'pager' => [
                        'class' => 'yii\bootstrap5\LinkPager',
                    ],
                    'columns' => [
                        ['class' => 'yii\grid\SerialColumn'],
                        [
                            'label' => 'Participant',
                            'format' => 'html',
                            'value' => function($model){
                                return $model->shortFieldsHtml;
                            },
                        ],
                        [
                            'label' => 'Members',
                            'format' => 'raw',
                            'value' => function($model){
                                return $model->memberStr;
                            },
                        ],
                        [
                            'label' => 'Certificates',
                            'format' => 'raw',
                            'value' => function($model){
                                $links = [];
                                $links[] = Html::a('Participation', ['/program/cert-participation', 'reg' => $model->id], ['class' => 'btn btn-primary btn-sm mb-1', 'target' => '_blank']);

                                if($model->award > 0){
                                    $links[] = Html::a('Achievement', ['/program/cert-achievement', 'reg' => $model->id], ['class' => 'btn btn-outline-primary btn-sm mb-1', 'target' => '_blank']);
                                }

                                if($model->achievements){
                                    foreach($model->achievements as $achievement){
                                        $label = $achievement->achieve ? 'Excellence: ' . $achievement->achieve->name : 'Excellence';
                                        $links[] = Html::a(Html::encode($label), ['/program/cert-excellence', 'reg' => $model->id], ['class' => 'btn btn-outline-primary btn-sm mb-1', 'target' => '_blank']);
                                    }
                                }

                                return implode(' ', $links);
                            },
                        ],
                    ],
                ]) ?>
            </div>
        </div>
    </div>
</section>
