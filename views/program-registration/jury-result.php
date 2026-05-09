<?php

use app\models\Mentor;
use app\models\Program;
use app\models\ProgramRegistration;
use app\models\JuryAssign;
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
$this->params['breadcrumbs'][] = [
    'label' => $program->program_abbr . ($programSub ? ' / ' . $programSub->sub_abbr : ''),
    'url' => ['/program-registration/manager-dashboard', 'id' => $program->id, 'sub' => $programSub ? $programSub->id : null]
];
$this->params['breadcrumbs'][] = $this->title;

$recommendationValue = function($model, $asHtml = false){
    $answer = $model->rubricAnswer;
    $rubric = $answer && $answer->rubric ? $answer->rubric : $model->rubric;
    if(!$answer || !$rubric || !$rubric->categoriesRecommend){
        return '';
    }

    $items = [];
    foreach($rubric->categoriesRecommend as $cat){
        if(!$cat->items){
            continue;
        }

        foreach($cat->items as $item){
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
                $items[] = trim((string)($item->item_short ?: $item->item_text));
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

    return implode("\n", $items);
};

$statusSummaryQuery = JuryAssign::find()->alias('a')
    ->select(['a.status', 'total' => 'COUNT(*)'])
    ->joinWith(['registration r'])
    ->leftJoin('user u','u.id = r.user_id')
    ->where(['r.program_id' => $program->id, 'a.rubric_id' => $searchModel->rubric]);
if($programSub){
    $statusSummaryQuery->andWhere(['r.program_sub' => $programSub->id]);
}
$statusSummaryQuery->andFilterWhere(['like', 'u.fullname', $searchModel->fullnameSearch]);
$statusSummaryRows = $statusSummaryQuery->groupBy('a.status')->asArray()->all();
$statusSummary = [0 => 0, 10 => 0, 20 => 0];
foreach($statusSummaryRows as $row){
    $statusSummary[(int)$row['status']] = (int)$row['total'];
}
$totalAssignments = array_sum($statusSummary);
$completionPercent = $totalAssignments > 0 ? round(($statusSummary[20] / $totalAssignments) * 100) : 0;
$selectedStatus = $searchModel->jury_status;
$statusFilterUrl = function($status = null)use($searchModel, $program, $programSub){
    $params = Yii::$app->request->queryParams;
    unset($params['page'], $params['per-page']);
    $params[0] = 'program-registration/jury-result';
    $params['id'] = $program->id;
    if($programSub){
        $params['sub'] = $programSub->id;
    }

    if($status === null){
        unset($params[$searchModel->formName()]['jury_status']);
    }else{
        $params[$searchModel->formName()]['jury_status'] = $status;
    }

    return Url::to($params);
};

$this->registerCss(<<<CSS
.jury-result-stats {
    display: grid;
    grid-template-columns: repeat(5, minmax(0, 1fr));
    gap: .75rem;
    margin: 0 0 1rem;
}
.jury-result-stat {
    display: block;
    padding: .9rem 1rem;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    background: #fff;
    color: inherit;
    text-decoration: none;
}
.jury-result-stat:hover,
.jury-result-stat.active {
    border-color: #0d6efd;
    box-shadow: 0 .5rem 1.25rem rgba(13, 110, 253, .12);
}
.jury-result-stat-label {
    color: #6c757d;
    font-size: .78rem;
    font-weight: 700;
    text-transform: uppercase;
}
.jury-result-stat-count {
    margin-top: .25rem;
    font-size: 1.6rem;
    font-weight: 800;
    line-height: 1;
}
@media (max-width: 767.98px) {
    .jury-result-stats {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }
}
CSS);
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
        'label' =>'Group Members',
        'value' => function($model){
            $members = $model->registration->members;
            $html = '';
            if($members){
                $i=1;
                foreach($members as $m){
                        $br = $i == 1 ? '' : "\n";
                        $matric = '';
                        if($m->member_matric){
                            $matric = ' ('. $m->member_matric .')';
                        }
                        $html .= $br . $m->member_name . $matric;
                        $i++;
                    
                }
            }
            return $html;
        }
    ];
    
    $exportColumns[] = [
        'label' =>'Recommendation',
        'value' => function($model)use($recommendationValue){
            return $recommendationValue($model);
        }
    ];

    $exportColumns[] = [
        'label' =>'Group Name',
        'value' => function($model){
            $reg = $model->registration;
            return $reg->group_name;
        }
    ];

    $exportColumns[] = [
        'label' =>'Project Title',
        'value' => function($model){
            $reg = $model->registration;
            return $reg->project_name;
        }
    ];

    $exportColumns[] = [
        'label' =>'Mentor',
        'value' => function($model){
            $reg = $model->registration;
            $main = $reg->mentorMain;
            $co = $reg->mentorCo;
            $html = '';
            if($main){
                if($main->user){
                    $html .= $main->user->fullname;
                }
            }
            if($co){
                if($co->user){
                    $html .= "\n" . $co->user->fullname;
                } 
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

    <div class="jury-result-stats">
        <?= Html::a(
            '<div class="jury-result-stat-label">All Assignments</div><div class="jury-result-stat-count text-primary">' . Html::encode($totalAssignments) . '</div>',
            $statusFilterUrl(null),
            ['class' => 'jury-result-stat' . ($selectedStatus === null || $selectedStatus === '' ? ' active' : '')]
        ) ?>
        <?= Html::a(
            '<div class="jury-result-stat-label">Assigned</div><div class="jury-result-stat-count text-warning">' . Html::encode($statusSummary[0]) . '</div>',
            $statusFilterUrl(0),
            ['class' => 'jury-result-stat' . ((string)$selectedStatus === '0' ? ' active' : '')]
        ) ?>
        <?= Html::a(
            '<div class="jury-result-stat-label">Judging</div><div class="jury-result-stat-count text-primary">' . Html::encode($statusSummary[10]) . '</div>',
            $statusFilterUrl(10),
            ['class' => 'jury-result-stat' . ((string)$selectedStatus === '10' ? ' active' : '')]
        ) ?>
        <?= Html::a(
            '<div class="jury-result-stat-label">Complete</div><div class="jury-result-stat-count text-success">' . Html::encode($statusSummary[20]) . '</div>',
            $statusFilterUrl(20),
            ['class' => 'jury-result-stat' . ((string)$selectedStatus === '20' ? ' active' : '')]
        ) ?>
        <div class="jury-result-stat">
            <div class="jury-result-stat-label">% Completion</div>
            <div class="jury-result-stat-count text-success"><?= Html::encode($completionPercent) ?>%</div>
        </div>
    </div>

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
                'label' =>'Recommendation',
                'format' => 'raw',
                'value' => function($model)use($recommendationValue){
                    $html = $recommendationValue($model, true);
                    return $html ?: '<span class="text-muted">-</span>';
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
