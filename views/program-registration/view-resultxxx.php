<?php

use yii\bootstrap5\ActiveForm;
use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var app\models\ProgramRegistration $model */

$this->title = 'View Result';

$rubric = $assign->rubric;
$register = $assign->registration;
$formName =  $model->formName();
?>

<div class="pagetitle">
<h1><?=$this->title?></h1></div>
    <section class="section dashboard">

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
                  
                  <?php list($total, $score, $percent, $award) = $model->totalScorePercent?>
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

  <div class="pagetitle"><h1>Rubric Form </h1>
  (<?=$rubric->rubric_name?>)
</div>


    <?php $form = ActiveForm::begin(); ?>
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
                    echo '<i style="font-size:13px">('.$item->item_description.')</i>';
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
                  echo '<td><input type="radio" style="cursor:pointer;" id="r'.$item->id.'-'.$x.'" name="'.$formName.'['.$item->colum_ans.']" value="'.$x.'" '.$check.' disabled></td>';
                }
              echo '<td>Excellent</td></tr>';
              
                echo '</table>
                  </div>
              </div>
              
              </td></tr>';
              $i++;
              }else if($item->item_type == 2){
                 echo '<tr><td width="10">'.$i.'. </td><td>
                 <div class="row">
                    <div class="col-md-8">
                        <div> '.$item->item_text.'</div>
                        ';
                        $arr = [1=>'Yes', 2 => 'No'];
                        foreach($arr as $key => $val){
                          $qn = $item->colum_ans;
                          $check = $model->$qn == $key ? 'checked' : '';
                          echo '<div class="form-group"><label style="cursor:pointer;" for="r'.$item->id.'-'.$key.'"><input type="radio" style="cursor:pointer;" id="r'.$item->id.'-'.$key.'" name="'.$formName.'['.$item->colum_ans.']" value="'.$key.'" '.$check.' disabled> '.$val.'</label></div>';
                        }
                    echo '</div>
         
                 </div>
                 </td></tr>';
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
    
          
          

        
            <?php ActiveForm::end(); ?>


          </section>
