<?php

use kartik\export\ExportMenu;
use kartik\select2\Select2;
use kartik\select2\Select2Asset;
use app\models\ProgramRegistration;
use yii\bootstrap5\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var app\models\ProgramRegistrationSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */
/** @var bool $hasWinnerTitleSelection */
/** @var array $winnerTitlesByAchievement */
$program = $role->program;
$sub_text = $programSub ? ' / ' .$programSub->sub_abbr:'';
$this->title = 'Analysis & Achievement ('.$program->program_abbr. $sub_text . ')';
Select2Asset::register($this);
$this->params['breadcrumbs'][] = [
    'label' => $program->program_abbr . $sub_text,
    'url' => ['/program-registration/manager-dashboard', 'id' => $program->id, 'sub' => $programSub ? $programSub->id : null]
];
$this->params['breadcrumbs'][] = $this->title;
$openAnalysisCard = Yii::$app->session->remove('managerAnalysisOpenCard');
if(!in_array($openAnalysisCard, ['filter', 'achievement-form'], true)){
    $openAnalysisCard = '';
}

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
$achievementFilterUrl = function($achievementId)use($analysisUrl){
    return $analysisUrl(['achievementId' => (int)$achievementId]);
};
$achievementSummary = [];
$assignedWinners = [];
$assignedAchievementRows = [];
$participantOptions = ['' => 'Select Participant'];
if(isset($analysisModels) && $analysisModels){
    foreach($analysisModels as $analysisModel){
        $label = trim((string)($analysisModel->group_name ?? ''));
        if($label === ''){
            $label = (string)$analysisModel->participantText;
        }else{
            $label .= ' - ' . (string)$analysisModel->participantText;
        }
        $participantOptions[(int)$analysisModel->id] = $label;

        if(!$analysisModel->achievements){
            continue;
        }
        foreach($analysisModel->achievements as $pa){
            if(!$pa->achieve_id){
                continue;
            }
            $aid = (int)$pa->achieve_id;
            if(!isset($assignedWinners[$aid])){
                $assignedWinners[$aid] = [];
            }
            if(!isset($assignedAchievementRows[$aid])){
                $assignedAchievementRows[$aid] = [];
            }
            $assignedAchievementRows[$aid][] = $pa;
            $participantName = Html::encode((string)$analysisModel->participantText);
            $groupName = trim((string)($analysisModel->group_name ?? ''));
            if($groupName !== ''){
                $participantName = Html::tag('span', Html::encode($groupName), ['class' => 'badge bg-secondary me-2']) . $participantName;
            }
            if($pa->winnerTitle && trim((string)$pa->winnerTitle->title_name) !== ''){
                $participantName .= ' <span class="text-muted">(' . Html::encode((string)$pa->winnerTitle->title_name) . ')</span>';
            }
            $assignedWinners[$aid][] = $participantName;
        }
    }
}

if(isset($achievements) && $achievements){
    foreach($achievements as $a){
        $aid = (int)$a->id;
        $achievementSummary[$aid] = [
            'name' => (string)$a->name,
            'winner_count' => (int)$a->winner_count,
            'assigned_count' => isset($assignedWinners[$aid]) ? count($assignedWinners[$aid]) : 0,
            'titles' => $a->winnerTitles,
        ];
    }
}
$hasActiveFilters = function()use($searchModel){
    $filters = Yii::$app->request->get($searchModel->formName(), []);
    if(!is_array($filters)){
        return false;
    }
    unset($filters['rubric'], $filters['stage']);
    foreach($filters as $value){
        if($value !== null && $value !== ''){
            return true;
        }
    }
    return false;
};
$clearFilterUrl = function()use($program, $programSub){
    $params = ['/program-registration/manager-analysis', 'id' => $program->id];
    if($programSub){
        $params['sub'] = $programSub->id;
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

    <div class="d-flex align-items-center gap-2 mt-3">
        <?=Html::button('<i class="bi bi-download"></i> Excel Analysis', ['id' => 'dwl-exl','class' => 'btn btn-success'])?>
        <?= Html::button('<i class="bi bi-funnel"></i> Hide Filter Form', ['id' => 'toggle-filter-form', 'class' => 'btn btn-outline-secondary']) ?>
        <?= Html::button('<i class="bi bi-ui-checks"></i> Achievement Form', ['id' => 'toggle-achievement-form', 'class' => 'btn btn-outline-secondary']) ?>
        <?php if($hasActiveFilters()): ?>
            <?= Html::a('<i class="bi bi-x-circle"></i> Clear Filter', $clearFilterUrl(), ['class' => 'btn btn-outline-secondary']) ?>
        <?php endif; ?>
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

        var defaultOpenCard = ' . json_encode((string)$openAnalysisCard) . ';
        var analysisCards = {
            "filter": {
                card: $("#con-filter-form"),
                button: $("#toggle-filter-form"),
                labelOpen: "<i class=\"bi bi-funnel\"></i> Hide Filter Form",
                labelClosed: "<i class=\"bi bi-funnel\"></i> Show Filter Form"
            },
            "achievement-form": {
                card: $("#achievement-form-card"),
                button: $("#toggle-achievement-form"),
                labelOpen: "<i class=\"bi bi-ui-checks\"></i> Achievement Form",
                labelClosed: "<i class=\"bi bi-ui-checks\"></i> Achievement Form"
            }
        };

        function setAnalysisCard(openKey){
            $.each(analysisCards, function(key, item){
                var isOpen = key === openKey;
                item.card.toggle(isOpen);
                item.button
                    .toggleClass("btn-secondary", isOpen)
                    .toggleClass("btn-outline-secondary", !isOpen)
                    .html(isOpen ? item.labelOpen : item.labelClosed);
            });
        }

        $.each(analysisCards, function(key, item){
            item.button.click(function(){
                setAnalysisCard(item.card.is(":visible") ? "" : key);
            });
        });

        $(".achievement-add-row").click(function(){
            var table = $(this).siblings(".achievement-sub-table");
            var template = table.find(".achievement-template-row").first();
            var nextIndex = parseInt(table.data("next-index"), 10) || 1;
            var row = template.clone();

            row.removeClass("achievement-template-row")
                .addClass("achievement-added-row")
                .show();
            row.find("[name]").each(function(){
                $(this).attr("name", $(this).attr("name").replace("__INDEX__", "extra_" + nextIndex));
            });
            row.find("[id]").each(function(){
                $(this).attr("id", $(this).attr("id").replace("__INDEX__", "extra-" + nextIndex));
            });
            row.find(".achievement-new-input").prop("disabled", false);
            table.find(".achievement-empty-row").hide();
            template.before(row);
            table.data("next-index", nextIndex + 1);
            if($.fn.select2){
                row.find(".achievement-participant-select").select2({
                    allowClear: true,
                    placeholder: "Select Participant",
                    width: "100%"
                });
            }
            row.find(".achievement-new-input").first().focus();
        });

        $(document).on("click", ".achievement-remove-row", function(){
            var row = $(this).closest("tr");
            row.find(".achievement-remove-input").val("1");
            row.hide();
        });

        setAnalysisCard(defaultOpenCard);
        ');
        ?>

    
    <br />

    <div class="card"  id="con-filter-form">
    <div class="card-header">Filter Form</div>
    <div class="card-body pt-4">
    <?= $this->render('_search_analysis', [
        'model' => $searchModel,
        'rubrics' => $rubrics,
        'achievements' => $achievements,
        'stages' =>$stages,
        'programSub' => $programSub,
        'selectedRubric' => $selectedRubric,
        'action' => 'manager-analysis'
    ]) ?>
</div></div>

    <div class="card" id="achievement-form-card">
        <div class="card-header">Achievement Form</div>
        <div class="card-body pt-4">
            <?php if($analysisModels && $achievements): ?>
                <?php
                $suggestionUrlParams = ['manager-analysis-suggestion', 'id' => $role->program_id];
                if($programSub){
                    $suggestionUrlParams['sub'] = $programSub->id;
                }
                $suggestionUrl = Url::to(array_merge($suggestionUrlParams, Yii::$app->request->queryParams));
                ?>
                <div class="mb-3">
                    <?= Html::a('<i class="bi bi-lightbulb"></i> Show Suggestion', $suggestionUrl, ['class' => 'btn btn-outline-primary']) ?>
                </div>
                <?= Html::beginForm('', 'post') ?>
                <?= Html::hiddenInput('action_type', 'analysis-achievement-bulk') ?>
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead>
                            <tr>
                                <th style="min-width: 280px;">Achievement</th>
                                <th style="width: 180px;">Number of Winner</th>
                                <th style="min-width: 520px;">Existing Groups</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($achievements as $achievement): ?>
                                <?php
                                $achievementId = (int)$achievement->id;
                                $achievementRow = $achievementSummary[(int)$achievement->id] ?? [
                                    'assigned_count' => 0,
                                    'winner_count' => (int)$achievement->winner_count,
                                ];
                                $winnerTitleOptions = ['' => 'No winner title'];
                                if($hasWinnerTitleSelection && isset($winnerTitlesByAchievement[(int)$achievement->id])){
                                    foreach($winnerTitlesByAchievement[(int)$achievement->id] as $winnerTitle){
                                        $title = trim((string)$winnerTitle->title_name);
                                        if($title === ''){
                                            $title = 'Winner ' . (int)$winnerTitle->winner_order . ' (no title)';
                                        }
                                        $winnerTitleOptions[(int)$winnerTitle->id] = $title;
                                    }
                                }
                                $existingAchievementRows = $assignedAchievementRows[$achievementId] ?? [];
                                $emptyWinnerSlotCount = max(0, (int)$achievementRow['winner_count'] - count($existingAchievementRows));
                                ?>
                                <tr>
                                    <td><?= Html::encode((string)$achievement->name) ?></td>
                                    <td>
                                        <?= Html::a(
                                            '<span class="badge bg-primary">' . (int)$achievementRow['assigned_count'] . '</span>',
                                            $achievementFilterUrl($achievement->id),
                                            ['title' => 'Filter by this achievement']
                                        ) ?>
                                        <span class="text-muted ms-2">/ <?= (int)$achievementRow['winner_count'] ?></span>
                                    </td>
                                    <td>
                                        <table class="table table-sm mb-2 achievement-sub-table" data-next-index="<?= $emptyWinnerSlotCount + 1 ?>">
                                            <thead>
                                                <tr>
                                                    <th>Participant</th>
                                                    <th style="width: 260px;">Winner Title</th>
                                                    <th style="width: 100px;"></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php if($existingAchievementRows): ?>
                                                    <?php foreach($existingAchievementRows as $participantAchievement): ?>
                                                        <tr>
                                                            <td>
                                                                <?= Select2::widget([
                                                                    'name' => 'achievement_form[' . $achievementId . '][rows][' . (int)$participantAchievement->id . '][program_reg_id]',
                                                                    'value' => (int)$participantAchievement->program_reg_id,
                                                                    'data' => $participantOptions,
                                                                    'options' => [
                                                                        'id' => 'achievement-participant-' . $achievementId . '-' . (int)$participantAchievement->id,
                                                                        'class' => 'achievement-participant-select',
                                                                        'placeholder' => 'Select Participant',
                                                                    ],
                                                                    'pluginOptions' => [
                                                                        'allowClear' => true,
                                                                        'width' => '100%',
                                                                    ],
                                                                ]) ?>
                                                            </td>
                                                            <td>
                                                                <?= Html::dropDownList(
                                                                    'achievement_form[' . $achievementId . '][rows][' . (int)$participantAchievement->id . '][winner_title_id]',
                                                                    $hasWinnerTitleSelection ? (int)$participantAchievement->winner_title_id : '',
                                                                    $winnerTitleOptions,
                                                                    [
                                                                        'class' => 'form-select',
                                                                        'disabled' => !$hasWinnerTitleSelection,
                                                                    ]
                                                                ) ?>
                                                            </td>
                                                            <td>
                                                                <?= Html::hiddenInput(
                                                                    'achievement_form[' . $achievementId . '][rows][' . (int)$participantAchievement->id . '][remove]',
                                                                    0,
                                                                    ['class' => 'achievement-remove-input']
                                                                ) ?>
                                                                <?= Html::button('<i class="bi bi-trash"></i> Remove', [
                                                                    'class' => 'btn btn-outline-danger btn-sm achievement-remove-row',
                                                                    'type' => 'button',
                                                                ]) ?>
                                                            </td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                <?php endif; ?>
                                                <?php for($slotIndex = 1; $slotIndex <= $emptyWinnerSlotCount; $slotIndex++): ?>
                                                    <tr>
                                                        <td>
                                                            <?= Select2::widget([
                                                                'name' => 'achievement_form[' . $achievementId . '][rows][new_' . $slotIndex . '][program_reg_id]',
                                                                'value' => '',
                                                                'data' => $participantOptions,
                                                                'options' => [
                                                                    'id' => 'achievement-participant-' . $achievementId . '-new-' . $slotIndex,
                                                                    'class' => 'achievement-participant-select',
                                                                    'placeholder' => 'Select Participant',
                                                                ],
                                                                'pluginOptions' => [
                                                                    'allowClear' => true,
                                                                    'width' => '100%',
                                                                ],
                                                            ]) ?>
                                                        </td>
                                                        <td>
                                                            <?= Html::dropDownList(
                                                                'achievement_form[' . $achievementId . '][rows][new_' . $slotIndex . '][winner_title_id]',
                                                                '',
                                                                $winnerTitleOptions,
                                                                [
                                                                    'class' => 'form-select',
                                                                    'disabled' => !$hasWinnerTitleSelection,
                                                                ]
                                                            ) ?>
                                                        </td>
                                                        <td></td>
                                                    </tr>
                                                <?php endfor; ?>
                                                <?php if(!$existingAchievementRows && $emptyWinnerSlotCount === 0): ?>
                                                    <tr class="achievement-empty-row">
                                                        <td colspan="3" class="text-muted">No group added yet.</td>
                                                    </tr>
                                                <?php endif; ?>
                                                <tr class="achievement-template-row" style="display:none;">
                                                    <td>
                                                        <?= Html::dropDownList(
                                                            'achievement_form[' . $achievementId . '][rows][__INDEX__][program_reg_id]',
                                                            '',
                                                            $participantOptions,
                                                            [
                                                                'id' => 'achievement-participant-' . $achievementId . '-__INDEX__',
                                                                'class' => 'form-select achievement-new-input achievement-participant-select',
                                                                'disabled' => true,
                                                                'prompt' => 'Select Participant',
                                                            ]
                                                        ) ?>
                                                    </td>
                                                    <td>
                                                        <?= Html::dropDownList(
                                                            'achievement_form[' . $achievementId . '][rows][__INDEX__][winner_title_id]',
                                                            '',
                                                            $winnerTitleOptions,
                                                            [
                                                                'class' => $hasWinnerTitleSelection ? 'form-select achievement-new-input' : 'form-select',
                                                                'disabled' => true,
                                                            ]
                                                        ) ?>
                                                    </td>
                                                    <td></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                        <?= Html::button('<i class="bi bi-plus-circle"></i> Add', [
                                            'class' => 'btn btn-outline-primary btn-sm achievement-add-row',
                                            'type' => 'button',
                                        ]) ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?= Html::submitButton('<i class="bi bi-check2-circle"></i> Save', ['class' => 'btn btn-primary']) ?>
                <?= Html::endForm() ?>
            <?php else: ?>
                <span class="text-muted">No participants or achievements found for the current filter.</span>
            <?php endif; ?>
        </div>
    </div>


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
                'format' => 'raw',
                'value' => function($model){
                    $name = Html::encode($model->participantText);
                    $groupName = trim((string)($model->group_name ?? ''));
                    if($groupName === ''){
                        return $name;
                    }
                    $badge = Html::tag('span', Html::encode($groupName), ['class' => 'badge bg-secondary me-2']);
                    return $badge . $name;
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
                'label' =>'Medal',
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
