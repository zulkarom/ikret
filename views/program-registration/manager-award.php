<?php

use yii\bootstrap5\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var app\models\ProgramRegistration $model */

$this->title = 'Achievement Award';

?>

<div class="pagetitle">
<h1><?=$this->title?></h1></div>

<nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="<?=Url::to(['/'])?>"><i class="bi bi-house-door"></i></a></li>
        <li class="breadcrumb-item"><a href="<?=Url::to(['/program-registration/manager-analysis', 'id' => $achieve->registration->program_id, 'sub' => $achieve->registration->program_sub])?>">Analysis & Achievement</a></li>
        <li class="breadcrumb-item active"><?=$this->title?></li>
        </ol>
        </nav>

    </div><!-- End Page Title -->


    <?php $form = ActiveForm::begin(); ?>


<div class="card">
<div class="card-header">MEDAL AWARD FOR <?= $achieve->registration->participantText?></div>
       <div class="card-body pt-4">


       <div class="row">
<div class="col-md-8"><?= $form->field($model, 'award')->dropDownList($model->listAward(), ['prompt' => 'No Award'])->label(false) ?></div>
<div class="col-md-4"><?= Html::submitButton('<i class="bi bi-shield-plus"></i> Change', ['class' => 'btn btn-primary btn-sm']) ?></div>
</div>

       </div>
   </div>
   <?php ActiveForm::end(); ?>

<?php $form = ActiveForm::begin(); ?>


               <div class="card">
              <div class="card-header">EXCELLENCE AWARD FOR <?= $achieve->registration->participantText?></div>
                      <div class="card-body pt-4">


                      <div class="row">
    <div class="col-md-8"><?= $form->field($achieve, 'achieve_id')->dropDownList($list, ['prompt' => 'Select Award'])->label(false) ?></div>
    <div class="col-md-4"><?= Html::submitButton('<i class="bi bi-shield-plus"></i> Add Achievement', ['class' => 'btn btn-primary btn-sm']) ?></div>
</div>


            <table class="table">
              <tbody>
                  <tr><th>No.</th><th>Achievement</th><th></th></tr>
                  <?php 
                  if($model->achievements){
                      $i=1;
                      foreach($model->achievements as $r){
                          echo '<tr><td>'.$i.'. </td><td>'.$r->achieve->name.'</td><td>'. Html::a('Remove', ['achieve-delete', 'id' => $r->id], ['class' => 'btn btn-outline-danger btn-sm']) .'</td></tr>';
                          $i++;
                      }
                  }
                  ?> 
              </tbody>
          </table>
                      </div>
                  </div>
                  <?php ActiveForm::end(); ?>

             

