<?php

use yii\grid\GridView;
use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'All Committees';
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
                            'label' => 'Name',
                            'value' => function($model){
                                return $model->user ? $model->user->fullname : '';
                            },
                        ],
                        [
                            'label' => 'Committee',
                            'format' => 'raw',
                            'value' => function($model){
                                $text = $model->committee ? Html::encode($model->committee->com_name_en) : '';
                                if($model->committee && (int)$model->committee->is_jawatankuasa === 1){
                                    $text .= (int)$model->is_leader === 1 ? ' <b>- Leader</b>' : ' <b>- Member</b>';
                                }

                                return $text;
                            },
                        ],
                        [
                            'label' => 'Certificate',
                            'format' => 'raw',
                            'value' => function($model){
                                return Html::a('Certificate', ['/committee/certificate', 'id' => $model->id], ['class' => 'btn btn-primary btn-sm', 'target' => '_blank']);
                            },
                        ],
                    ],
                ]) ?>
            </div>
        </div>
    </div>
</section>
