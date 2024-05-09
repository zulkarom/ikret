<?php

/* @var $this yii\web\View */

use yii\helpers\Url;

$this->title = 'Programme Registered';
?>
  <div class="pagetitle">
<h1>Certificate of Participation</h1></div>

    </div><!-- End Page Title -->

    <section class="section dashboard">

    <div class="card">
            <div class="card-body">
            <div class="table-responsive">
    <table class="table">
    <tbody>
        <tr><th>No.</th><th>List of Registered Programs</th></tr>
        <?php
        if($registered){
          $i = 1;
          foreach($registered as $program){
            echo ' <tr><td>'.$i.'. </td><td>'.$program->program->program_name;
            if($program->project_name){
              echo '<br /><i>Project Title: '. $program->project_name .'</i>';
            }
            echo '<table class="table table-borderless">
                <tbody>
                    <tr><th>No. </th><th>Group Members</th><th>Matric</th><th></th></tr>';
                    $members = $program->members;
                    if($members){
                      $i = 1;
                      foreach($members as $m){
                        echo '<tr><td>'.$i.'. </td><td>'.$m->member_name.'</td><td>'.$m->member_matric.'</td><td><a href="'.Url::to(['cert-participation','reg' => $program->id, 'm' => $m->id]).'" class="btn btn-sm btn-primary" target="_blank">DOWNLOAD</a></td></tr>';
                        $i++;
                      }
                    }
                    
            echo '</tbody>
            </table>';


            echo '</td></tr>';
            $i++;
          }
        }else{
          echo '<tr><td colspan="2"><i>You have not registered to any program so far.</i></td></tr>';
        }
        
        
        ?>
       
    </tbody>
</table>
</div>
            </div>
        </div>

  
    </section>