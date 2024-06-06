<?php

use kartik\export\ExportMenu;
use yii\bootstrap5\ActiveForm;
use yii\helpers\Html;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var app\models\ProgramRegistrationSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */
$program = $role->program;
$sub_text = $programSub ? ' / ' .$programSub->sub_abbr:'';
$this->title = 'Registration ('.$program->program_abbr. $sub_text . ')';
$this->params['breadcrumbs'][] = $this->title;
?>
  <div class="pagetitle">
<h1><?=$this->title?></h1>

</div>

    </div><!-- End Page Title -->

    <section class="section dashboard">
        <div class="form-group">
        <?=Html::button('<i class="bi bi-download"></i> Excel Analysis', ['id' => 'dwl-exl','class' => 'btn btn-success'])?>
    </div> 

    <?php

    $exportColumns[] = ['class' => 'yii\grid\SerialColumn'];
    $exportColumns[] = 'participantText';
    $exportColumns[] = [
        'label' =>'Completed Juries',
        'value' => function($model){
            $juries = $model->juriesCompleted;
                    $html = '';
                    if($juries){
                        $x=1;
                        foreach($juries as $jury){
                            $br = $x==1 ? '' : "\n";
                            $html .= $br . $jury->user->fullname . " (".$jury->score.")";
                            $x++;
                        }
                    }
                    return $html;
        }
    ];
    $exportColumns[] = [
        'label' =>'Average Score',
        'value' => function($model){
            return $model->score;
        }
    ];
    $exportColumns[] = [
        'label' =>'Award',
        'value' => function($model){
            return $model->awardText();
        }
    ];
    $exportColumns[] = [
        'label' =>'Achievement',
        'format' => 'html',
        'value' => function($model){
            $html = '';
            if($model->achievements){
                foreach($model->achievements as $a){
                    $html .= $a->achieve->name . '<br />';
                }
            }
            return $html;
        }
    ];
    //dapatkan category rubric
    if($selectedRubric){
        if($selectedRubric->categoriesNotRecommend){
            foreach($selectedRubric->categoriesNotRecommend as $cat){
                $exportColumns[] = [
                    'label' =>$cat->category_name,
                    'format' => 'html',
                    'value' => function($model) use($cat){
                        $items = $cat->items;
                        $arrayColum = [];
                        if($items){
                            foreach($items as $item){
                                $arrayColum[] = $item->colum_ans;
                            }
                        }
                        if($model->juriesCompleted){
                            $juri_count = 0;
                            $avg_cat_item = 0;
                            foreach($model->juriesCompleted as $j){
                                if($j->rubricAnswer){
                                    $ans = $j->rubricAnswer;
                                    //dapatkan answer colum2 tertentu
                                    //total markah soalan2 / total soalan
                                    $count_q = 0;
                                    $sum_q = 0;
                                    if($arrayColum){
                                        foreach($arrayColum as $q){
                                            $sum_q += $ans[$q];
                                            $count_q++;
                                        }
                                    }
                                    $avg_q = $count_q > 0 ? $sum_q / $count_q : 0;
                                    $avg_cat_item += $avg_q;
                                    $juri_count++;
                                }
                            }
                            $avg_cat = $juri_count > 0 ? $avg_cat_item / $juri_count : 0;
                            return number_format($avg_cat,2);
                        }
                        return 0;
                    }
                ];
            }
        }

        if($selectedRubric->categoriesRecommend){
            foreach($selectedRubric->categoriesRecommend as $cat){
                if($cat->itemsYesno){
                    foreach($cat->itemsYesno as $item){
                        //get total
                        $colum = $item->colum_ans;
                        $exportColumns[] = [
                            'label' =>$item->item_short,
                            'format' => 'html',
                            'value' => function($model) use($colum){
                                $total = 0;
                                if($model->juriesCompleted){
                                    foreach($model->juriesCompleted as $j){
                                        if($j->rubricAnswer){
                                            $answer = $j->rubricAnswer;
                                            if($answer[$colum] == 1){
                                                $total++;
                                            }
                                        }
                                    }
                                }
                                return $total;
                            }
                        ];
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
    'filename' => 'I-CREATE_ANALYSIS_' . date('Y-m-d'),
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

    <div class="card"  id="con-filter-form">
    <div class="card-header">Filter Form</div>
    <div class="card-body pt-4">
    <?= $this->render('_search_analysis', [
        'model' => $searchModel,
        'rubrics' => $rubrics,
        'stages' =>$stages,
        'programSub' => $programSub,
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
            'format' => 'html',
            'value' => function($model){
                $html = '';
                if($model->achievements){
                    foreach($model->achievements as $a){
                        $html .= $a->achieve->name . '<br />';
                    }
                }
                return $html;
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
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            [
                'label' =>'Participant',
                'value' => function($model){
                    return $model->participantText;
                }
            ],
            [
                'label' =>'Completed Juries',
                'format' => 'html',
                'value' => function($model){
                    $juries = $model->juriesCompleted;
                    $html = '';
                    if($juries){
                        foreach($juries as $jury){
                            $html .= $jury->user->fullname . " (".$jury->score.")<br />";
                        }
                    }
                    return $html;
                }
            ],
            [
                'label' =>'Average Score',
                'value' => function($model){
                    return number_format($model->purata,2).'%';
                }
            ],
            [
                'label' =>'Award',
                'value' => function($model){
                    return $model->awardText();
                }
            ],
            [
                'label' =>'Achievement',
                'format' => 'html',
                'value' => function($model){
                    $html = '';
                    if($model->achievements){
                        foreach($model->achievements as $a){
                            $html .= $a->achieve->name . '<br />';
                        }
                    }
                    return $html;
                }
            ],
            ['class' => 'yii\grid\ActionColumn',
'template' => '{view}',
//'visible' => false,
'buttons'=>[
    'view'=>function ($url, $model) {
        $url = ['manager-award', 'id' => $model->id];
        if($model->programSub){
            $url = ['manager-award', 'id' => $model->id, 'sub' => $model->programSub->id];
        }
        return Html::a('Update', $url,['class'=>'btn btn-primary btn-sm']);
    }
],

]
                    
            ],
    ]); ?>

</div>
            </div>
        </div>


    </section>
