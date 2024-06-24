<?php

use yii\bootstrap5\ActiveForm;
use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var app\models\ProgramRegistration $model */

$this->title = $title;
$rubric = $assign->rubric;
$register = $assign->registration;
$formName =  $model->formName();
?>

<div class="pagetitle">
<h1><?=$this->title?></h1></div>
    <section class="section dashboard">

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
