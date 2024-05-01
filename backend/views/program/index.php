<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\ProgramSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Programs';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="program-index">

    <div class="row">
        <div class="col-md-4">
            <?= Html::a('Create Program', ['create'], ['class' => 'btn btn-success']) ?>
        </div>

        <div class="col-md-4"></div>
    
        <div class="col-md-4" align="right">
            <?= $this->render('_form_search', [
                'model' => $searchModel,
            ]) ?>
        </div>
    </div>

    <div class="card">
    <div class="card-body">

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        //'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'entrepreneurName',
            'prog_name',
            [
             'label' => 'Program Anjuran',
             'value' => function($model){
                return $model->anjuranText;
             }
            ],
            [
             'label' => 'Date Program',
             'value' => function($model){
                return date('d M Y', strtotime($model->prog_date));
             }
            ],

            ['class' => 'yii\grid\ActionColumn',
            //     'contentOptions' => ['style' => 'width: 13%'],
                'template' => '{view} {delete}',
                //'visible' => false,
                'buttons'=>[
                    'delete'=>function ($url, $model) {
                    return Html::a('<span class="fa fa-trash"></span> '.\Yii::t('app', 'Delete'), ['delete', 'id' => $model->id], [
                        'class' => 'btn btn-danger btn-sm',
                        'data' => [
                            'confirm' => 'Are you sure you want to remove this item?',
                            'method' => 'post',
                        ],
                    ]);
                    },
                    'view'=>function ($url, $model) {
                        return Html::a('<span class="fa fa-search"></span> '.\Yii::t('app', 'View'), ['view', 'id' => $model->id], [
                            'class' => 'btn btn-primary btn-sm',
  
                        ]);
                    }
                ],
            
            ],
        ],
    ]); ?>
</div>
</div>

</div>
