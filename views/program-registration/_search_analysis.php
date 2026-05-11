<?php

use yii\bootstrap5\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\ProgramRegistrationSearch $model */
/** @var yii\widgets\ActiveForm $form */
$selectedRubric = $selectedRubric ?? null;
$isJuryResultSearch = $model instanceof \app\models\JuryResultSearch;
$isManagerAnalysisSearch = $model instanceof \app\models\ManagerAnalysisSearch;
$achievements = $achievements ?? [];
$recommendationOptions = [];
if(($isJuryResultSearch || $isManagerAnalysisSearch) && $selectedRubric && $selectedRubric->categoriesRecommend){
    foreach($selectedRubric->categoriesRecommend as $cat){
        if(!$cat->itemsRecommend){
            continue;
        }
        foreach($cat->itemsRecommend as $item){
            $label = trim((string)($item->item_short ?: $item->item_text));
            if($label !== ''){
                $recommendationOptions[$item->id] = $label;
            }
        }
    }
}
?>

<div class="program-registration-search">
    

    <?php
    $url = [$action, 'id' => $model->program_id];
    if($programSub){
        $url = [$action, 'id' => $model->program_id, 'sub' => $programSub->id];
    }
    $form = ActiveForm::begin([
        'action' => $url,
        'method' => 'get',
    ]); ?>
    <div class="row">
        <div class="<?= ($isJuryResultSearch || $isManagerAnalysisSearch) ? 'col-md-6' : 'col-12' ?>">
            <?= $form->field($model, 'fullnameSearch')->textInput(['placeholder' => 'Search Participant'])->label(false) ?>
        </div>
        <?php if($model->hasProperty('jurySearch')): ?>
            <div class="col-md-6">
                <?= $form->field($model, 'jurySearch')->textInput(['placeholder' => 'Search Jury'])->label(false) ?>
            </div>
        <?php endif; ?>
    </div>
    <?php if($model->hasProperty('statFilter')): ?>
        <?= Html::activeHiddenInput($model, 'statFilter') ?>
    <?php endif; ?>
    <?php if($model->hasProperty('awardFilter')): ?>
        <?= Html::activeHiddenInput($model, 'awardFilter') ?>
    <?php endif; ?>
    <div class="row">
        <?php if(!$isManagerAnalysisSearch): ?>
            <div class="<?= $isJuryResultSearch ? 'col-md-3' : 'col-md-6' ?>"><?= $form->field($model, 'rubric')->dropDownList(ArrayHelper::map($rubrics, 'rubric_id', 'rubric.rubric_name'))->label(false) ?></div>
        <?php endif; ?>
        <?php if($isJuryResultSearch): ?>
            <div class="col-md-3">
                <?= $form->field($model, 'jury_status')->dropDownList([
                    0 => 'Assigned',
                    20 => 'Complete',
                ], ['prompt' => 'All Status'])->label(false) ?>
            </div>
            <div class="col-md-3">
                <?= $form->field($model, 'recommendationItem')->dropDownList($recommendationOptions, ['prompt' => 'Recommendation Item'])->label(false) ?>
            </div>
        <?php elseif($isManagerAnalysisSearch): ?>
            <div class="col-md-6">
                <?= $form->field($model, 'recommendationItem')->dropDownList($recommendationOptions, ['prompt' => 'Recommendation Item'])->label(false) ?>
            </div>
            <div class="col-md-6">
                <?= $form->field($model, 'medal')->dropDownList([
                    'GOLD' => 'Gold',
                    'SILVER' => 'Silver',
                    'BRONZE' => 'Bronze',
                ], ['prompt' => 'Medal'])->label(false) ?>
            </div>
            <div class="col-md-6">
                <?= $form->field($model, 'achievementId')->dropDownList(ArrayHelper::map($achievements, 'id', 'name'), ['prompt' => 'Achievement'])->label(false) ?>
            </div>
        <?php endif; ?>

        <div class="<?= ($isJuryResultSearch && !$isManagerAnalysisSearch) ? 'col-md-3' : 'col-md-6' ?>"><?= Html::submitButton('Apply Filter', ['class' => 'btn btn-primary']) ?></div>
 
    </div>


    <?php ActiveForm::end(); ?>

</div>
