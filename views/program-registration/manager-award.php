<?php

use yii\bootstrap5\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var app\models\ProgramRegistration $model */

$this->title = 'Update Achievement';

?>


<div class="d-flex  py-4">
                <a href="index.html" class="logo d-flex w-auto">
          
                  <span class="d-none d-lg-block"><?=$this->title?></span>
                </a>
              </div>

<?php $form = ActiveForm::begin(); ?>


               <div class="card">
              <div class="card-header">Participant Achivement</div>
                      <div class="card-body pt-4">


                      <div class="row">
    <div class="col-md-8"><?= $form->field($achieve, 'achieve_id')->dropDownList($list, ['prompt' => 'Select Achievement'])->label(false) ?></div>
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

             

