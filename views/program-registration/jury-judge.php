<?php

use yii\bootstrap5\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var app\models\ProgramRegistration $model */

$this->title = $title;
$rubric = $assign->rubric;
$register = $assign->registration;
$formName =  $model->formName();
$edit = $edit ?? false;
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
                    <form method="post" action="<?= Url::to(['program/rubric-update-name', 'id' => $rubric->id]) ?>">
                        <?= Html::hiddenInput(Yii::$app->request->csrfParam, Yii::$app->request->csrfToken) ?>
                        <div class="row g-2 align-items-center">
                            <div class="col-md-8">
                                <input type="text" name="rubric_name" class="form-control" value="<?= Html::encode($rubric->rubric_name) ?>" />
                            </div>
                            <div class="col-md-4">
                                <button type="submit" class="btn btn-primary btn-sm">Save Name</button>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="mb-4">
                    <h6>Add Category</h6>
                    <form method="post" action="<?= Url::to(['program/rubric-category-add', 'id' => $rubric->id]) ?>">
                        <?= Html::hiddenInput(Yii::$app->request->csrfParam, Yii::$app->request->csrfToken) ?>
                        <div class="row g-2 align-items-center">
                            <div class="col-md-6">
                                <input type="text" name="category_name" class="form-control" placeholder="Category name" />
                            </div>
                            <div class="col-md-2">
                                <input type="number" name="cat_order" class="form-control" placeholder="Order" />
                            </div>
                            <div class="col-md-2">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="is_recommend" value="1" id="is_recommend_new" />
                                    <label class="form-check-label" for="is_recommend_new">Recommend</label>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-success btn-sm">Add</button>
                            </div>
                        </div>
                    </form>
                </div>

                <?php if($rubric && $rubric->categories){ ?>
                    <?php foreach($rubric->categories as $cat){ ?>
                        <div class="card mb-3">
                            <div class="card-body pt-3">
                                <div class="mb-2">
                                    <b><?= Html::encode($cat->category_name) ?></b>
                                    <?php if((int)$cat->is_recommend === 1){ echo ' <span class="badge bg-info">Recommend</span>'; } ?>
                                </div>

                                <div class="mb-3">
                                    <form method="post" action="<?= Url::to(['program/rubric-category-edit', 'id' => $rubric->id, 'cat' => $cat->id]) ?>" class="mb-2">
                                        <?= Html::hiddenInput(Yii::$app->request->csrfParam, Yii::$app->request->csrfToken) ?>
                                        <div class="row g-2 align-items-center">
                                            <div class="col-md-6">
                                                <input type="text" name="category_name" class="form-control" value="<?= Html::encode($cat->category_name) ?>" />
                                            </div>
                                            <div class="col-md-2">
                                                <input type="number" name="cat_order" class="form-control" value="<?= Html::encode($cat->cat_order) ?>" />
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="is_recommend" value="1" id="is_recommend_<?= (int)$cat->id ?>" <?= ((int)$cat->is_recommend === 1 ? 'checked' : '') ?> />
                                                    <label class="form-check-label" for="is_recommend_<?= (int)$cat->id ?>">Recommend</label>
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <button type="submit" class="btn btn-primary btn-sm">Save</button>
                                                <?= Html::a('Delete', ['program/rubric-category-delete', 'id' => $rubric->id, 'cat' => $cat->id], [
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

                                <div class="mb-3">
                                    <h6 class="mb-2">Add Item</h6>
                                    <form method="post" action="<?= Url::to(['program/rubric-item-add', 'id' => $rubric->id, 'cat' => $cat->id]) ?>">
                                        <?= Html::hiddenInput(Yii::$app->request->csrfParam, Yii::$app->request->csrfToken) ?>
                                        <div class="row g-2">
                                            <div class="col-md-6">
                                                <input type="text" name="item_text" class="form-control" placeholder="Item text" />
                                            </div>
                                            <div class="col-md-2">
                                                <select name="item_type" class="form-select">
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
                                                <input type="number" name="item_order" class="form-control" placeholder="Order" />
                                            </div>
                                            <div class="col-md-4">
                                                <input type="text" name="colum_ans" class="form-control" placeholder="colum_ans (e.g. item_no1)" />
                                            </div>
                                            <div class="col-md-4">
                                                <input type="text" name="item_short" class="form-control" placeholder="Short label" />
                                            </div>
                                            <div class="col-md-4">
                                                <input type="text" name="item_description" class="form-control" placeholder="Description" />
                                            </div>
                                            <div class="col-md-12">
                                                <button type="submit" class="btn btn-success btn-sm">Add Item</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>

                                <?php if($cat->items){ ?>
                                    <div class="table-responsive">
                                        <table class="table table-sm">
                                            <tbody>
                                                <tr>
                                                    <th>Item</th>
                                                    <th>Type</th>
                                                    <th>Options</th>
                                                    <th>Order</th>
                                                    <th>colum_ans</th>
                                                    <th></th>
                                                </tr>
                                                <?php foreach($cat->items as $item){ ?>
                                                    <tr>
                                                        <td><?= Html::encode($item->item_text) ?></td>
                                                        <td><?= Html::encode($item->item_type) ?></td>
                                                        <td><?= Html::encode($item->option_number) ?></td>
                                                        <td><?= Html::encode($item->item_order) ?></td>
                                                        <td><?= Html::encode($item->colum_ans) ?></td>
                                                        <td>
                                                            <details>
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
                                                                            <input type="number" name="item_order" class="form-control" value="<?= Html::encode($item->item_order) ?>" />
                                                                        </div>
                                                                        <div class="col-md-4">
                                                                            <input type="text" name="colum_ans" class="form-control" value="<?= Html::encode($item->colum_ans) ?>" />
                                                                        </div>
                                                                        <div class="col-md-4">
                                                                            <input type="text" name="item_short" class="form-control" value="<?= Html::encode($item->item_short) ?>" />
                                                                        </div>
                                                                        <div class="col-md-4">
                                                                            <input type="text" name="item_description" class="form-control" value="<?= Html::encode($item->item_description) ?>" />
                                                                        </div>
                                                                        <div class="col-md-12">
                                                                            <button type="submit" class="btn btn-primary btn-sm">Save</button>
                                                                            <?= Html::a('Delete', ['program/rubric-item-delete', 'id' => $rubric->id, 'item' => $item->id], [
                                                                                'class' => 'btn btn-danger btn-sm',
                                                                                'data' => [
                                                                                    'method' => 'post',
                                                                                    'confirm' => 'Delete this item?',
                                                                                ],
                                                                            ]) ?>
                                                                        </div>
                                                                    </div>
                                                                </form>
                                                            </details>
                                                        </td>
                                                    </tr>
                                                <?php } ?>
                                            </tbody>
                                        </table>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                    <?php } ?>
                <?php } ?>

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
