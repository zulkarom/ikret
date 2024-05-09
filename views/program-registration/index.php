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

$this->title = 'Program Registrations';
$this->params['breadcrumbs'][] = $this->title;
?>
  <div class="pagetitle">
<h1>List of Registration</h1></div>

    </div><!-- End Page Title -->

    <section class="section dashboard">

    <div class="card">
            <div class="card-body pt-4">
            <div class="table-responsive">

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            [
                'label' =>'Name',
                'attribute' => 'fullnameSearch',
                'value' => function($model){
                    return $model->user->fullname;
                }
            ],
            [
                'label' =>'Program',
                'attribute' => 'programx_id',
                'filter' => Html::activeDropDownList($searchModel, 'programx_id', ArrayHelper::map(Program::find()->all(),'id', 'program_abbr'),['class'=> 'form-control','prompt' => 'Pilih Program']),
                'value' => function($model){
                    return $model->program->program_name;
                }
            ],
            [
                'label' =>'Date Time',
                'attribute' => 'dateSearch',
                'value' => function($model){
                    return $model->submitted_at;
                }
            ],
            ['class' => 'yii\grid\ActionColumn',
                'contentOptions' => ['style' => 'width: 13%'],
            'template' => '{view}',
            //'visible' => false,
            'buttons'=>[
                'view'=>function ($url, $model) {
                    return Html::a('<span class="bi bi-eye"></span> View',['view', 'id' => $model->id],['class'=>'btn btn-primary btn-sm']);
                },
            ],
        
        ],
  
        ],
    ]); ?>

</div>
            </div>
        </div>



    </section>
