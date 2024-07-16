<?php

use kartik\export\ExportMenu;
use kartik\form\ActiveForm;
//use yii\bootstrap5\ActiveForm;
use yii\helpers\Html;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var app\models\ProgramRegistrationSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */
$program = $role->program;
$sub_str = $programSub? ' / ' . $programSub->sub_abbr  : '';
$this->title = 'Certificates ('.$program->program_abbr . $sub_str.')';
$this->params['breadcrumbs'][] = $this->title;
?>
  <div class="pagetitle">
<h1><?=$this->title?></h1>
</div>
    </div><!-- End Page Title -->
    <section class="section dashboard">
         
    
    <br />


    <div class="card">
            <div class="card-body pt-4">
            <div class="table-responsive">

    <?php

    $colums[] = ['class' => 'yii\grid\SerialColumn'];



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
    'label' =>'Members',
    'format' => 'raw',
    'value' => function($model){
        return $model->memberStr;
    }
];

$colums[] = [
    'label' =>'Certificates',
    'format' => 'raw',
    'value' => function($model){
        $reg = $model;
        $str = '<ul>';
        $str .= '<li>' . Html::a('Cert. of Participation',['/program/cert-participation', 'reg' => $reg->id],['target' => '_blank']) . '</li>';

        if($reg->award > 0 ){ //&& $reg->score > 0
            $str .= '<li>' . Html::a('Cert. of Achievement ('.$reg->awardTextColor().')',['/program/cert-achievement', 'reg' => $reg->id],['target' => '_blank']) . '</li>';
        }
        if($reg->achievements){
            foreach($reg->achievements as $v){
                //
                $str .= '<li>' . Html::a('Cert. of Excellence (<span style="color:#DA9100">'.$v->achieve->name.'</span>)',['/program/cert-excellence', 'reg' => $reg->id],['target' => '_blank']) . '</li>';
            }
            
        }


        return $str;
    }
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
