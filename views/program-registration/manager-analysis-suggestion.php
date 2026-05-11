<?php

use yii\helpers\Html;
use yii\helpers\Url;

/** @var yii\web\View $this */
/** @var app\models\ManagerAnalysisSearch $searchModel */
/** @var app\models\ProgramAchievement[] $achievements */
/** @var array $suggestionsByAchievement */
/** @var app\models\ProgramSub|null $programSub */
/** @var app\models\UserProgram|null $role */

$this->title = 'Suggestion - ' . $role->program->program_abbr . ($programSub ? ' / (' . $programSub->sub_abbr . ')' : '');
$this->params['breadcrumbs'][] = [
    'label' => 'Manager Analysis',
    'url' => ['/program-registration/manager-analysis', 'id' => $role->program_id, 'sub' => $programSub ? $programSub->id : null]
];
$this->params['breadcrumbs'][] = $this->title;

$backUrl = Url::to(array_merge(
    ['/program-registration/manager-analysis', 'id' => $role->program_id, 'sub' => $programSub ? $programSub->id : null],
    Yii::$app->request->queryParams
));
?>

<div class="pagetitle">
    <h1><?= Html::encode($this->title) ?></h1>
</div>

<div class="card">
    <div class="card-body pt-4">
        <div class="mb-3">
            <?= Html::a('<i class="bi bi-arrow-left"></i> Back', $backUrl, ['class' => 'btn btn-outline-secondary']) ?>
        </div>

        <?php if(!$achievements): ?>
            <div class="text-muted">No achievements configured.</div>
        <?php else: ?>
            <?php foreach($achievements as $a): ?>
                <?php $rows = $suggestionsByAchievement[(int)$a->id] ?? []; ?>

                <div class="card mb-3">
                    <div class="card-header"><?= Html::encode($a->name) ?></div>
                    <div class="card-body pt-3">
                        <?php if(!$rows): ?>
                            <div class="text-muted">No suggestion data available (missing jury marks / recommendation mapping).</div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th style="width:60px;">#</th>
                                            <th>Participant</th>
                                            <th style="width:140px;" class="text-end">Avg Score</th>
                                            <th style="width:180px;" class="text-end">Recommend Score</th>
                                            <th style="width:160px;" class="text-end">Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach($rows as $i => $r): ?>
                                            <tr>
                                                <td><?= (int)($i + 1) ?></td>
                                                <td><?= Html::encode($r['participant']) ?></td>
                                                <td class="text-end"><?= number_format((float)$r['avg_score'], 2) ?></td>
                                                <td class="text-end"><?= number_format((float)$r['recommend_score'], 2) ?></td>
                                                <td class="text-end"><b><?= number_format((float)$r['total'], 2) ?></b></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>
