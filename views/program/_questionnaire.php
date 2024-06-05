

<?php

use kartik\form\ActiveForm;
use yii\helpers\Html;
$formName =  $model->formName();
 $form = ActiveForm::begin(); ?>
    <div class="card">
        <div class="card-body">

        <div class="table-responsive">
            <table class="table">
                <tbody>
                    <tr><th>No.</th><th width="65%">Questions</th>
                  <th colspan="5">
                    <?php 
                    if($pre_post == 1){
                      echo '1-Very Uncertain
                      2- Not convinced
                      3-Medium
                      4-Confident
                      5- Very confident';
                    }else{
                      echo '1-Very Disinterested
                      2-Not interested
                      3-Medium
                      4-Interested
                      5-Very interested';
                    }
                    ?>
                  
                  </th>
                  </tr>
                    <?php
                    $i = 1;
                    foreach($quest_likert as $q){
                      echo '<tr><td>'.$i.'. </td><td>'.$q->question_text.'</td>';
                      for($x=1;$x<=5;$x++){
                        $qn = 'q'.$q->question_number;
                        $check = $model->$qn == $x ? 'checked' : '';
                        echo '<td>';
              
                        echo '<label for="r'.$q->id.'-'.$x.'">' . $x . '</label><br /><input type="radio" id="r'.$q->id.'-'.$x.'" name="'.$formName.'[q'.$q->question_number.']" value="'.$x.'" '.$check.'>';
                        
                        echo '</td>';
                      }
                      echo '</tr>';
                      $i++;
                    }
                    ?>
                    
                </tbody>
            </table>
        </div>
            
        </div>
    </div>
    
<div class="card">
        <div class="card-body">
        <div class="table-responsive">
            <table class="table">
                <tbody>
                    <tr><th>No.</th><th>Questions</th>
 
                  </tr>

                    <?php
                   // $i = 1;
                    foreach($quest_checkbox as $q){

                      

                      echo '<tr><td>'.$i.'. </td><td>';
                      
                      echo $q->question_text;
                      echo '<br /><i>(You can choose more than one option.)</i>';
                      echo '<p>';
                      if($q->questionSubs){
                        foreach($q->questionSubs as $sub){
                          //echo '<div>'.$sub->question_text.'</div>';
                          echo $form->field($model, $sub->answer_colum)
	->checkbox(['label'=>$sub->question_text]);
                        }
                        
                      }
                      echo '</p>';



                      echo '</td>';
               
                      echo '</tr>';
                      $i++;
                    }
                    ?>
                    
                </tbody>
            </table>
        </div>
        </div>
    </div>


    <div class="form-group">
        
<?= Html::submitButton('Submit Answer', ['class' => 'btn btn-success']) ?>
    </div>
    <?php ActiveForm::end(); ?>