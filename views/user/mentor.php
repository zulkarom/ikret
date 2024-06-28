<?php

use app\models\JuryAssign;
use app\models\User;
use kartik\select2\Select2;
use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Modal;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use yii\web\JsExpression;

/** @var yii\web\View $this */
/** @var app\models\ProgramRegistrationSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'List of Mentors';
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
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            [
                'attribute' => 'fullname',
            ],
            [
                'attribute' => 'email',
                'label' => 'Email /Phone',
                'format' => 'html',
                'value' => function($model){
                    return $model->email . ' ' . $model->phone;
                }
            ],
            [
                'attribute' => 'kira',
                'label' => 'No. Assign',
                'format' => 'html',
                 'value' => function($model){

                    return '<span class="badge bg-primary">' . $model->kira . '</span>';
                } 
            ],
            ['class' => 'yii\grid\ActionColumn',
            //'contentOptions' => ['style' => 'width: 13%'],
            'template' => '{view}',
            //'visible' => false,
            'buttons'=>[
                'view'=>function ($url, $model) {
                    return Html::a('Cert.',['/program-registration/mentor-mentees', 'u' => $model->id],['class'=>'btn btn-primary btn-sm']);
                },
            ],
        
        ],
  
        ],
    ]); ?>

</div>
            </div>
        </div>



    </section>
