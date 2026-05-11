<?php

use yii\bootstrap5\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;
use yii\helpers\HtmlPurifier;

/** @var yii\web\View $this */
/** @var app\models\ProgramRegistration $model */

$this->title = $title;
$rubric = $assign->rubric;
$register = $assign->registration;
$formName =  $model->formName();
$edit = $edit ?? false;
$program = $program ?? null;
$programSub = $programSub ?? null;
$canReturnResultJudging = !$write
    && !Yii::$app->user->isGuest
    && Yii::$app->user->identity->isAdminJury
    && (int)$assign->status === 20;
$canMarkNotNullified = !$write
    && !Yii::$app->user->isGuest
    && Yii::$app->user->identity->isAdminJury
    && (int)$assign->is_nullified === 1;
$backToResultListUrl = null;
if(!$write && $register){
    $backToResultListUrl = [
        '/program-registration/jury-result',
        'id' => $register->program_id,
        'sub' => $register->program_sub,
    ];
}
$groupBadgeHtml = '';
$participantInfoHtml = $register ? $register->shortFieldsHtml : '';
$participantMembersHtml = '';

if($register){
    $groupName = trim((string)$register->group_name);
    if($groupName !== ''){
        $colorClass = 'c' . ((crc32($groupName) % 6) + 1);
        $groupBadgeHtml = '<div><span class="participant-cell-group ' . $colorClass . '">' . Html::encode($groupName) . '</span></div>';
        $participantInfoHtml = preg_replace('~<li><i>Group Name:</i>.*?</li>~', '', $participantInfoHtml);
        $participantInfoHtml = preg_replace(
            '~<li><i>Program:</i>.*?</li>~',
            '',
            $participantInfoHtml,
            1
        );
    }

    if($register->user){
        $leaderName = trim((string)$register->user->fullname);
        $leaderMatric = trim((string)$register->user->matric);
    }else if(!empty($register->contact_person)){
        $leaderName = trim((string)$register->contact_person);
        $leaderMatric = '';
    }else if(!empty($register->contact_email)){
        $leaderName = trim((string)$register->contact_email);
        $leaderMatric = '';
    }else{
        $leaderName = 'Participant';
        $leaderMatric = '';
    }

    $leaderDisplay = $leaderName;
    if($leaderMatric !== ''){
        $leaderDisplay .= ' (' . $leaderMatric . ')';
    }
    $participantInfoHtml = preg_replace(
        '~^.*?<ul>~s',
        Html::encode($leaderDisplay) . '<ul>',
        $participantInfoHtml,
        1
    );

    $memberItems = [];
    foreach($register->members as $member){
        $memberName = trim((string)$member->member_name);
        $memberMatric = trim((string)$member->member_matric);
        if($memberName === ''){
            continue;
        }
        if($leaderMatric !== '' && $memberMatric !== '' && strcasecmp($leaderMatric, $memberMatric) === 0){
            continue;
        }
        if(strcasecmp($leaderName, $memberName) === 0){
            continue;
        }
        $memberLabel = $memberName;
        if($memberMatric !== ''){
            $memberLabel .= ' (' . $memberMatric . ')';
        }
        $memberItems[] = '<li>' . Html::encode($memberLabel) . '</li>';
    }

    if($memberItems){
        $participantMembersHtml = '<div class="participant-members-title">Members</div><ul class="participant-members-list">' . implode('', $memberItems) . '</ul>';
    }
}

if(($plain ?? false) && $program){
    $subStr = $programSub ? ' / ' . $programSub->sub_abbr : '';
    $this->params['breadcrumbs'][] = [
        'label' => $program->program_abbr . $subStr,
        'url' => ['/program-registration/manager-dashboard', 'id' => $program->id, 'sub' => $programSub ? $programSub->id : null],
    ];
    $this->params['breadcrumbs'][] = $this->title;
}

$selectedCat = null;
if($plain && $edit){
    $catId = isset($cat) ? (int)$cat : 0;
    if($rubric && $rubric->categories){
        foreach($rubric->categories as $c){
            if($catId > 0 && (int)$c->id === $catId){
                $selectedCat = $c;
                break;
            }
        }
        if($selectedCat === null){
            $selectedCat = $rubric->categories[0];
        }
    }
}

$this->registerCss(<<<CSS
.jury-judge-form .jury-category-title {
    display: block;
    margin: 1.25rem 0 0.5rem;
    letter-spacing: 0.02em;
}

.jury-judge-form .rubric-form-table > tbody > tr > td {
    vertical-align: top;
    padding-top: 1rem;
    padding-bottom: 1rem;
}

.jury-judge-form .rubric-question-no {
    width: 2.25rem;
    white-space: nowrap;
    color: #6c757d;
}

.jury-judge-form .rubric-question-text {
    font-size: 1.05rem;
    line-height: 1.45;
}

.jury-judge-form .rubric-question-desc {
    display: inline-block;
    margin-top: 0.25rem;
    font-size: 0.95rem;
    line-height: 1.45;
}

.jury-judge-form .rubric-scale {
    min-width: 0;
}

.jury-judge-form .rubric-scale-labels {
    display: flex;
    justify-content: space-between;
    gap: 0.75rem;
    margin-bottom: 0.5rem;
    color: #6c757d;
    font-size: 0.875rem;
}

.jury-judge-form .rubric-scale-options {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(3rem, 1fr));
    gap: 0.5rem;
}

.participant-members-title {
    margin-top: 0.75rem;
    font-weight: 700;
}

.participant-cell-group {
    display: inline-block;
    margin-bottom: 0.5rem;
    padding: 0.28rem 0.65rem;
    border-radius: 999px;
    font-size: 0.8rem;
    font-weight: 700;
    line-height: 1.2;
    color: #fff;
    box-shadow: inset 0 -1px 0 rgba(0, 0, 0, 0.12);
}

.participant-cell-group.c1 { background: #0d6efd; }
.participant-cell-group.c2 { background: #198754; }
.participant-cell-group.c3 { background: #6f42c1; }
.participant-cell-group.c4 { background: #d63384; }
.participant-cell-group.c5 { background: #0f766e; }
.participant-cell-group.c6 { background: #b45309; }

.participant-members-list {
    margin: 0.25rem 0 0 0.35rem;
    padding: 0;
    font-size: 0.9rem;
    line-height: 1.4;
    color: #6c757d;
    list-style-position: inside;
}

.jury-judge-form .rubric-scale-option,
.jury-judge-form .rubric-choice-option {
    position: relative;
}

.jury-judge-form .rubric-scale-option input,
.jury-judge-form .rubric-choice-option input {
    position: absolute;
    opacity: 0;
    pointer-events: none;
}

.jury-judge-form .rubric-scale-option label,
.jury-judge-form .rubric-choice-option label {
    display: flex;
    align-items: center;
    justify-content: center;
    min-height: 2.75rem;
    margin: 0;
    border: 1px solid #ced4da;
    border-radius: 0.75rem;
    background: #fff;
    color: #212529;
    font-weight: 700;
    cursor: pointer;
    transition: border-color 0.15s ease, box-shadow 0.15s ease, background-color 0.15s ease, color 0.15s ease;
}

.jury-judge-form .rubric-scale-option input:checked + label,
.jury-judge-form .rubric-choice-option input:checked + label {
    border-color: #0d6efd;
    background: #0d6efd;
    color: #fff;
    box-shadow: 0 0.35rem 0.9rem rgba(13, 110, 253, 0.22);
}

.jury-judge-form .rubric-scale-option input:focus + label,
.jury-judge-form .rubric-choice-option input:focus + label {
    outline: 2px solid rgba(13, 110, 253, 0.35);
    outline-offset: 2px;
}

.jury-judge-form .rubric-choice-options {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 10rem));
    gap: 0.5rem;
    margin-top: 0.75rem;
}

.jury-judge-form .rubric-choice-option label {
    min-height: 3rem;
}

@media (min-width: 992px) {
    .jury-judge-form .rubric-scale-options {
        display: flex;
        flex-wrap: nowrap;
    }

    .jury-judge-form .rubric-scale-option {
        flex: 1 1 0;
        min-width: 2.25rem;
    }
}

@media (max-width: 575.98px) {
    .jury-judge-form .card-body {
        padding-left: 0.875rem;
        padding-right: 0.875rem;
    }

    .jury-judge-form .rubric-form-table,
    .jury-judge-form .rubric-form-table > tbody,
    .jury-judge-form .rubric-form-table > tbody > tr,
    .jury-judge-form .rubric-form-table > tbody > tr > td {
        display: block;
        width: 100%;
    }

    .jury-judge-form .rubric-question-no {
        padding-bottom: 0;
        font-weight: 700;
    }

    .jury-judge-form .rubric-scale {
        margin-top: 0.75rem;
    }

    .jury-judge-form .rubric-scale-options {
        grid-template-columns: repeat(5, minmax(0, 1fr));
    }

    .jury-judge-form .rubric-choice-options {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }
}
CSS);
?>

<div class="pagetitle">
<h1><?=$this->title?></h1>
<?php if(!$plain && $write && !Yii::$app->user->isGuest && Yii::$app->user->identity->isJury): ?>
    <?= Html::a('<i class="bi bi-arrow-left"></i> Back to Assignment List', ['/program-registration/jury-assignment'], ['class' => 'btn btn-outline-secondary btn-sm mt-2 mb-3']) ?>
<?php endif; ?>
</div>
    <section class="section dashboard">

    <?php if($plain && !$edit){ ?>
        <div class="mb-3">
            <?= Html::a('Edit Rubric', ['program/view-rubric', 'id' => $rubric->id, 'edit' => 1], ['class' => 'btn btn-warning btn-sm']) ?>
        </div>
    <?php } ?>

    <?php if($plain && $edit){ ?>
        <div class="card">
            <div class="card-body pt-4">

                <div class="mb-4">
                    <h5>Rubric Builder</h5>
                    <div class="mb-2">
                        <?= Html::a('Exit Edit Mode', ['program/view-rubric', 'id' => $rubric->id], ['class' => 'btn btn-secondary btn-sm']) ?>
                    </div>
                </div>

                <div class="mb-4">
                    <h6>Rubric Name</h6>
                    <div class="form-control" style="background: #f8f9fa;"><?= Html::encode($rubric->rubric_name) ?></div>
                </div>

                <div class="row g-3">
                    <div class="col-md-4">
                        <div class="mb-3">
                            <h6>Categories</h6>
                            <form method="post" action="<?= Url::to(['program/rubric-category-add', 'id' => $rubric->id]) ?>">
                                <?= Html::hiddenInput(Yii::$app->request->csrfParam, Yii::$app->request->csrfToken) ?>
                                <div class="row g-2 align-items-center">
                                    <div class="col-12">
                                        <input type="text" name="category_name" class="form-control" placeholder="New category name" />
                                    </div>
                                    <div class="col-12 text-end">
                                        <button type="submit" class="btn btn-success btn-sm">Add</button>
                                    </div>
                                </div>
                            </form>
                        </div>

                        <?php if($rubric && $rubric->categories){ ?>
                            <div class="list-group" id="rubric-category-list">
                                <?php foreach($rubric->categories as $c){ ?>
                                    <?php $isActive = ($selectedCat && (int)$selectedCat->id === (int)$c->id); ?>
                                    <div class="list-group-item d-flex justify-content-between align-items-start <?= $isActive ? 'active' : '' ?>" data-id="<?= (int)$c->id ?>">
                                        <div class="me-2" style="cursor: move; user-select: none;">
                                            <span class="drag-handle">≡</span>
                                        </div>
                                        <div class="flex-grow-1">
                                            <div>
                                                <?= Html::a(Html::encode($c->category_name), ['program/view-rubric', 'id' => $rubric->id, 'edit' => 1, 'cat' => $c->id], ['class' => $isActive ? 'text-white' : '']) ?>
                                                <?php if($c->itemsRecommend){ echo ' <span class="badge bg-info">Has recommendation items</span>'; } ?>
                                            </div>
                                            <form method="post" action="<?= Url::to(['program/rubric-category-edit', 'id' => $rubric->id, 'cat' => $c->id]) ?>" class="mt-2">
                                                <?= Html::hiddenInput(Yii::$app->request->csrfParam, Yii::$app->request->csrfToken) ?>
                                                <div class="row g-2">
                                                    <div class="col-12">
                                                        <input type="text" name="category_name" class="form-control form-control-sm" value="<?= Html::encode($c->category_name) ?>" />
                                                    </div>
                                                    <div class="col-12 text-end">
                                                        <button type="submit" class="btn btn-light btn-sm">Save</button>
                                                        <?= Html::a('Del', ['program/rubric-category-delete', 'id' => $rubric->id, 'cat' => $c->id], [
                                                            'class' => 'btn btn-danger btn-sm',
                                                            'data' => [
                                                                'method' => 'post',
                                                                'confirm' => 'Delete this category and all items inside it?',
                                                            ],
                                                        ]) ?>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                <?php } ?>
                            </div>
                        <?php }else{ ?>
                            <div class="text-muted">No categories yet.</div>
                        <?php } ?>
                    </div>

                    <div class="col-md-8">
                        <h6>Items</h6>

                        <?php if($selectedCat){ ?>
                            <div class="mb-3">
                                <div class="mb-2"><b>Category Description</b></div>
                                <form method="post" action="<?= Url::to(['program/rubric-category-edit', 'id' => $rubric->id, 'cat' => $selectedCat->id]) ?>">
                                    <?= Html::hiddenInput(Yii::$app->request->csrfParam, Yii::$app->request->csrfToken) ?>
                                    <div class="mb-2">
                                        <textarea name="category_description" class="form-control rubric-category-richtext" rows="6" placeholder="Category description"><?= Html::encode(html_entity_decode((string)$selectedCat->category_description, ENT_QUOTES | ENT_HTML5, 'UTF-8')) ?></textarea>
                                    </div>
                                    <div>
                                        <button type="submit" class="btn btn-primary btn-sm">Save Description</button>
                                    </div>
                                </form>
                            </div>

                            <div class="mb-3">
                                <div class="mb-2"><b><?= Html::encode($selectedCat->category_name) ?></b></div>
                                <form method="post" action="<?= Url::to(['program/rubric-item-add', 'id' => $rubric->id, 'cat' => $selectedCat->id]) ?>">
                                    <?= Html::hiddenInput(Yii::$app->request->csrfParam, Yii::$app->request->csrfToken) ?>
                                    <div class="row g-2">
                                        <div class="col-md-6">
                                            <input type="text" name="item_text" class="form-control" placeholder="Item text" />
                                        </div>
                                        <div class="col-md-2">
                                            <select name="item_type" class="form-select" id="item_type_new">
                                                <option value="1">likert</option>
                                                <option value="2">yesno</option>
                                                <option value="3">shorttext</option>
                                                <option value="4">longtext</option>
                                            </select>
                                        </div>
                                        <div class="col-md-2">
                                            <input type="number" name="option_number" class="form-control" placeholder="Options" />
                                        </div>
                                        <div class="col-md-2">
                                            <input type="text" name="item_short" class="form-control" placeholder="Short label" />
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="is_required" value="1" id="is_required_new" checked />
                                                <label class="form-check-label" for="is_required_new">Required</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="is_recommend" value="1" id="item_is_recommend_new" />
                                                <label class="form-check-label" for="item_is_recommend_new">Recommendation item</label>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <input type="text" name="item_description" class="form-control" placeholder="Description" />
                                        </div>
                                        <div class="col-md-12">
                                            <button type="submit" class="btn btn-success btn-sm">Add Item</button>
                                        </div>
                                    </div>
                                </form>
                            </div>

                            <?php if($selectedCat->items){ ?>
                                <div class="list-group" id="rubric-item-list">
                                    <?php foreach($selectedCat->items as $item){ ?>
                                        <?php $typeList = $item->listType(); $typeText = $typeList[$item->item_type] ?? $item->item_type; ?>
                                        <div class="list-group-item" data-id="<?= (int)$item->id ?>">
                                            <div class="d-flex justify-content-between align-items-start">
                                                <div class="me-2" style="cursor: move; user-select: none;">
                                                    <span class="drag-handle">≡</span>
                                                </div>
                                                <div class="flex-grow-1">
                                                    <div><b><?= Html::encode($item->item_text) ?></b></div>
                                                    <div class="text-muted" style="font-size: 0.9em;">
                                                        Type: <?= Html::encode($typeText) ?> | Options: <?= Html::encode($item->option_number) ?> | colum_ans: <?= Html::encode($item->colum_ans) ?>
                                                        <?php if($item->isRecommendation){ echo ' | <span class="badge bg-info">Recommend</span>'; } ?>
                                                    </div>
                                                </div>
                                                <div>
                                                    <?= Html::a('Del', ['program/rubric-item-delete', 'id' => $rubric->id, 'item' => $item->id], [
                                                        'class' => 'btn btn-danger btn-sm',
                                                        'data' => [
                                                            'method' => 'post',
                                                            'confirm' => 'Delete this item?',
                                                        ],
                                                    ]) ?>
                                                </div>
                                            </div>

                                            <details class="mt-2">
                                                <summary>Edit</summary>
                                                <form method="post" action="<?= Url::to(['program/rubric-item-edit', 'id' => $rubric->id, 'item' => $item->id]) ?>" class="mt-2">
                                                    <?= Html::hiddenInput(Yii::$app->request->csrfParam, Yii::$app->request->csrfToken) ?>
                                                    <div class="row g-2">
                                                        <div class="col-md-6">
                                                            <input type="text" name="item_text" class="form-control" value="<?= Html::encode($item->item_text) ?>" />
                                                        </div>
                                                        <div class="col-md-2">
                                                            <select name="item_type" class="form-select">
                                                                <option value="1" <?= ((int)$item->item_type === 1 ? 'selected' : '') ?>>likert</option>
                                                                <option value="2" <?= ((int)$item->item_type === 2 ? 'selected' : '') ?>>yesno</option>
                                                                <option value="3" <?= ((int)$item->item_type === 3 ? 'selected' : '') ?>>shorttext</option>
                                                                <option value="4" <?= ((int)$item->item_type === 4 ? 'selected' : '') ?>>longtext</option>
                                                            </select>
                                                        </div>
                                                        <div class="col-md-2">
                                                            <input type="number" name="option_number" class="form-control" value="<?= Html::encode($item->option_number) ?>" />
                                                        </div>
                                                        <div class="col-md-2">
                                                            <input type="text" name="item_short" class="form-control" value="<?= Html::encode($item->item_short) ?>" />
                                                        </div>
                                                        <div class="col-md-12">
                                                            <div class="form-check">
                                                                <input class="form-check-input" type="checkbox" name="is_required" value="1" id="is_required_<?= (int)$item->id ?>" <?= ((int)$item->is_required === 1 ? 'checked' : '') ?> />
                                                                <label class="form-check-label" for="is_required_<?= (int)$item->id ?>">Required</label>
                                                            </div>
                                                            <div class="form-check">
                                                                <input class="form-check-input" type="checkbox" name="is_recommend" value="1" id="item_is_recommend_<?= (int)$item->id ?>" <?= ($item->isRecommendation ? 'checked' : '') ?> />
                                                                <label class="form-check-label" for="item_is_recommend_<?= (int)$item->id ?>">Recommendation item</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-12">
                                                            <input type="text" name="item_description" class="form-control" value="<?= Html::encode($item->item_description) ?>" />
                                                        </div>
                                                        <div class="col-md-12">
                                                            <button type="submit" class="btn btn-primary btn-sm">Save</button>
                                                        </div>
                                                    </div>
                                                </form>
                                            </details>
                                        </div>
                                    <?php } ?>
                                </div>
                            <?php }else{ ?>
                                <div class="text-muted">No items in this category.</div>
                            <?php } ?>
                        <?php }else{ ?>
                            <div class="text-muted">Select a category first.</div>
                        <?php } ?>
                    </div>
                </div>

                <?php
                $this->registerJsFile('https://cdn.jsdelivr.net/npm/sortablejs@1.15.2/Sortable.min.js', ['position' => \yii\web\View::POS_END]);
                $csrfParam = Yii::$app->request->csrfParam;
                $csrfToken = Yii::$app->request->csrfToken;
                $urlCatSort = Url::to(['program/rubric-category-sort', 'id' => $rubric->id]);
                $urlItemSort = $selectedCat ? Url::to(['program/rubric-item-sort', 'id' => $rubric->id, 'cat' => $selectedCat->id]) : '';
                $this->registerJs(<<<JS
if(window.tinymce){
  tinymce.remove('.rubric-category-richtext');
  tinymce.init({
    selector: '.rubric-category-richtext',
    menubar: false,
    plugins: [
      'advlist', 'autolink', 'lists', 'link', 'charmap',
      'searchreplace', 'visualblocks', 'code', 'fullscreen',
      'paste'
    ],
    toolbar: 'undo redo | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent',
    height: 200,
    branding: false
  });
}

function rubricPostOrder(url, ids){
    if(!url){ return; }
    var params = new URLSearchParams();
    ids.forEach(function(id){
        params.append('order[]', id);
    });
    params.append('{$csrfParam}', '{$csrfToken}');
    return fetch(url, {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8' },
        body: params.toString()
    });
}

function rubricSyncRequiredForNewItem(){
    var typeEl = document.getElementById('item_type_new');
    var reqEl = document.getElementById('is_required_new');
    if(!typeEl || !reqEl){ return; }
    var t = String(typeEl.value || '');
    if(t === '1' || t === '2'){
        reqEl.checked = true;
    }else if(t === '3' || t === '4'){
        reqEl.checked = false;
    }
}

rubricSyncRequiredForNewItem();
var typeEl = document.getElementById('item_type_new');
if(typeEl){
    typeEl.addEventListener('change', rubricSyncRequiredForNewItem);
}

var catEl = document.getElementById('rubric-category-list');
if(catEl && typeof Sortable !== 'undefined'){
    new Sortable(catEl, {
        animation: 150,
        handle: '.drag-handle',
        onEnd: function(){
            var ids = Array.from(catEl.querySelectorAll('[data-id]')).map(function(el){ return el.getAttribute('data-id'); });
            rubricPostOrder('{$urlCatSort}', ids);
        }
    });
}

var itemEl = document.getElementById('rubric-item-list');
if(itemEl && typeof Sortable !== 'undefined'){
    new Sortable(itemEl, {
        animation: 150,
        handle: '.drag-handle',
        onEnd: function(){
            var ids = Array.from(itemEl.querySelectorAll('[data-id]')).map(function(el){ return el.getAttribute('data-id'); });
            rubricPostOrder('{$urlItemSort}', ids);
        }
    });
}
JS);
                ?>

                <div class="mt-4">
                    <form method="post" action="<?= Url::to(['program/rubric-rearrange-columns', 'id' => $rubric->id]) ?>">
                        <?= Html::hiddenInput(Yii::$app->request->csrfParam, Yii::$app->request->csrfToken) ?>
                        <button type="submit" class="btn btn-outline-primary btn-sm" onclick="return confirm('Rearrange colum_ans according to current sorting?');">Rearrange colum_ans</button>
                    </form>
                </div>

                <div class="mt-4">
                    <h6>Import Categories & Items (CSV)</h6>
                    <div class="text-muted mb-2" style="font-size: 0.9em;">
                        CSV header must include: <b>category_name</b>, <b>item_text</b>.
                        Optional columns: <b>item_description</b>, <b>item_short</b>, <b>item_type</b> (1-4 or likert/yesno/shorttext/longtext or scale/boolean/textarea), <b>option_number</b>, <b>is_required</b> (0/1), <b>is_recommend</b> (0/1).
                        <br />Notes: you may leave <b>category_name blank</b> to continue the previous category. For <b>likert/scale</b>, <b>option_number is mandatory</b>. Category will be created if not exists. Items will be appended. <b>colum_ans is auto-generated</b>.
                    </div>
                    <div class="mb-2" style="font-family: monospace; font-size: 0.9em; white-space: pre;">category_name,item_text,item_description,item_short,item_type,option_number,is_required,is_recommend
Main Evaluation,Video ini memaparkan idea yang kreatif...,Menilai tahap kreativiti...,Creative Idea,scale,10,1,0
,Video ini mempunyai jalan cerita yang jelas...,Menilai kejelasan...,Storytelling,scale,10,1,0
KOMEN JURI,Kekuatan,Ruang untuk juri...,Strengths,textarea,,0,0</div>
                    <form method="post" enctype="multipart/form-data" action="<?= Url::to(['program/rubric-import-csv', 'id' => $rubric->id]) ?>">
                        <?= Html::hiddenInput(Yii::$app->request->csrfParam, Yii::$app->request->csrfToken) ?>
                        <div class="row g-2 align-items-center">
                            <div class="col-md-8">
                                <input type="file" name="csv_file" class="form-control" accept=".csv,text/csv" />
                            </div>
                            <div class="col-md-4">
                                <button type="submit" class="btn btn-success btn-sm" onclick="return confirm('Import CSV into this rubric?');">Import CSV</button>
                            </div>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    <?php } ?>

    <?php 
    if(!$plain){
    ?>
    <div class="card">
            <div class="card-body pt-4">

            <div class="row">
                <div class="col-md-6">
<h5>Participant Information</h5>
                <?=$groupBadgeHtml?>
                <?=$participantInfoHtml?>
                <?=$participantMembersHtml?>
                </div>
                <div class="col-md-6">

                <h5>Judging Summary</h5>
                <ul>
                  <li>Status: <?=$assign->statusLabel?></li>
                  
                  <?php 
                  list($total, $score, $percent, $award) = $model->totalScorePercent;
                  if($assign->is_nullified == 1){
                    $score = 0 . ' (nullified)';
                    $percent = 0;
                  }
                  
                  ?>
                  <li>Complete: <?=$model->isCompleteText?></li>
                  <li>Full Score: <?=$total?></li>
                  <li>Score Earned: <?=$score?></li>
                  <li>Percentage: <?=$percent?>%</li>
                  <?php if((int)$assign->is_nullified === 1): ?>
                    <li>Nullified Reason: <?= trim((string)$assign->reason_nullified) !== '' ? Html::encode($assign->reason_nullified) : '<span class="text-muted">-</span>' ?></li>
                  <?php endif; ?>
                
                   <?php 
                  if($assign->status <= 10 && $model->updated_at){
                    echo '<li>Last Update: ' . date('d M Y h:i:s', strtotime($model->updated_at)) . '</li>';
                  }else if($assign->status == 20 && $model->submitted_at){
                    echo '<li>Submitted at: ' . date('d M Y h:i:s', strtotime($model->submitted_at)) . '</li>';
                  }
                  ?>
                </ul>
                <i>(save & preview to view score)</i>
                </div>
            </div>
                
            </div>
        </div>
        <?php } ?>

<?php if($canReturnResultJudging){ ?>
    <div class="mb-3 d-flex flex-wrap gap-2">
        <?php if($backToResultListUrl): ?>
            <?= Html::a('<i class="bi bi-arrow-left"></i> Back to List', $backToResultListUrl, ['class' => 'btn btn-outline-secondary']) ?>
        <?php endif; ?>
        <?= Html::beginForm(['return-result-judging', 'id' => $assign->id], 'post') ?>
            <?= Html::submitButton('Return as Judging', [
                'class' => 'btn btn-warning',
                'data-confirm' => 'Return this completed result to Judging so the jury can edit and submit again?',
            ]) ?>
        <?= Html::endForm() ?>
        <?php if($canMarkNotNullified): ?>
            <?= Html::beginForm(['mark-result-not-nullified', 'id' => $assign->id], 'post') ?>
                <?= Html::submitButton('Mark as Not Nullified', [
                    'class' => 'btn btn-outline-success',
                    'data-confirm' => 'Mark this result as not nullified and restore its score from the rubric answers?',
                ]) ?>
            <?= Html::endForm() ?>
        <?php endif; ?>
    </div>
<?php }elseif($canMarkNotNullified){ ?>
    <div class="mb-3 d-flex flex-wrap gap-2">
        <?php if($backToResultListUrl): ?>
            <?= Html::a('<i class="bi bi-arrow-left"></i> Back to List', $backToResultListUrl, ['class' => 'btn btn-outline-secondary']) ?>
        <?php endif; ?>
        <?= Html::beginForm(['mark-result-not-nullified', 'id' => $assign->id], 'post') ?>
            <?= Html::submitButton('Mark as Not Nullified', [
                'class' => 'btn btn-outline-success',
                'data-confirm' => 'Mark this result as not nullified and restore its score from the rubric answers?',
            ]) ?>
        <?= Html::endForm() ?>
    </div>
<?php } ?>


<?php if($assign->status <= 10 || $write == false){?>
  <div class="pagetitle"><h1>Rubric Form </h1>
  (<?= Html::encode($rubric->rubric_name) ?>)
  <?php
  $rubricDesc = (string)$rubric->rubric_description;
  if(trim($rubricDesc) !== ''){
    $rubricDescHtml = html_entity_decode($rubricDesc, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    echo '<div class="mt-2 small text-muted">' . HtmlPurifier::process($rubricDescHtml) . '</div>';
  }
  ?>
</div>


    <?php $form = ActiveForm::begin(); 
    ?>
    <div id="con-form" class="jury-judge-form">
    <?php  
    $i = 1;
    if($rubric && $rubric->categories){
      foreach($rubric->categories as $cat){
        echo '<b class="jury-category-title">'.strtoupper($cat->category_name).'</b>';
        $catDesc = (string)$cat->category_description;
        if(trim($catDesc) !== ''){
          $descHtml = html_entity_decode($catDesc, ENT_QUOTES | ENT_HTML5, 'UTF-8');
          echo '<div class="mt-2 small text-muted border-start ps-2">' . HtmlPurifier::process($descHtml) . '</div>';
        }
        ?>

<div class="card">
            <div class="card-body pt-4">
        <table class="table rubric-form-table">
            <tbody>
                
                <?php 
            if($cat->items){
              
              foreach($cat->items as $item){
                if($item->item_type == 1){
                  $options = $item->option_number;
              echo '<tr><td class="rubric-question-no">'.$i.'. </td><td>
              <div class="row">
                  <div class="col-md-6 col-lg-5"><div class="rubric-question-text">'.$item->item_text.'</div>';
                
                  if($item->item_description){
                    if(strpos($item->item_description, "\n") !== FALSE) {
                      echo '<i class="rubric-question-desc">'.nl2br($item->item_description).'</i>';
                    }else {
                      echo '<i class="rubric-question-desc">('.$item->item_description.')</i>';
                    }
                    
                  }
                  
                 

                  echo '</div>
                  <div class="col-md-6 col-lg-7">
                  <div class="rubric-scale" role="radiogroup" aria-label="'.Html::encode(strip_tags($item->item_text)).'">
                  <div class="rubric-scale-labels"><span>Poor</span><span>Excellent</span></div>
                  <div class="rubric-scale-options">';

                for($x=1;$x<=$options;$x++){
                  $qn = $item->colum_ans;
                  $check = $model->$qn == $x ? 'checked' : '';
                  echo '<div class="rubric-scale-option"><input type="radio" id="r'.$item->id.'-'.$x.'" name="'.$formName.'['.$item->colum_ans.']" value="'.$x.'" '.$check.'><label for="r'.$item->id.'-'.$x.'">'.$x.'</label></div>';
                }
              echo '</div></div>';

               

                  echo '</div>
              </div>';
              if(!$write){
                echo '<br /> ** '. $item->colum_ans.' **';
              }
              echo '</td></tr>';
              $i++;
              }else if($item->item_type == 2){
                 echo '<tr><td class="rubric-question-no">'.$i.'. </td><td>
                 <div class="row">
                    <div class="col-md-8">
                        <div class="rubric-question-text">'.$item->item_text.'</div>
                        ';
                        if($item->item_description){
                          if(strpos($item->item_description, "\n") !== FALSE) {
                            echo '<i class="rubric-question-desc">'.nl2br($item->item_description).'</i>';
                          }else {
                            echo '<i class="rubric-question-desc">('.$item->item_description.')</i>';
                          }
                          
                        }

                        


                        echo '<div class="rubric-choice-options" role="radiogroup" aria-label="'.Html::encode(strip_tags($item->item_text)).'">';
                        $arr = [1=>'Yes', 2 => 'No'];
                        foreach($arr as $key => $val){
                          $qn = $item->colum_ans;
                          $check = $model->$qn == $key ? 'checked' : '';
                          echo '<div class="rubric-choice-option"><input type="radio" id="r'.$item->id.'-'.$key.'" name="'.$formName.'['.$item->colum_ans.']" value="'.$key.'" '.$check.'><label for="r'.$item->id.'-'.$key.'">'.$val.'</label></div>';
                        }
                        echo '</div>';
                        
                    echo '</div>
         
                 </div>';

                 if(!$write){
                  echo '<br /> ** '. $item->colum_ans.' **';
                }

                 echo '</td></tr>';
                 $i++;
              }else if($item->item_type == 3){ //text area
                echo '<tr><td width="10">'.$i.'. </td><td>
                 <div class="row">
                    <div class="col-md-8">
                        <label class="rubric-question-text"> '.$item->item_text.'</label>
                        ';
                    echo '<div><textarea class="form-control" name="'.$formName.'['.$item->colum_ans.']" name="">'.$model->{$item->colum_ans}.'</textarea></div>';
                    echo '</div>
         
                 </div>';

                 if(!$write){
                  echo '<br /> ** '. $item->colum_ans.' **';
                }

                 echo '</td></tr>';
                $i++;
              }else if($item->item_type == 4){ //long text
                echo '<tr><td width="10">'.$i.'. </td><td>
                 <div class="row">
                    <div class="col-md-10">
                        <label class="rubric-question-text"> '.$item->item_text.'</label>
                        ';
                    echo '<div><textarea class="form-control" rows="6" name="'.$formName.'['.$item->colum_ans.']" name="">'.$model->{$item->colum_ans}.'</textarea></div>';
                    echo '</div>
         
                 </div>';

                 if(!$write){
                  echo '<br /> ** '. $item->colum_ans.' **';
                }

                 echo '</td></tr>';
                $i++;
              }
            }
          }
          ?>
            </tbody>
        </table>
            </div></div>

<?php
      }
    }
    
    
    ?>
    
          
    </div>
        <?php if($write){
          $check = $assign->is_nullified == 1 ? 'checked' : '';
          $hide = $assign->is_nullified == 1 ? '' : 'style="display: none;"';
          ?>  
    <div class="form-group">

    <div>
    <label for="nullify" id="lbl-nullify"> 
      <input type="checkbox" name="nullify" id="nullify" value="1" <?=$check?>> <i class="bi bi-exclamation-triangle-fill text-warning"></i> Mark this participant as nullified (e.g. in case of absent, non-compliant etc.)
  </label>
  
</div><br />

    <div id="con-nullified" <?=$hide?>>
      <label>State your reason</label>
      <textarea name="reason_nullified" id="reason_nullified" class="form-control"><?=$assign->reason_nullified?></textarea>
      <br />
    </div>
  <?php

  $this->registerJs('
      $("#nullify").change(function(){
           if ($(this).prop("checked")==true){ 
              $("#con-nullified").slideDown();
          }else{
              $("#con-nullified").slideUp();
          }
      });
  ');

  ?>  
  <?=$form->field($model, 'updated_at')->hiddenInput(['value' => time()])->label(false)?>
        <?= Html::submitButton('Save & Preview', ['name' => 'action', 'value' => 'save', 'class' => 'btn btn-primary']) ?> 

        <?= Html::submitButton('Finalise & Submit', ['name' => 'action', 'value' => 'submit','class' => 'btn btn-success', 'data-confirm' => 'Are you sure to submit this form?']) ?>
            </div>
            <?php }else{
              echo '<i>This page is meant for view only. Please make sure item in stars (** __ **) are unique.</i>';
            } ?>

        
            <?php ActiveForm::end(); } ?>


          </section>
