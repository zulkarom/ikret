<?php

use yii\helpers\Html;
use yii\helpers\Url;
use app\models\ParticipantAchieve;

/** @var yii\web\View $this */
/** @var app\models\ProgramRegistration $model */
/** @var bool $hasWinnerCountColumn */
/** @var bool $hasWinnerTitleTable */
/** @var bool $hasWinnerTitleAchievementColumn */
/** @var array $winnerTitlesByAchievement */
/** @var app\models\RubricItem[] $recommendationItems */

$this->title = 'Achievement: ' . $model->program_name;

$programSub = $programSub ?? null;

$this->params['breadcrumbs'][] = [
    'label' => $model->program_abbr . ($programSub ? ' / ' . $programSub->sub_abbr : ''),
    'url' => ['/program-registration/manager-dashboard', 'id' => $model->id, 'sub' => $programSub ? $programSub->id : null]
];
$this->params['breadcrumbs'][] = $this->title;
?>


<div class="pagetitle">
<h1><?=$this->title?></h1>
<?php 
if($programSub){
    echo $programSub->sub_name;
}

?>
</div>

    </div><!-- End Page Title -->

    <section class="section dashboard">

    <div class="card mb-3">
            <div class="card-header">Achievement & Recommendation Mapping</div>
            <div class="card-body pt-3">
                <?php
                $recommendationItems = $recommendationItems ?? [];
                $recommendationMap = [];
                if($recommendationItems){
                    foreach($recommendationItems as $item){
                        $label = trim((string)($item->item_short ?: $item->item_text));
                        if($label === ''){
                            $label = 'Item #' . (int)$item->id;
                        }
                        $recommendationMap[$item->id] = $label;
                    }
                }
                ?>

                <?php if(!$recommendationMap): ?>
                    <div class="text-muted">No recommendation items found in the assigned rubric(s).</div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table">
                            <tbody>
                                <tr>
                                    <th style="min-width: 360px;">Achievement</th>
                                    <th>Recommendation Item (Rubric)</th>
                                    <th style="width: 220px;">Action</th>
                                </tr>
                                <?php if($achievement): ?>
                                    <?php foreach($achievement as $a): ?>
                                        <?php $formId = 'achievement-map-' . (int)$a->id; ?>
                                        <tr>
                                            <td><?= Html::encode($a->name) ?></td>
                                            <td>
                                                <?= Html::beginForm(Url::to(['program/achievement', 'id' => $model->id, 'sub' => $programSub ? $programSub->id : null]), 'post', ['id' => $formId]) ?>
                                                <?= Html::hiddenInput(Yii::$app->request->csrfParam, Yii::$app->request->getCsrfToken()) ?>
                                                <?= Html::hiddenInput('action_type', 'map-recommendation') ?>
                                                <?= Html::hiddenInput('achievement_id', (int)$a->id) ?>
                                                <?= Html::dropDownList('rubric_item_id', $a->rubric_item_id, $recommendationMap, [
                                                    'class' => 'form-select form-select-sm',
                                                    'prompt' => 'Not linked',
                                                ]) ?>
                                                <?= Html::endForm() ?>
                                            </td>
                                            <td>
                                                <?= Html::submitButton('Save', ['class' => 'btn btn-primary btn-sm', 'form' => $formId]) ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr><td colspan="3" class="text-muted">No achievement found.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>

    <div class="card mb-3">
            <div class="card-header">Achievement</div>
            <div class="card-body pt-3">
                <?= Html::beginForm(Url::to(['program/achievement', 'id' => $model->id, 'sub' => $programSub ? $programSub->id : null]), 'post', ['class' => 'row g-2']) ?>
                <?= Html::hiddenInput(Yii::$app->request->csrfParam, Yii::$app->request->getCsrfToken()) ?>
                <?= Html::hiddenInput('action_type', 'add') ?>
                <div class="<?= $hasWinnerCountColumn ? 'col-12 col-md-7' : 'col-12 col-md-9' ?>">
                    <?= Html::textInput('name', '', ['class' => 'form-control', 'placeholder' => 'Achievement name', 'required' => true]) ?>
                </div>
                <?php if($hasWinnerCountColumn): ?>
                    <div class="col-12 col-md-2">
                        <?= Html::input('number', 'winner_count', '', ['class' => 'form-control', 'placeholder' => 'Winners', 'min' => 0]) ?>
                    </div>
                <?php endif; ?>
                <div class="col-12 col-md-3">
                    <?= Html::submitButton('Add', ['class' => 'btn btn-primary w-100']) ?>
                </div>
                <?= Html::endForm() ?>

                <?php if($hasWinnerCountColumn && !$hasWinnerTitleAchievementColumn): ?>
                    <div class="alert alert-warning mt-3 mb-0">
                        Winner title inputs need the achievement-based table. Please run <code>db/2026-05-10_create_program_winner_title.sql</code>
                        <?php if($hasWinnerTitleTable): ?> or <code>db/2026-05-10_update_program_winner_title_depend_achievement.sql</code><?php endif; ?>.
                    </div>
                <?php endif; ?>
                <?php if($hasWinnerCountColumn && $hasWinnerTitleAchievementColumn): ?>
                    <div class="form-text mt-2">
                        Leave a winner title blank when the certificate should show no title text for that winner.
                    </div>
                <?php endif; ?>

            <div class="table-responsive mt-4">
            <table class="table">
                <tbody>
                    <tr>
                        <th>No.</th>
                        <th style="min-width: 320px;">Achievement Name</th>
                        <?php if($hasWinnerCountColumn): ?>
                            <th style="width: 160px;">Number of Winner</th>
                            <th style="min-width: 260px;">Winner's Title</th>
                        <?php endif; ?>
                        <th style="width: 220px;">Used</th>
                        <th style="width: 260px;">Action</th>
                    </tr>
                    <?php 
                    if($achievement){
                        $i=1;
                        foreach($achievement as $a){
                            $usedCount = (int)ParticipantAchieve::find()->where(['achieve_id' => $a->id])->count();
                            $formId = 'achievement-update-' . (int)$a->id;
                            echo '<tr><td>'.$i.'. </td><td>';
                            echo Html::textInput('name', $a->name, ['class' => 'form-control form-control-sm', 'required' => true, 'form' => $formId]);
                            echo '</td>';
                            if($hasWinnerCountColumn){
                                echo '<td>';
                                echo Html::input('number', 'winner_count', $a->winner_count, ['class' => 'form-control form-control-sm', 'min' => 0, 'form' => $formId]);
                                echo '</td>';
                                echo '<td>';
                                if($hasWinnerTitleAchievementColumn){
                                    $winnerCount = max(0, (int)$a->winner_count);
                                    if($winnerCount > 0){
                                        for($winnerNo = 1; $winnerNo <= $winnerCount; $winnerNo++){
                                            $winnerTitle = $winnerTitlesByAchievement[$a->id][$winnerNo] ?? null;
                                            echo '<div class="input-group input-group-sm mb-1">';
                                            echo '<span class="input-group-text">' . $winnerNo . '</span>';
                                            echo Html::textInput('winner_titles[' . $winnerNo . ']', $winnerTitle ? $winnerTitle->title_name : '', [
                                                'class' => 'form-control',
                                                'placeholder' => 'Winner title',
                                                'form' => $formId,
                                            ]);
                                            echo '</div>';
                                        }
                                    }else{
                                        echo Html::tag('span', 'Set number of winner first.', ['class' => 'text-muted']);
                                    }
                                }else{
                                    echo Html::tag('span', 'Migration required', ['class' => 'text-muted']);
                                }
                                echo '</td>';
                            }
                            echo '<td>';
                            echo $usedCount > 0 ? Html::tag('span', $usedCount . ' participant(s)', ['class' => 'badge bg-info']) : Html::tag('span', 'Not used', ['class' => 'badge bg-secondary']);
                            echo '</td><td class="text-nowrap">';
                            echo Html::beginForm(Url::to(['program/achievement', 'id' => $model->id, 'sub' => $programSub ? $programSub->id : null]), 'post', ['id' => $formId, 'style' => 'display:inline-block;margin-right:6px;']);
                            echo Html::hiddenInput(Yii::$app->request->csrfParam, Yii::$app->request->getCsrfToken());
                            echo Html::hiddenInput('action_type', 'update');
                            echo Html::hiddenInput('achievement_id', $a->id);
                            echo Html::submitButton('Save', ['class' => 'btn btn-primary btn-sm']);
                            echo Html::endForm();
                            if($usedCount === 0){
                                echo Html::beginForm(Url::to(['program/achievement', 'id' => $model->id, 'sub' => $programSub ? $programSub->id : null]), 'post', ['style' => 'display:inline-block;']);
                                echo Html::hiddenInput(Yii::$app->request->csrfParam, Yii::$app->request->getCsrfToken());
                                echo Html::hiddenInput('action_type', 'delete');
                                echo Html::hiddenInput('achievement_id', $a->id);
                                echo Html::submitButton('Delete', ['class' => 'btn btn-outline-danger btn-sm', 'data-confirm' => 'Delete this achievement?']);
                                echo Html::endForm();
                            }else{
                                echo ' ' . Html::button('Delete', ['class' => 'btn btn-outline-secondary btn-sm', 'disabled' => true, 'title' => 'Already used']);
                            }
                            echo '</td></tr>';
                            $i++;
                        }
                    }else{
                        echo '<tr><td colspan="' . ($hasWinnerCountColumn ? 6 : 4) . '" class="text-muted">No achievement found.</td></tr>';
                    }
                    ?> 
                </tbody>
            </table>
            </div>
</div>
            </div>
        </div>


    </section>
