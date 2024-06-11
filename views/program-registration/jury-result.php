<?php

use app\models\Program;
use app\models\ProgramRegistration;
use app\widgets\Breadcrumbs;
use kartik\export\ExportMenu;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;

/** @var yii\web\View $this */
/** @var app\models\ProgramRegistrationSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$sub_str = $programSub? ' / (' . $programSub->sub_abbr . ')' : '';
$this->title = 'Result by Assignment - ' . $program->program_abbr . $sub_str;
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="pagetitle">
<h1><?=$this->title?></h1>
</div>

<div class="form-group">
    <?=Html::button('<i class="bi bi-download"></i> Download Jury Data', ['id' => 'dwl-exl','class' => 'btn btn-success'])?>
</div>

<?php

    $exportColumns[] = ['class' => 'yii\grid\SerialColumn'];
    //$exportColumns[] = 'participantText';
    $exportColumns[] = [
        'label' =>'Participant',
        'value' => function($model){
            $reg = $model->registration;
            return $reg->participantText;
        }
    ];
    
    $exportColumns[] = [
        'label' =>'Group/Booth ID',
        'value' => function($model){
            $html = '';
            $reg = $model->registration;
            if($reg->group_code){
                $html .= $reg->group_code. ' ';
            }
            if($reg->booth_number){
                $html .= $reg->booth_number;
            }
            return $html;
        }
    ];

    $exportColumns[] = [
        'label' =>'Jury',
        'value' => function($model){
            return $model->user->fullname;
        }
    ];

    $exportColumns[] = [
        'label' =>'Status',
        'format' => 'html',
        'value' => function($model){
           return $model->statusText;
        }
    ];

    $exportColumns[] = [
        'label' =>'Score',
        'value' => function($model){
            return $model->score;
        }
    ];
    //ok kita keluarkan senarai soalan dia
    //$rubric = 
    //dapatkan category rubric
    if($selectedRubric){
        if($selectedRubric->categories){
            $i=1;
            foreach($selectedRubric->categories as $cat){
                if($cat->items){
                    
                    foreach($cat->items as $item){
                        $label = $item->item_text;
                        if (strlen($label) > 50)
                        $label = substr($label, 0, 47) . '...';
                        
                        $exportColumns[] = [
                            'label' => $i.'. ' . $label,
                            'value' => function($model)use($item){
                                $result = '';
                                if($model->rubricAnswer){
                                    $ans = $model->rubricAnswer;
                                    if($item->item_type == 2){
                                        //yes no
                                        $r = $ans->{$item->colum_ans};
                                        if($r == 1){
                                            $result = 'Yes';
                                        }else if($r == 2){
                                            $result = 'No';
                                        }
                                    }else{
                                        $result = $ans->{$item->colum_ans};
                                    }
                                }
                                return $result;
                            }
                        ];
                        $i++;
                    }
                }

            }

        }
    }

?>

<div style="display: none;">
<?=ExportMenu::widget([
    'dataProvider' => $dataProvider,
    'columns' => $exportColumns,
    'filename' => 'I-CREATE_JURY_DATA_' . date('Y-m-d'),
    'onRenderSheet'=>function($sheet, $grid){
        $sheet->getStyle('A2:'.$sheet->getHighestColumn().$sheet->getHighestRow())
        ->getAlignment()->setWrapText(true);
    },
    'exportConfig' => [
        ExportMenu::FORMAT_PDF => false,
        ExportMenu::FORMAT_EXCEL_X => false,
    ],
]);?>
</div>

        <?php
        $this->registerJs('
        $("#dwl-exl").click(function(){
            $("#w0-xls")[0].click();
        });
        ');
        ?>
<br />
    </div><!-- End Page Title -->

    <section class="section dashboard">

   

    <div class="card"  id="con-filter-form">
    <div class="card-header">Filter Form</div>
    <div class="card-body pt-4">
        <?php 
        $stages = ['']; //sepatutnya xperlu stages, by rubric dh cukup
        ?>
    <?= $this->render('_search_analysis', [
        'model' => $searchModel,
        'rubrics' => $rubrics,
        'stages' =>$stages,
        'programSub' => $programSub,
        'action' => 'jury-result'
    ]) ?>
</div></div>

    <div class="card">
            <div class="card-body pt-4">
            <div class="table-responsive">

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
                'pager' => [
            'class' => 'yii\bootstrap5\LinkPager',
        ],
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
                'label' =>'Group/ Booth',
                'value' => function($model){
                    $html = '';
                    $reg = $model->registration;
                    if($reg->group_code){
                        $html .= $reg->group_code. ' ';
                    }
                    if($reg->booth_number){
                        $html .= $reg->booth_number;
                    }
                    return $html;
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
                    return Html::a('View',['view-result', 'id' => $model->id],['class'=>'btn btn-primary btn-sm']);
                },
            ],
        
        ],
  
        ],
    ]); ?>

</div>
            </div>
        </div>



    </section>
