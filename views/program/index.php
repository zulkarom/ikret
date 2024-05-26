<?php

/* @var $this yii\web\View */

use yii\helpers\Url;

$this->title = 'Programme Registered';
?>
  <div class="pagetitle">
<h1>List of Programs</h1></div>

    </div><!-- End Page Title -->

    <section class="section dashboard">

    <div class="card">
            <div class="card-body">
            <div class="table-responsive">
    <table class="table">
    <tbody>
        <tr><th>No.</th><th>List of Registered Programs</th><th>Status</th><th></th></tr>
        <?php
        if($registered){
          $i = 1;
          foreach($registered as $program){
            echo ' <tr><td>'.$i.'. </td><td>'.$program->program->program_name;
            if($program->project_name){
              echo '<br /><i>Project Title: '. $program->project_name .'</i>';
            }
            
            echo '</td><td>'.$program->statusLabel.'</td>
            <td><a href="'.Url::to(['program/register-form', 'id' => $program->program->id, 'reg' => $program->id]).'" class="btn btn-warning btn-sm">View</a></td></tr>';
            $i++;
          }
        }else{
          echo '<tr><td colspan="4"><i>You have not registered to any program so far.</i></td></tr>';
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
        <tr><th>No.</th><th>List of Available Programs</th><th></th></tr>
        <?php 
        $i = 1;
        foreach($programs as $program){
          echo ' <tr><td>'.$i.'. </td><td>'.$program->program_name.'</td><td><a href="'.Url::to(['program/register-form', 'id' => $program->id]).'" class="btn btn-primary btn-sm">Register</a></td></tr>';
          $i++;
        }
        
        ?>
       
    </tbody>
</table>
</div>
            </div>
        </div>



    </section>