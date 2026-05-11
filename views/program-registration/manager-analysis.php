<?php

use kartik\export\ExportMenu;
use app\models\ProgramRegistration;
use yii\bootstrap5\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;
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

$analysisModels = (clone $dataProvider->query)->all();
$analysisUrl = function($extra = []) use($program, $programSub){
    $params = Yii::$app->request->queryParams;
    unset($params['page'], $params['per-page']);
    $params[0] = '/program-registration/manager-analysis';
    $params['id'] = $program->id;
    if($programSub){
        $params['sub'] = $programSub->id;
    }else{
        unset($params['sub']);
    }
    if(!isset($params['ManagerAnalysisSearch']) || !is_array($params['ManagerAnalysisSearch'])){
        $params['ManagerAnalysisSearch'] = [];
    }
    unset($params['ManagerAnalysisSearch']['statFilter'], $params['ManagerAnalysisSearch']['awardFilter']);
    foreach($extra as $key => $value){
        if($value === null || $value === ''){
            unset($params['ManagerAnalysisSearch'][$key]);
        }else{
            $params['ManagerAnalysisSearch'][$key] = $value;
        }
    }

    return Url::to($params);
};
$isStatActive = function($statFilter = null, $awardFilter = null) use($searchModel){
    return (string)$searchModel->statFilter === (string)$statFilter
        && (string)$searchModel->awardFilter === (string)$awardFilter;
};
$analysisStats = [
    'participants' => count($analysisModels),
    'score_sum' => 0,
    'score_count' => 0,
    'awarded' => 0,
    'achievements' => 0,
    'awards' => [],
];
foreach(ProgramRegistration::listAward() as $awardValue){
    $analysisStats['awards'][$awardValue] = 0;
}
foreach($analysisModels as $analysisModel){
    $score = $analysisModel->purata;
    if($score !== null && $score !== '' && is_numeric($score)){
        $score = (float)$score;
        $analysisStats['score_sum'] += $score;
        $analysisStats['score_count']++;
        $award = ProgramRegistration::calcAward($score);
        $awardLabel = $award ? (ProgramRegistration::listAward()[$award] ?? '') : '';
        if($awardLabel !== ''){
            $analysisStats['awarded']++;
            $analysisStats['awards'][$awardLabel]++;
        }
    }
    $analysisStats['achievements'] += $analysisModel->achievements ? count($analysisModel->achievements) : 0;
}
$analysisAverageScore = $analysisStats['score_count'] > 0 ? $analysisStats['score_sum'] / $analysisStats['score_count'] : 0;

$this->registerCss(<<<CSS
.analysis-stats {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
    gap: .85rem;
    margin-bottom: 1rem;
}
.analysis-stat {
    display: block;
    background: #fff;
    border: 1px solid #e4ebf3;
    border-radius: 12px;
    padding: 1rem;
    box-shadow: 0 8px 20px rgba(20, 43, 69, .06);
    text-decoration: none;
    transition: border-color .15s ease, box-shadow .15s ease, transform .15s ease;
}
.analysis-stat:hover,
.analysis-stat:focus {
    border-color: #7aa7d8;
    box-shadow: 0 10px 24px rgba(20, 43, 69, .1);
    transform: translateY(-1px);
    text-decoration: none;
}
.analysis-stat.is-active {
    border-color: #0d6efd;
    box-shadow: 0 10px 26px rgba(13, 110, 253, .16);
}
.analysis-stat__value {
    display: block;
    color: #17324d;
    font-size: 1.45rem;
    font-weight: 700;
    line-height: 1.1;
}
.analysis-stat__label {
    display: block;
    color: #6f8499;
    font-size: .82rem;
    margin-top: .25rem;
}
CSS);

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

    <div class="analysis-stats mt-3">
        <a class="analysis-stat <?= $isStatActive(null, null) ? 'is-active' : '' ?>" href="<?= Html::encode($analysisUrl()) ?>">
            <span class="analysis-stat__value"><?= (int)$analysisStats['participants'] ?></span>
            <span class="analysis-stat__label">Analyzed Participants</span>
        </a>
        <a class="analysis-stat <?= $isStatActive('awarded', null) ? 'is-active' : '' ?>" href="<?= Html::encode($analysisUrl(['statFilter' => 'awarded'])) ?>">
            <span class="analysis-stat__value"><?= (int)$analysisStats['awarded'] ?></span>
            <span class="analysis-stat__label">Awarded Participants</span>
        </a>
        <a class="analysis-stat <?= $isStatActive('achievements', null) ? 'is-active' : '' ?>" href="<?= Html::encode($analysisUrl(['statFilter' => 'achievements'])) ?>">
            <span class="analysis-stat__value"><?= (int)$analysisStats['achievements'] ?></span>
            <span class="analysis-stat__label">Achievements Assigned</span>
        </a>
        <?php foreach($analysisStats['awards'] as $awardLabel => $awardCount): ?>
            <?php
            $awardValue = array_search($awardLabel, ProgramRegistration::listAward(), true);
            ?>
            <a class="analysis-stat <?= $isStatActive(null, $awardValue) ? 'is-active' : '' ?>" href="<?= Html::encode($analysisUrl(['awardFilter' => $awardValue])) ?>">
                <span class="analysis-stat__value"><?= (int)$awardCount ?></span>
                <span class="analysis-stat__label"><?= Html::encode(ucwords(strtolower($awardLabel))) ?> Awards</span>
            </a>
        <?php endforeach; ?>
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
                                            if(!$q){
                                                continue;
                                            }
                                            $value = $ans[$q];
                                            if($value === null || $value === '' || !is_numeric($value)){
                                                continue;
                                            }
                                            $sum_q += (float)$value;
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
