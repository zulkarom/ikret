<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\moduleKategoriSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Module Category';
$this->params['breadcrumbs'][] = $this->title;
?>
<p>
    <?= Html::a('Create Module Category', ['create'], ['class' => 'btn btn-success']) ?>
</p>
<br/>
<div class="card">
<div class="card-body">
<div class="module-kategori-index">

     <?= GridView::widget([
            'dataProvider' => $dataProvider,
            // 'filterModel' => $searchModel,
            'columns' => [
                ['class' => 'yii\grid\SerialColumn'],

                'kategori_name',
                'created_at:datetime',

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
