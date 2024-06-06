<?php

use app\models\Program;
use app\models\ProgramRegistration;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;

/** @var yii\web\View $this */
/** @var app\models\ProgramRegistrationSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$sub_str = $programSub? ' / (' . $programSub->sub_abbr . ')' : '';
$this->title = $program->program_abbr . $sub_str;
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="pagetitle">
<h1><?=$this->title?></h1>

<h1>Result by Assignment</h1>
</div>




    </div><!-- End Page Title -->

    <section class="section dashboard">

    <div class="card">
            <div class="card-body pt-4">
            <div class="table-responsive">

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        //'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            [
                'label' =>'Participant',
                'value' => function($model){
                   return $model->registration->participantText;
                }
            ],
            [
                'label' =>'Jury',
                'value' => function($model){
                   return $model->user->fullname;
                }
            ],
            [
                'label' =>'Score',
                'value' => function($model){
                    if($model->rubricAnswer){
                        return $model->rubricAnswer->scoreValue;
                    }
                   
                }
            ],
            [
                'label' =>'Status',
                'format' => 'html',
                'value' => function($model){
                   return $model->statusLabel;
                }
            ],
          
            ['class' => 'yii\grid\ActionColumn',
            'template' => '{view}',
            //'visible' => false,
            'buttons'=>[
                'view'=>function ($url, $model) {
                    return Html::a('<span class="bi bi-eye"></span> View',['view-result', 'id' => $model->id],['class'=>'btn btn-primary btn-sm']);
                },
            ],
        
        ],
  
        ],
    ]); ?>

</div>
            </div>
        </div>



    </section>
