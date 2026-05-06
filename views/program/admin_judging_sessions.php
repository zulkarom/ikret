<?php

use app\models\RubricJudgingSession;
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'Judging Sessions';

$modeList = RubricJudgingSession::listMode();
$programId = (int)$programId;
$programSubSelected = $programSubId === null ? '' : (string)(int)$programSubId;

$formatDatetime = function($value){
    if(!$value){
        return '<span class="text-muted">-</span>';
    }
    return Html::encode(date('d M Y, h:i A', strtotime($value)));
};

?>
<div class="pagetitle">
<h1><?=$this->title?></h1></div>

</div><!-- End Page Title -->

<section class="section dashboard">

<div class="card mb-3">
    <div class="card-header">Filter</div>
    <div class="card-body pt-3">
        <?= Html::beginForm(Url::to(['program/admin-judging-sessions']), 'get', ['class' => 'row g-2 align-items-end']) ?>
        <div class="col-12 col-md-5">
            <label class="form-label">Program</label>
            <select name="program_id" class="form-select">
                <option value="">All Programs</option>
                <?php foreach($programs as $program){ ?>
                    <option value="<?= (int)$program->id ?>" <?= $programId === (int)$program->id ? 'selected' : '' ?>>
                        <?= Html::encode(($program->program_abbr ? $program->program_abbr . ' - ' : '') . $program->program_name) ?>
                    </option>
                <?php } ?>
            </select>
        </div>
        <div class="col-12 col-md-5">
            <label class="form-label">Sub Program</label>
            <select name="program_sub" class="form-select">
                <option value="">All Sub Programs</option>
                <option value="0" <?= $programSubSelected === '0' ? 'selected' : '' ?>>Program Level Only</option>
                <?php foreach($subs as $sub){ ?>
                    <?php
                    $program = $sub->program;
                    $label = ($program ? (($program->program_abbr ?: $program->program_name) . ' / ') : '') . $sub->sub_name;
                    ?>
                    <option value="<?= (int)$sub->id ?>" <?= $programSubSelected === (string)(int)$sub->id ? 'selected' : '' ?>>
                        <?= Html::encode($label) ?>
                    </option>
                <?php } ?>
            </select>
        </div>
        <div class="col-12 col-md-2">
            <button type="submit" class="btn btn-primary w-100">Filter</button>
        </div>
        <?= Html::endForm() ?>
    </div>
</div>

<?= Html::beginForm(Url::to(array_merge(['program/admin-judging-sessions'], Yii::$app->request->get())), 'post') ?>
<?= Html::hiddenInput(Yii::$app->request->csrfParam, Yii::$app->request->getCsrfToken()) ?>

<div class="card mb-3">
    <div class="card-header">Bulk Edit Selected</div>
    <div class="card-body pt-3">
        <div class="row g-3 align-items-end">
            <div class="col-12 col-lg-3">
                <div class="form-check mb-1">
                    <input class="form-check-input" type="checkbox" name="apply[datetime_start]" value="1" id="apply-start">
                    <label class="form-check-label" for="apply-start">Update Start</label>
                </div>
                <input type="datetime-local" name="datetime_start" class="form-control">
            </div>
            <div class="col-12 col-lg-3">
                <div class="form-check mb-1">
                    <input class="form-check-input" type="checkbox" name="apply[datetime_end]" value="1" id="apply-end">
                    <label class="form-check-label" for="apply-end">Update End</label>
                </div>
                <input type="datetime-local" name="datetime_end" class="form-control">
            </div>
            <div class="col-12 col-lg-3">
                <div class="form-check mb-1">
                    <input class="form-check-input" type="checkbox" name="apply[location]" value="1" id="apply-location">
                    <label class="form-check-label" for="apply-location">Update Location</label>
                </div>
                <input type="text" name="location" class="form-control" placeholder="Location">
            </div>
            <div class="col-12 col-lg-2">
                <div class="form-check mb-1">
                    <input class="form-check-input" type="checkbox" name="apply[mode]" value="1" id="apply-mode">
                    <label class="form-check-label" for="apply-mode">Update Mode</label>
                </div>
                <select name="mode" class="form-select">
                    <?php foreach($modeList as $value => $label){ ?>
                        <option value="<?= (int)$value ?>"><?= Html::encode($label) ?></option>
                    <?php } ?>
                </select>
            </div>
            <div class="col-12 col-lg-1">
                <button type="submit" class="btn btn-primary w-100" data-confirm="Update selected judging sessions?">Save</button>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-body pt-4">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th style="width: 42px;">
                            <input type="checkbox" class="form-check-input" id="select-all-sessions">
                        </th>
                        <th style="width: 60px;">ID</th>
                        <th>Program / Sub</th>
                        <th>Rubric</th>
                        <th>Session</th>
                        <th style="width: 170px;">Start</th>
                        <th style="width: 170px;">End</th>
                        <th>Location</th>
                        <th style="width: 110px;">Mode</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(!$rows){ ?>
                        <tr>
                            <td colspan="9" class="text-center text-muted py-4">No judging sessions found.</td>
                        </tr>
                    <?php } ?>
                    <?php foreach($rows as $row){ ?>
                        <?php
                        $programLabel = ($row['program_abbr'] ?: $row['program_name']);
                        if($row['sub_name']){
                            $programLabel .= ' / ' . $row['sub_name'];
                        }
                        ?>
                        <tr>
                            <td>
                                <input type="checkbox" class="form-check-input session-check" name="selection[]" value="<?= (int)$row['session_id'] ?>">
                            </td>
                            <td><?= (int)$row['session_id'] ?></td>
                            <td>
                                <?= Html::a(Html::encode($programLabel), ['/program/rubrics', 'id' => (int)$row['program_id'], 'sub' => $row['program_sub_id'] ? (int)$row['program_sub_id'] : null]) ?>
                            </td>
                            <td><?= Html::encode($row['rubric_name']) ?></td>
                            <td><?= Html::encode($row['session_name']) ?></td>
                            <td><?= $formatDatetime($row['datetime_start']) ?></td>
                            <td><?= $formatDatetime($row['datetime_end']) ?></td>
                            <td><?= $row['location'] ? Html::encode($row['location']) : '<span class="text-muted">-</span>' ?></td>
                            <td><?= Html::encode($modeList[(int)$row['mode']] ?? '-') ?></td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?= Html::endForm() ?>

</section>

<?php
$this->registerJs(<<<JS
$('#select-all-sessions').on('change', function(){
    $('.session-check').prop('checked', this.checked);
});
$('.session-check').on('change', function(){
    var all = $('.session-check').length;
    var checked = $('.session-check:checked').length;
    $('#select-all-sessions').prop('checked', all > 0 && all === checked);
});
JS
);
?>
