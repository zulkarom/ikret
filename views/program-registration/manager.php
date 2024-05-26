<?php
use yii\helpers\Html;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var app\models\ProgramRegistrationSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */
$program = $role->program;
$this->title = 'Registration ('.$program->program_abbr.')';
$this->params['breadcrumbs'][] = $this->title;
?>
  <div class="pagetitle">
<h1><?=$this->title?></h1></div>

    </div><!-- End Page Title -->

    <section class="section dashboard">

    <div class="card">
            <div class="card-body pt-4">
            <div class="table-responsive">

    <?php
    
    $colums[] = ['class' => 'yii\grid\SerialColumn'];

    /* $colums[] = [
        'label' =>'Date Time',
        'attribute' => 'dateSearch',
        'value' => function($model){
            return $model->submitted_at;
        }
    ]; */
    

    if($program->id == 1){
        $colums[] = [
            'label' =>'Name',
            'attribute' => 'fullnameSearch',
            'value' => function($model){
                return $model->user->fullname;
            }
        ];

        $colums[] = [
            'label' =>'Jury',
            'attribute' => 'programx_id',
            'value' => function($model){
                return '';
            }
        ];
    }

    $colums[] = ['class' => 'yii\grid\ActionColumn',
    'contentOptions' => ['style' => 'width: 13%'],
'template' => '{view}',
//'visible' => false,
'buttons'=>[
    'view'=>function ($url, $model) {
        return Html::a('<span class="bi bi-eye"></span> View',['manager-view', 'id' => $model->id],['class'=>'btn btn-primary btn-sm']);
    },
],

];
    
    echo GridView::widget([
        'dataProvider' => $dataProvider,
        //'filterModel' => $searchModel,
        'columns' => $colums,
    ]); ?>

</div>
            </div>
        </div>



    </section>
