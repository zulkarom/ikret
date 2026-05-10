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
$this->title = 'Analysis & Achievement ('.$program->program_abbr. $sub_text . ')';
$this->params['breadcrumbs'][] = [
    'label' => $program->program_abbr . $sub_text,
    'url' => ['/program-registration/manager-dashboard', 'id' => $program->id, 'sub' => $programSub ? $programSub->id : null]
];
$this->params['breadcrumbs'][] = $this->title;

$recommendationValue = function($model, $asHtml = false){
    $items = [];
    if($model->juriesCompleted){
        foreach($model->juriesCompleted as $jury){
            $answer = $jury->rubricAnswer;
            $rubric = $answer && $answer->rubric ? $answer->rubric : $jury->rubric;
            if(!$answer || !$rubric || !$rubric->categoriesRecommend){
                continue;
            }

            foreach($rubric->categoriesRecommend as $cat){
                if(!$cat->itemsRecommend){
                    continue;
                }

                foreach($cat->itemsRecommend as $item){
                    $column = $item->colum_ans;
                    if(!$column){
                        continue;
                    }

                    $value = $answer->$column;
                    $hasRecommendation = false;
                    if((int)$item->item_type === 2){
                        $hasRecommendation = (int)$value === 1;
                    }else if((int)$item->item_type === 1){
                        $hasRecommendation = (int)$value > 0;
                    }else{
                        $hasRecommendation = trim((string)$value) !== '';
                    }

                    if($hasRecommendation){
                        $label = trim((string)($item->item_short ?: $item->item_text));
                        if($label !== ''){
                            $items[$label] = $label;
                        }
                    }
                }
            }
        }
    }

    if(!$items){
        return '';
    }

    if($asHtml){
        $html = '<ul class="mb-0 ps-3">';
        foreach($items as $item){
            $html .= '<li>' . Html::encode($item) . '</li>';
        }
        return $html . '</ul>';
    }

    return implode("\n", array_values($items));
};

$achievementValue = function($model, $asHtml = false){
    $items = [];
    if($model->achievements){
        foreach($model->achievements as $a){
            $text = $a->achieve->name;
            if($a->winnerTitle){
                $titleText = trim((string)$a->winnerTitle->title_name);
                if($titleText === ''){
                    $titleText = 'Winner ' . $a->winnerTitle->winner_order . ' (no title)';
                }
                $text .= ' - ' . $titleText;
            }
            $items[] = $text;
        }
    }

    if(!$items){
        return '';
    }

    if($asHtml){
        $html = '';
        foreach($items as $item){
            $html .= Html::encode($item) . '<br />';
        }
        return $html;
    }

    return implode("\n", $items);
};
?>
  <div class="pagetitle">
<h1><?=$this->title?></h1>
</div>

<section class="section dashboard">
    <div class="form-group">
    <?=Html::button('<i class="bi bi-download"></i> Excel Analysis', ['id' => 'dwl-exl','class' => 'btn btn-success'])?>
</div> 

    <?php
    $exportColumns[] = ['class' => 'yii\grid\SerialColumn'];
    $exportColumns[] = 'participantText';
    $exportColumns[] =[
        'label' =>'Recommendation',
        'value' => function($model) use($recommendationValue){
            return $recommendationValue($model);
        }
    ];
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
        'value' => function($model) use($achievementValue){
            return $achievementValue($model, true);
        }
    ];
    //dapatkan category rubric
    if($selectedRubric){
        if($selectedRubric->categoriesScore){
            foreach($selectedRubric->categoriesScore as $cat){
                $exportColumns[] = [
                    'label' =>$cat->category_name,
                    'format' => 'html',
                    'value' => function($model) use($cat){
                        $items = $cat->itemsScore;
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
                if($cat->itemsRecommendYesno){
                    foreach($cat->itemsRecommendYesno as $item){
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
        'action' => 'manager-analysis'
    ]) ?>
</div></div>


    <div class="card">
            <div class="card-body pt-4">
            <div class="table-responsive">

   <?=GridView::widget([
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
                    return $model->participantText;
                }
            ],
            [
                'label' =>'Recommendation',
                'format' => 'raw',
                'value' => function($model) use($recommendationValue){
                    $html = $recommendationValue($model, true);
                    return $html ?: '<span class="text-muted">-</span>';
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
                'value' => function($model) use($achievementValue){
                    return $achievementValue($model, true);
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
