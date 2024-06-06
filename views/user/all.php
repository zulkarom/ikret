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
<h1>All Users</h1></div>

    </div><!-- End Page Title -->

    <section class="section dashboard">

    <div class="card">
            <div class="card-body pt-4">
            <div class="table-responsive">

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'pager' => [
            'class' => 'yii\bootstrap5\LinkPager',
        ],
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
                    return $model->email . '<br />' . $model->phone;
                }
            ],
            [
                'attribute' => 'is_internal',
                'format' => 'html',
                'filter' => Html::activeDropDownList($searchModel, 'is_internal', $searchModel->listCategory(),['class'=> 'form-control','prompt' => 'Pilih Category']),
                'value' => function($model){
                    $str = $model->categoryText. '<br />';
                    if($model->is_internal == 1){
                        $str .= $model->matric;
                    }else if($model->is_internal == 2){
                        $str .= $model->institution;
                    }
                    return $str;
                }
            ],
            ['class' => 'yii\grid\ActionColumn',
                'contentOptions' => ['style' => 'width: 13%'],
            'template' => '{view}',
            //'visible' => false,
            'buttons'=>[
                'view'=>function ($url, $model) {
                    return Html::a('<span class="bi bi-pencil-square
                    "></span> Update',['update', 'id' => $model->id],['class'=>'btn btn-primary btn-sm']);
                },
            ],
        
        ],
  
        ],
    ]); ?>

</div>
            </div>
        </div>



    </section>
