<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\KursusPesertaSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Kursus Name: '.$anjur->kursus->kursus_name;
$this->params['breadcrumbs'][] = ['label' => 'Kursus Anjur', 'url' => ['/kursus-anjur/index']];
$this->params['breadcrumbs'][] = 'Kursus Peserta';

?>
<h4>Kursus Siri: <?=$anjur->kursus_siri?></h4>
<div class="box">
<div class="box-header"></div>
<div class="box-body">
<div class="kursus-peserta-index">

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        // 'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            [
                'label' => 'Participant Name',
                'value' => function($model){
                    return $model->user->fullname;
                    
                }
            ],
            [
             'format' => 'html',
             'label' => 'Status',
             'value' => function($model){
                if($model->status == 10){
                    return '<span class="label label-info">'.$model->statusText.'</span>';
                }else if($model->status == 20){
                    return '<span class="label label-primary">'.$model->statusText.'</span>';
                }else if($model->status == 30){
                    return '<span class="label label-success">'.$model->statusText.'</span>';
                }else{
                    return '<span class="label label-danger">'.$model->statusText.'</span>';
                }
             }
            ],

            ['class' => 'yii\grid\ActionColumn',
                'contentOptions' => ['style' => 'width: 18%'],
                'template' => '{view}',
                //'visible' => false,
                'buttons'=>[
                'view'=>function ($url, $model) {
                    if($model->user_type == 1){
                        return Html::a('<span class="fa fa-eye"></span> View',['/kadet/view', 'id' => $model->user->kadet->id],['class'=>'btn btn-primary btn-sm']);
                    }else if($model->user_type == 2){
                        return Html::a('<span class="fa fa-eye"></span> View',['/anakGayong/view', 'id' => $model->user->anakGayong->id],['class'=>'btn btn-primary btn-sm']);
                    }else if($model->user_type == 3){
                        return Html::a('<span class="fa fa-eye"></span> View',['/fasi/view', 'id' => $model->user->fasi->id],['class'=>'btn btn-primary btn-sm']);
                    }else if($model->user_type == 4){
                        return Html::a('<span class="fa fa-eye"></span> View',['/jurulatih/view', 'id' => $model->user_id],['class'=>'btn btn-primary btn-sm']);
                    }
                }
                ],    
            ],
        ],
    ]); ?>
</div>
