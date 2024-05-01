<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\moduleAnjurSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Admin Anjur';
$this->params['breadcrumbs'][] = $this->title;
?>
<p>
    <?= Html::a('Create Admin Anjur', ['create'], ['class' => 'btn btn-success']) ?>
</p>
<br/>

<div class="box">
<div class="box-header"></div>
<div class="box-body">
<div class="module-anjur-index">   

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        // 'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            [
                'label' => 'Module Name',
                'value' => function($model){
                    return $model->module->module_name;
                    
                }
            ],
            'module_siri',
            'date_start',
            'date_end',
            'capacity',
            'location',
            [
                'format' => 'html',
                'label' => 'Total Participant',
                'value' => function($model){
                    return Html::a($model->getCountPeserta($model->id),['view', 'id' => $model->id]);
                }
            ],

            ['class' => 'yii\grid\ActionColumn',
                'contentOptions' => ['style' => 'width: 18%'],
                'template' => '{view} {delete}',
                //'visible' => false,
                'buttons'=>[
                'view'=>function ($url, $model) {
                    return Html::a('<span class="fa fa-eye"></span> View',['view', 'id' => $model->id],['class'=>'btn btn-primary btn-sm']);
                },
                'delete'=>function ($url, $model) {
                    return Html::a('<span class="fa fa-trash"></span>',['delete', 'id' => $model->id],['class'=>'btn btn-danger btn-sm', 'data' => [
                    'confirm' => 'Are you sure to delete this data?'
                    ],
                    ]);
                }
                ],
                
                ],
        ],
    ]); ?>
</div>
</div>
</div>
