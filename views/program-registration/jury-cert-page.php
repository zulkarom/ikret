<?php

use app\models\Program;
use app\models\ProgramRegistration;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;

/** @var yii\web\View $this */
/** @var app\models\ProgramRegistrationSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Jury Certificate';
?>
  <div class="pagetitle">
<h1><?=$this->title?></h1></div>

    </div><!-- End Page Title -->

    <section class="section dashboard">

    <div class="card">
            <div class="card-body pt-4">
      <table class="table">
                      <tbody>
                          <tr><th>No.</th><th>Program</th><th></th></tr>
                          <?php 
    
    if($programs){
      $i = 1;
      foreach($programs as $j){
        echo '<tr><td>'.$i.'</td><td>' . $j->registration->programNameShort . '</td><td><a href="'. Url::to(['jury-cert-pdf', 'p' => $j->program_id, 's' =>$j->program_sub, 'u' => $user->id]) .'" target="_blank" class="btn btn-primary btn-sm">Certificate</a></td></tr>';
        $i++;
      }
    }
    
    
    ?>
                      </tbody>
                  </table>
            
            </div>
        </div>



    </section>
