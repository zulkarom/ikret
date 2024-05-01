<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\EntrepreneurSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Beneficiaries';
$this->params['breadcrumbs'][] = $this->title;
?>

<p>
    <?= Html::a('Add Beneficiary', ['create'], ['class' => 'btn btn-success']) ?>
</p>
<br/>

<div class="white_card card_height_100 mb_30">
<div class="white_card_header">
<div class="entrepreneur-index">

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'pager' => [
            'class' => 'yii\bootstrap4\LinkPager',
        ],
        
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            [
                'label' => 'Name',
                'attribute' => 'fullname',
                'value' => function($model){
                    return $model->user->fullname;
                }
            ],
            [
                'label' => 'NRIC',
                'attribute' => 'nric',
                'value' => function($model){
                    return $model->user->nric;
                }
            ],
            [
                'label' => 'Phone',
                'attribute' => 'phone',
 
            ],
            [
             'label' => 'Date Register',
             'value' => function($model){
                if($model->user){
                    return date('d M Y', $model->user->created_at);
                }
             }
            ],



            ['class' => 'yii\grid\ActionColumn',
                'template' => '{view} {edit} {delete}',
                //'visible' => false,
                'buttons'=>[
                'view'=>function ($url, $model) {
                    return Html::a('<span class="fa fa-search"></span> View',['view', 'id' => $model->id],['class'=>'btn btn-primary btn-sm']);
                },
                'edit'=>function ($url, $model) {
                    return Html::a('<span class="fa fa-pencil"></span> Edit',['view-edit', 'id' => $model->id],['class'=>'btn btn-info btn-sm']);
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
