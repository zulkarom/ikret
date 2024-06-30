<?php

use app\models\Session;
use kartik\export\ExportMenu;
use kartik\form\ActiveForm;
//use yii\bootstrap5\ActiveForm;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;

/** @var yii\web\View $this */
/** @var app\models\ProgramRegistrationSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */
$program = $role->program;
$sub_str = $programSub? ' / ' . $programSub->sub_abbr  : '';
$this->title = 'Registration ('.$program->program_abbr . $sub_str.')';
$this->params['breadcrumbs'][] = $this->title;
?>
  <div class="pagetitle">
<h1><?=$this->title?></h1>
</div>

    </div><!-- End Page Title -->

    <section class="section dashboard">
        <div class="form-group"><?=Html::button('Filter Form',['id' => 'btn-filter-form','class' => 'btn btn-info'])?> 

    </div> 



        <?php
        $this->registerJs('
            $("#btn-jury-form").click(function(){
                $("#con-jury-form").slideDown();
                $("#con-filter-form").slideUp();
            });
            $("#hide-jury-form").click(function(){
                $("#con-jury-form").slideUp();
              
            });

            $("#btn-filter-form").click(function(){
                $("#con-filter-form").slideDown();
                $("#con-jury-form").slideUp();
            });
            $("#hide-filter-form").click(function(){
                $("#con-filter-form").slideUp();
            });
        ');
        
        ?>
    
    <br />

    <div class="card" style="display:none" id="con-filter-form">
    <div class="card-header">Filter Form</div>
    <div class="card-body pt-4">
    <?= $this->render('_search', [
        'model' => $searchModel,
        'programSub' => $programSub,
        'action' => 'manager-session'
    ]) ?>
</div></div>


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
    

    if(true){ //$program->id == 1
        $colums[] = [
            'label' =>'Participants',
            'attribute' => 'fullnameSearch',
            'format' => 'html',
            'value' => function($model){
                return $model->user->fullname;
            }
        ];

        $colums[] = [
            'label' =>'Certificates',
            'format' => 'raw',
            'value' => function($model){
                $html = '';
            $certs = Session::find()->alias('a')
        ->select('a.*, r.id as reg_id')
        ->joinWith(['sessionAttendances t'])
        ->innerJoin('program_reg r', 'r.program_id = a.program_id')
        ->where(['r.user_id' => $model->user_id, 'r.status' => 10, 'r.program_id' => $model->program_id, 't.user_id' => $model->user_id])
        ->all();
                if($certs){
                    $html .='<ul>';
                    foreach ($certs as $item) {
                        $html .= '<li><a href="'.Url::to(['/program/cert-participation-session', 'reg' => $item->reg_id, 's' => $item->id, 'u' => $model->user_id]).'" target="_blank">Certificate of Participation</a> <br /><span style="font-size:12px">('.$item->session_name.')</span></li>';
                        
                    }
                    $html .='</ul>';
                }

                return $html;
            }
        ];
    }

    $colums[] = ['class' => 'yii\grid\ActionColumn',
'template' => '{view}',
//'visible' => false,
'buttons'=>[
    'view'=>function ($url, $model) {
        $url = ['manager-view', 'id' => $model->id];
        if($model->programSub){
            $url = ['manager-view', 'id' => $model->id, 'sub' => $model->programSub->id];
        }
        return Html::a('View',$url,['class'=>'btn btn-primary btn-sm']);
    }
],

];
    
    echo GridView::widget([
        'dataProvider' => $dataProvider,
            'pager' => [
            'class' => 'yii\bootstrap5\LinkPager',
        ],
        //'filterModel' => $searchModel,
        'columns' => $colums,
    ]); ?>

</div>
            </div>
        </div>


    </section>
