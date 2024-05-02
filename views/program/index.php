<?php

/* @var $this yii\web\View */

use yii\helpers\Url;

$this->title = 'Programme Registered';
?>
  <div class="pagetitle">
<h1>Registered Programs</h1></div>

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
            echo ' <tr><td>'.$i.'. </td><td>'.$program->program->program_name.'<br />
            <i>Project Title: '. $program->project_name .'</i>
            </td><td>'.$program->statusLabel.'</td>
            <td><a href="'.Url::to(['program/view-register', 'id' => $program->program->id, 'reg' => $program->id]).'" class="btn btn-warning btn-sm">View</a></td></tr>';
            $i++;
          }
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
        <tr><th>No.</th><th>List of Available Events/Programmes</th><th></th></tr>
        <?php 
        $i = 1;
        foreach($programs as $program){
          echo ' <tr><td>'.$i.'. </td><td>'.$program->program_name.'</td><td><a href="'.Url::to(['program/register', 'id' => $program->id]).'" class="btn btn-primary btn-sm">Register</a></td></tr>';
          $i++;
        }
        
        ?>
       
    </tbody>
</table>
</div>
            </div>
        </div>



    </section>