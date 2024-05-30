<?php

use yii\bootstrap5\ActiveForm;
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
        <div class="form-group"><?=Html::button('Filter Form',['id' => 'btn-filter-form','class' => 'btn btn-primary'])?> <?=Html::button('Jury Assignment Form',['id' => 'btn-jury-form', 'class' => 'btn btn-success'])?></div>
        <?php
        $this->registerJs('
            $("#btn-jury-form").click(function(){
                $("#con-jury-form").slideDown();
                $("#con-filter-form").slideUp();
            });
            $("#hide-jury-form").click(function(){
                $("#con-jury-form").slideUp();
                $("#con-filter-form").slideDown();
            });

            $("#btn-filter-form").click(function(){
                $("#con-filter-form").slideDown();
                $("#con-jury-form").slideUp();
            });
            $("#hide-filter-form").click(function(){
                $("#con-filter-form").slideUp();
                $("#con-jury-form").slideDown();
            });
        ');
        
        ?>
    
    <br />

    <div class="card" style="display:none" id="con-filter-form">
    <div class="card-header">Filter Form</div>
    <div class="card-body pt-4">
    <?= $this->render('_search', [
        'model' => $searchModel
    ]) ?>
</div></div>

    <?php $form = ActiveForm::begin(); ?>
    <div class="card" style="display:none" id="con-jury-form">
    <div class="card-header">Jury Assignment Form</div>
    <div class="card-body pt-4">
    <?= $this->render('_form_jury', [
        'model' => $model,
        'form' => $form
    ]) ?>
</div></div>




    <div class="card">
            <div class="card-body pt-4">
            <div class="table-responsive">

    <?php
    $colums[] = ['class' => 'yii\grid\CheckboxColumn'];
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
                $html = '';
                if($model->flag == 1){
                    $html .= '<i class="bi bi-flag-fill" style="color:blue"></i> ';
                }
                $html .= $model->shortFieldsHtml;
                return $html;
            }
        ];

        $colums[] = [
            'label' =>'Assigned Juries',
            'attribute' => 'programx_id',
            'format' => 'raw',
            'value' => function($model){
                $juries = $model->juries;
                $html = '';
                if($juries){
                    $html .= '<ul>';
                    foreach($juries as $jury){
                        $html .= $jury->infoHtml(true);
                    }
                    $html .= '<ul>';
                }
                return $html;
            }
        ];

        $colums[] = [
            'label' =>'Average Score',
            'value' => function($model){
                return $model->score;
            }
        ];

        $colums[] = [
            'label' =>'Award',
            'value' => function($model){
                return $model->awardText();
            }
        ];
        $colums[] = [
            'label' =>'Achievement',
            'value' => function($model){
                return '';
            }
        ];
    }

    $colums[] = ['class' => 'yii\grid\ActionColumn',
'template' => '{view} {flag}',
//'visible' => false,
'buttons'=>[
    'view'=>function ($url, $model) {
        return Html::a('<span class="bi bi-eye"></span> View',['manager-view', 'id' => $model->id],['class'=>'btn btn-primary btn-sm']);
    },
    'flag'=>function ($url, $model) {
        if($model->flag == 0){
            return Html::a('<span class="bi bi-flag"></span> Flag',['manager-flag', 'id' => $model->id, 'flag' => 1],['class'=>'btn btn-warning btn-sm']);
        }else if($model->flag == 1){
            return Html::a('<span class="bi bi-flag"></span> Unflag',['manager-flag', 'id' => $model->id, 'flag' => 0],['class'=>'btn btn-outline-warning btn-sm']);
        }
        
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


        <?php ActiveForm::end(); ?>
    </section>
