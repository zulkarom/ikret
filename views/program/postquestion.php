<?php

/* @var $this yii\web\View */

use yii\helpers\Url;

$this->title = 'Post-Event Questionnaire';
?>
  <div class="pagetitle">
<h1>Post-Event Questionnaire</h1></div>

    </div><!-- End Page Title -->

    <section class="section dashboard">

    
<div class="card">
        <div class="card-body">

        <div class="table-responsive">
            <table class="table">
            <tbody>
                    <tr><th>No.</th><th>Questions</th>
                  <?php 
                  for($x=1;$x<=5;$x++){
                    echo '<th></th>';
                  }
                  ?>
                  </tr>
                    <?php
                    $i = 1;
                    foreach($quest as $q){
                      echo '<tr><td>'.$i.'. </td><td>'.$q->question_text.'</td>';
                      for($x=1;$x<=5;$x++){
                        echo '<td><input type="radio" /></td>';
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
    



    </section>