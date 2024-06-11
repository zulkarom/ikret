<?php

use yii\helpers\Html;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var app\models\ProgramRegistrationSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Jury Assignments';
$this->params['breadcrumbs'][] = $this->title;
?>
  <div class="pagetitle">
<h1><?=$this->title?></h1></div>

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
       // 'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            [
                'label' =>'Program',
                'value' => function($model){
                    return $model->registration->program->program_abbr;
                }
            ],
            [
                'label' =>'Participants',
                'format' => 'raw',
                'value' => function($model){
                    return $model->registration->shortFieldsHtml;
                }
            ],
            [
                'label' =>'Jury Info',
                'format' => 'raw',
                'value' => function($model){
                    $jury = $model;
                    $html = '';
                        $html .= '<ul>';
                            $html .= $jury->infoHtml();
                        $html .= '<ul>';
         
                    return $html;
                }
            ],
            'statusLabel:html',
            [
                'label' =>'Result',
                'value' => function($model){
                    if($model->score){
                        return $model->score;
                    }else{
                        return 0;
                    }
                }
            ],

            ['class' => 'yii\grid\ActionColumn',
                'contentOptions' => ['style' => 'width: 13%'],
            'template' => '{view}',
            //'visible' => false,
            'buttons'=>[
                'view'=>function ($url, $model) {
                    return Html::a('Judge',['jury-judge', 'id' => $model->id],['class'=>'btn btn-primary btn-sm']);
                },
            ],
        
        ],
  
        ],
    ]); ?>

</div>
            </div>
        </div>



    </section>
