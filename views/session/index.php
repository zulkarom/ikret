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

    <p>
        <?= Html::a('Create Session', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

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
                            'template' => '{view} {update}',
                            
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
