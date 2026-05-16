<?php

use app\models\ParticipantAchieve;
use yii\bootstrap5\LinkPager;
use yii\helpers\Html;
use yii\helpers\Url;

/** @var yii\web\View $this */
/** @var yii\data\ActiveDataProvider $dataProvider */
/** @var bool $hasWinnerCountColumn */
/** @var bool $hasWinnerTitleTable */
/** @var bool $hasWinnerTitleAchievementColumn */
/** @var array $winnerTitlesByAchievement */
/** @var array $targetOptions */

$this->title = 'Achievement Config';
$this->params['breadcrumbs'][] = $this->title;
$models = $dataProvider->getModels();

$this->registerCss(<<<CSS
.achievement-config-table {
    table-layout: fixed;
}
.achievement-config-table th,
.achievement-config-table td {
    vertical-align: middle;
}
.achievement-config-table .form-control-sm,
.achievement-config-table .input-group-sm > .form-control,
.achievement-config-table .input-group-sm > .input-group-text {
    font-size: .78rem;
}
.achievement-config-table__select { width: 34px; }
.achievement-config-table__no { width: 48px; }
.achievement-config-table__scope { width: 13%; }
.achievement-config-table__name { width: 24%; }
.achievement-config-table__winners { width: 92px; }
.achievement-config-table__titles { width: 25%; }
.achievement-config-table__used { width: 118px; }
.achievement-config-table__action { width: 128px; }
.achievement-config-table__actions {
    display: flex;
    gap: .35rem;
    flex-wrap: wrap;
}
.achievement-config-table__actions form {
    margin: 0;
}
.achievement-config-table__actions .btn {
    padding-left: .55rem;
    padding-right: .55rem;
}
.achievement-config-table__scope-text {
    overflow-wrap: anywhere;
    line-height: 1.2;
}
.achievement-config-table .badge {
    white-space: normal;
    line-height: 1.15;
}
.bulk-winner-title-row.is-hidden {
    display: none;
}
@media (min-width: 1200px) {
    .achievement-config-table .input-group {
        max-width: 210px;
    }
}
@media (max-width: 991.98px) {
    .achievement-config-table {
        table-layout: auto;
    }
}
CSS);

$this->registerJs(<<<JS
(function(){
    var select = document.getElementById('bulk-winner-count');
    var rows = document.querySelectorAll('.bulk-winner-title-row');
    if(!select || !rows.length){
        return;
    }
    var syncRows = function(){
        var count = parseInt(select.value || '0', 10);
        rows.forEach(function(row){
            var order = parseInt(row.getAttribute('data-winner-order') || '0', 10);
            row.classList.toggle('is-hidden', order > count);
        });
    };
    select.addEventListener('change', syncRows);
    syncRows();
})();
JS);
?>

<div class="pagetitle">
    <h1><?= Html::encode($this->title) ?></h1>
</div>

</div><!-- End Page Title -->

<section class="section dashboard">
    <div class="mb-3 d-flex flex-wrap gap-2">
        <?= Html::a('<i class="bi bi-upload"></i> Import CSV', ['achievement-import'], ['class' => 'btn btn-primary']) ?>
        <?= Html::button('<i class="bi bi-list-check"></i> Bulk Winner Titles', [
            'class' => 'btn btn-outline-primary',
            'data-bs-toggle' => 'modal',
            'data-bs-target' => '#bulk-winner-title-modal',
            'disabled' => !$hasWinnerCountColumn || !$hasWinnerTitleAchievementColumn,
        ]) ?>
    </div>

    <div class="modal fade" id="bulk-winner-title-modal" tabindex="-1" aria-labelledby="bulk-winner-title-modal-label" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <?= Html::beginForm(Url::to(['certificate-template/achievement-config']), 'post') ?>
                <?= Html::hiddenInput(Yii::$app->request->csrfParam, Yii::$app->request->getCsrfToken()) ?>
                <?= Html::hiddenInput('action_type', 'bulk-winner-title-update') ?>
                <div class="modal-header">
                    <h5 class="modal-title" id="bulk-winner-title-modal-label">Bulk Winner Titles</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label" for="bulk-winner-count">Number of Winners</label>
                        <?= Html::dropDownList('bulk_winner_count', 3, [
                            2 => '2 winners',
                            3 => '3 winners',
                            4 => '4 winners',
                            5 => '5 winners',
                        ], [
                            'class' => 'form-select',
                            'id' => 'bulk-winner-count',
                            'required' => true,
                        ]) ?>
                    </div>
                    <?php for($winnerNo = 1; $winnerNo <= 5; $winnerNo++): ?>
                        <div class="mb-2 bulk-winner-title-row" data-winner-order="<?= $winnerNo ?>">
                            <label class="form-label" for="bulk-winner-title-<?= $winnerNo ?>">Winner <?= $winnerNo ?> Title</label>
                            <?= Html::textInput('bulk_winner_titles[' . $winnerNo . ']', '', [
                                'class' => 'form-control',
                                'id' => 'bulk-winner-title-' . $winnerNo,
                                'placeholder' => 'Winner title',
                            ]) ?>
                        </div>
                    <?php endfor; ?>
                </div>
                <div class="modal-footer">
                    <?= Html::button('Cancel', ['class' => 'btn btn-outline-secondary', 'data-bs-dismiss' => 'modal']) ?>
                    <?= Html::submitButton('Update All', [
                        'class' => 'btn btn-primary',
                        'data-confirm' => 'Update winner titles for all achievements with this number of winners?',
                    ]) ?>
                </div>
                <?= Html::endForm() ?>
            </div>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-header">Achievement</div>
        <div class="card-body pt-3">
            <?= Html::beginForm(Url::to(['certificate-template/achievement-config']), 'post', ['class' => 'row g-2']) ?>
            <?= Html::hiddenInput(Yii::$app->request->csrfParam, Yii::$app->request->getCsrfToken()) ?>
            <?= Html::hiddenInput('action_type', 'add') ?>
            <div class="col-12 col-lg-4">
                <?= Html::dropDownList('achievement_target', null, $targetOptions, [
                    'class' => 'form-select',
                    'prompt' => 'Select program / sub',
                    'required' => true,
                ]) ?>
            </div>
            <div class="<?= $hasWinnerCountColumn ? 'col-12 col-lg-4' : 'col-12 col-lg-5' ?>">
                <?= Html::textInput('name', '', ['class' => 'form-control', 'placeholder' => 'Achievement name', 'required' => true]) ?>
            </div>
            <?php if($hasWinnerCountColumn): ?>
                <div class="col-12 col-lg-2">
                    <?= Html::input('number', 'winner_count', '', ['class' => 'form-control', 'placeholder' => 'Winners', 'min' => 0]) ?>
                </div>
            <?php endif; ?>
            <div class="col-12 col-lg-2">
                <?= Html::submitButton('Add', ['class' => 'btn btn-primary w-100']) ?>
            </div>
            <?= Html::endForm() ?>

            <?php if($hasWinnerCountColumn && !$hasWinnerTitleAchievementColumn): ?>
                <div class="alert alert-warning mt-3 mb-0">
                    Winner title inputs need the achievement-based table. Please run <code>db/sql/2026-05-10_create_program_winner_title.sql</code>
                    <?php if($hasWinnerTitleTable): ?> or <code>db/sql/2026-05-10_update_program_winner_title_depend_achievement.sql</code><?php endif; ?>.
                </div>
            <?php endif; ?>
            <?php if($hasWinnerCountColumn && $hasWinnerTitleAchievementColumn): ?>
                <div class="form-text mt-2">
                    Leave a winner title blank when the certificate should show no title text for that winner.
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="card">
        <div class="card-body pt-4">
            <?= Html::beginForm(Url::to(['certificate-template/achievement-config']), 'post', ['id' => 'achievement-bulk-delete']) ?>
            <?= Html::hiddenInput(Yii::$app->request->csrfParam, Yii::$app->request->getCsrfToken()) ?>
            <?= Html::hiddenInput('action_type', 'bulk-delete') ?>
            <?= Html::endForm() ?>

            <div class="table-responsive">
                <table class="table table-sm achievement-config-table">
                    <thead>
                        <tr>
                            <th class="achievement-config-table__select"></th>
                            <th class="achievement-config-table__no">No.</th>
                            <th class="achievement-config-table__scope">Prog/Sub</th>
                            <th class="achievement-config-table__name">Achievement</th>
                            <?php if($hasWinnerCountColumn): ?>
                                <th class="achievement-config-table__winners">Winners</th>
                                <th class="achievement-config-table__titles">Winner's Title</th>
                            <?php endif; ?>
                            <th class="achievement-config-table__used">Used</th>
                            <th class="achievement-config-table__action">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php if($models): ?>
                        <?php
                        $pagination = $dataProvider->getPagination();
                        $offset = $pagination ? $pagination->getOffset() : 0;
                        foreach($models as $index => $model):
                            $usedCount = (int)ParticipantAchieve::find()->where(['achieve_id' => $model->id])->count();
                            $formId = 'achievement-update-' . (int)$model->id;
                            $programName = $model->program ? ($model->program->program_abbr ?: $model->program->program_name) : '';
                            $subName = $model->programSub ? ($model->programSub->sub_abbr ?: $model->programSub->sub_name) : '';
                            $programLabel = $model->programSub ? $programName . ' / ' . $subName : $programName;
                        ?>
                            <tr>
                                <td>
                                    <?php if($usedCount === 0): ?>
                                        <?= Html::checkbox('selection[]', false, [
                                            'value' => $model->id,
                                            'form' => 'achievement-bulk-delete',
                                        ]) ?>
                                    <?php endif; ?>
                                </td>
                                <td><?= Html::encode((string)($offset + $index + 1)) ?>.</td>
                                <td><div class="achievement-config-table__scope-text"><?= Html::encode($programLabel) ?></div></td>
                                <td>
                                    <?= Html::textInput('name', $model->name, [
                                        'class' => 'form-control form-control-sm',
                                        'required' => true,
                                        'form' => $formId,
                                    ]) ?>
                                </td>
                                <?php if($hasWinnerCountColumn): ?>
                                    <td>
                                        <?= Html::input('number', 'winner_count', $model->winner_count, [
                                            'class' => 'form-control form-control-sm',
                                            'min' => 0,
                                            'form' => $formId,
                                        ]) ?>
                                    </td>
                                    <td>
                                        <?php if($hasWinnerTitleAchievementColumn): ?>
                                            <?php $winnerCount = max(0, (int)$model->winner_count); ?>
                                            <?php if($winnerCount > 0): ?>
                                                <?php for($winnerNo = 1; $winnerNo <= $winnerCount; $winnerNo++): ?>
                                                    <?php $winnerTitle = $winnerTitlesByAchievement[$model->id][$winnerNo] ?? null; ?>
                                                    <div class="input-group input-group-sm mb-1">
                                                        <span class="input-group-text"><?= Html::encode((string)$winnerNo) ?></span>
                                                        <?= Html::textInput('winner_titles[' . $winnerNo . ']', $winnerTitle ? $winnerTitle->title_name : '', [
                                                            'class' => 'form-control',
                                                            'placeholder' => 'Winner title',
                                                            'form' => $formId,
                                                        ]) ?>
                                                    </div>
                                                <?php endfor; ?>
                                            <?php else: ?>
                                                <span class="text-muted">Set number of winner first.</span>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            <span class="text-muted">Migration required</span>
                                        <?php endif; ?>
                                    </td>
                                <?php endif; ?>
                                <td>
                                    <?= $usedCount > 0
                                        ? Html::tag('span', $usedCount . ' participant(s)', ['class' => 'badge bg-info'])
                                        : Html::tag('span', 'Not used', ['class' => 'badge bg-secondary']) ?>
                                </td>
                                <td>
                                    <div class="achievement-config-table__actions">
                                    <?= Html::beginForm(Url::to(['certificate-template/achievement-config']), 'post', ['id' => $formId, 'style' => 'display:inline-block;margin-right:6px;']) ?>
                                    <?= Html::hiddenInput(Yii::$app->request->csrfParam, Yii::$app->request->getCsrfToken()) ?>
                                    <?= Html::hiddenInput('action_type', 'update') ?>
                                    <?= Html::hiddenInput('achievement_id', $model->id) ?>
                                    <?= Html::submitButton('Save', ['class' => 'btn btn-primary btn-sm']) ?>
                                    <?= Html::endForm() ?>

                                    <?php if($usedCount === 0): ?>
                                        <?= Html::beginForm(Url::to(['certificate-template/achievement-config']), 'post', ['style' => 'display:inline-block;']) ?>
                                        <?= Html::hiddenInput(Yii::$app->request->csrfParam, Yii::$app->request->getCsrfToken()) ?>
                                        <?= Html::hiddenInput('action_type', 'delete') ?>
                                        <?= Html::hiddenInput('achievement_id', $model->id) ?>
                                        <?= Html::submitButton('Delete', ['class' => 'btn btn-outline-danger btn-sm', 'data-confirm' => 'Delete this achievement?']) ?>
                                        <?= Html::endForm() ?>
                                    <?php else: ?>
                                        <?= Html::button('Delete', ['class' => 'btn btn-outline-secondary btn-sm', 'disabled' => true, 'title' => 'Already used']) ?>
                                    <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="<?= $hasWinnerCountColumn ? 8 : 6 ?>" class="text-muted">No achievement found.</td></tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <div class="d-flex flex-wrap gap-2 align-items-center justify-content-between mt-3">
                <?= Html::submitButton('Delete Selected', [
                    'class' => 'btn btn-outline-danger',
                    'form' => 'achievement-bulk-delete',
                    'data-confirm' => 'Delete selected achievements? Achievements already used will be skipped.',
                ]) ?>
                <?= LinkPager::widget([
                    'pagination' => $dataProvider->getPagination(),
                    'options' => ['class' => 'pagination mb-0'],
                ]) ?>
            </div>
        </div>
    </div>
</section>
