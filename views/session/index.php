<?php

use app\models\Session;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var app\models\SessionSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Sessions';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="session-index">

<div class="pagetitle" >
<h1><?= Html::encode($this->title) ?></h1>

    <?php if(Yii::$app->user->identity->isManager): ?>
        <p>
            <?= Html::a('Create Session', ['create'], ['class' => 'btn btn-success']) ?>
        </p>
    <?php endif; ?>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        //'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            //'id',
            'session_name',
            
            //'program_id',
            //'program_sub',
            'datetime_start',
            'datetime_end',
            [
                'label' => 'Scan Window',
                'value' => function($model){
                    if((int)$model->allow_scan_outside_duration === 1){
                        return 'Any time';
                    }
                    if((int)$model->allow_scan_1_hour_after_event === 1){
                        return 'Until 1 hour after end';
                    }
                    return 'Event duration only';
                },
            ],
            [
                'label' =>'Program',
                'value' => function($model){
                    if($model->program){
                        return $model->programNameShort;
                    }
                    
                }
            ],
            
            //'token:ntext',
            ['class' => 'yii\grid\ActionColumn',
            //'format' => 'raw',
            'contentOptions' => ['style' => 'width: 15%'],
                            'template' => Yii::$app->user->identity->isManager ? '{view} {update}' : '{view}',
                            
                            //'visible' => false,
                            'buttons'=>[
                                'view'=>function ($url, $model) {
                                    return Html::a('QR CODE',['qrpdf', 'id' => $model->id],['class'=>'btn btn-danger btn-sm', 'target' => '_blank']);
                                },
                                'update'=>function ($url, $model) {
                                    return Html::a('Update',['update', 'id' => $model->id],['class'=>'btn btn-primary btn-sm']);
                                }
                            ],
                        
                        ],
        ],
    ]); ?>


</div>
