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
?>

<div class="pagetitle">
<h1><?=$this->title?></h1></div>
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
                                    <div class="col-8">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="is_recommend" value="1" id="is_recommend_new" />
                                            <label class="form-check-label" for="is_recommend_new">Recommend</label>
                                        </div>
                                    </div>
                                    <div class="col-4 text-end">
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
                                                <?php if((int)$c->is_recommend === 1){ echo ' <span class="badge bg-info">Recommend</span>'; } ?>
                                            </div>
                                            <form method="post" action="<?= Url::to(['program/rubric-category-edit', 'id' => $rubric->id, 'cat' => $c->id]) ?>" class="mt-2">
                                                <?= Html::hiddenInput(Yii::$app->request->csrfParam, Yii::$app->request->csrfToken) ?>
                                                <div class="row g-2">
                                                    <div class="col-12">
                                                        <input type="text" name="category_name" class="form-control form-control-sm" value="<?= Html::encode($c->category_name) ?>" />
                                                    </div>
                                                    <div class="col-7">
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox" name="is_recommend" value="1" id="is_recommend_<?= (int)$c->id ?>" <?= ((int)$c->is_recommend === 1 ? 'checked' : '') ?> />
                                                            <label class="form-check-label" for="is_recommend_<?= (int)$c->id ?>">Recommend</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-5 text-end">
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
                <?=$register->shortFieldsHtml?>
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
                
                   <?php 
                  if($assign->status <= 10 && $model->updated_at){
                    echo '<li>Last Update: ' . date('d M Y h:i:s', strtotime($model->updated_at)) . '</li>';
                  }else if($assign->status == 20 && $model->submitted_at){
                    echo '<li>Submitted at: ' . date('d M Y h:i:s', strtotime($model->submitted_at)) . '</li>';
                  }
                  ?>
                </ul>
                <i>(save & preview to view score)<br />
                * final score & award depend on average results from all juries.</i>
                <br />GOLD:	80 - 100 | SILVER:	60 - 79 | BRONZE:	0 - 59
                </div>
            </div>
                
            </div>
        </div>
        <?php } ?>


<?php if($assign->status <= 10 || $write == false){?>
  <div class="pagetitle"><h1>Rubric Form </h1>
  (<?=$rubric->rubric_name?>)
</div>


    <?php $form = ActiveForm::begin(); 
    $hide_form = $assign->is_nullified == 1 ? 'style="display: none;"' : '';
    ?>
    <div id="con-form" <?=$hide_form?>>
    <?php  
    $i = 1;
    if($rubric && $rubric->categories){
      foreach($rubric->categories as $cat){
        echo '<b>'.strtoupper($cat->category_name).'</b>';
        $catDesc = (string)$cat->category_description;
        if(trim($catDesc) !== ''){
          $descHtml = html_entity_decode($catDesc, ENT_QUOTES | ENT_HTML5, 'UTF-8');
          echo '<div class="mt-2 small text-muted border-start ps-2">' . HtmlPurifier::process($descHtml) . '</div>';
        }
        ?>

<div class="card">
            <div class="card-body pt-4">
        <table class="table">
            <tbody>
                
                <?php 
            if($cat->items){
              
              foreach($cat->items as $item){
                if($item->item_type == 1){
                  $options = $item->option_number;
              echo '<tr><td width="10">'.$i.'. </td><td>
              <div class="row">
                  <div class="col-md-6">'.$item->item_text.'<br />';
                
                  if($item->item_description){
                    if(strpos($item->item_description, "\n") !== FALSE) {
                      echo '<i style="font-size:14px">'.nl2br($item->item_description).'</i>';
                    }else {
                      echo '<i style="font-size:14px">('.$item->item_description.')</i>';
                    }
                    
                  }
                  
                 

                  echo '</div>
                  <div class="col-md-6">
                  <table border="0" cellpadding="7">';

              echo '<tr><td></td>';
                  for($x=1;$x<=$options;$x++){
                    echo '<td>'.$x.'</td>';
                  }
                echo '<td></td></tr>';

                echo '<tr><td>Poor</td>';
                for($x=1;$x<=$options;$x++){
                  $qn = $item->colum_ans;
                  $check = $model->$qn == $x ? 'checked' : '';
                  echo '<td><input type="radio" style="cursor:pointer;" id="r'.$item->id.'-'.$x.'" name="'.$formName.'['.$item->colum_ans.']" value="'.$x.'" '.$check.'></td>';
                }
              echo '<td>Excellent</td></tr>';
              
                echo '</table>';

               

                  echo '</div>
              </div>';
              if(!$write){
                echo '<br /> ** '. $item->colum_ans.' **';
              }
              echo '</td></tr>';
              $i++;
              }else if($item->item_type == 2){
                 echo '<tr><td width="10">'.$i.'. </td><td>
                 <div class="row">
                    <div class="col-md-8">
                        <div> '.$item->item_text.'</div>
                        ';
                        if($item->item_description){
                          if(strpos($item->item_description, "\n") !== FALSE) {
                            echo '<i style="font-size:14px">'.nl2br($item->item_description).'</i>';
                          }else {
                            echo '<i style="font-size:14px">('.$item->item_description.')</i>';
                          }
                          
                        }

                        


                        $arr = [1=>'Yes', 2 => 'No'];
                        foreach($arr as $key => $val){
                          $qn = $item->colum_ans;
                          $check = $model->$qn == $key ? 'checked' : '';
                          echo '<div class="form-group"><label style="cursor:pointer;" for="r'.$item->id.'-'.$key.'"><input type="radio" style="cursor:pointer;" id="r'.$item->id.'-'.$key.'" name="'.$formName.'['.$item->colum_ans.']" value="'.$key.'" '.$check.'> '.$val.'</label></div>';
                        }
                        
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
                        <label> '.$item->item_text.'</label>
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
                        <label> '.$item->item_text.'</label>
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
      <input type="checkbox" name="nullify" id="nullify" value="1" <?=$check?>> Mark this participant as nullified (e.g. in case of absent, non-compliant etc.)
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
              $("#con-form").hide();
          }else{
            $("#con-form").show();
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
