<?php

use yii\bootstrap5\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var app\models\ProgramRegistration $model */
/** @var bool $hasWinnerTitleSelection */
/** @var array $winnerTitlesByAchievement */

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

               <div class="card">
              <div class="card-header">EXCELLENCE AWARD FOR <?= $achieve->registration->participantText?></div>
                      <div class="card-body pt-4">

<?php $form = ActiveForm::begin(); ?>
                      <div class="row">
                        <?= Html::hiddenInput('action_type', 'achievement-add') ?>
                        <div class="col-md-8"><?= $form->field($achieve, 'achieve_id')->dropDownList($list, ['prompt' => 'Select Award'])->label(false) ?></div>
                        <div class="col-md-4"><?= Html::submitButton('<i class="bi bi-shield-plus"></i> Add Achievement', ['class' => 'btn btn-primary btn-sm']) ?></div>
                    </div>
<?php ActiveForm::end(); ?>

<?php $form = ActiveForm::begin(); ?>
<?= Html::hiddenInput('action_type', 'achievement-title-update') ?>
            <table class="table">
              <tbody>
                  <tr><th>No.</th><th>Achievement</th><?php if($hasWinnerTitleSelection): ?><th>Winner's Title</th><?php endif; ?><th></th></tr>
                  <?php 
                  if($model->achievements){
                      $i=1;
                      foreach($model->achievements as $r){
                          echo '<tr><td>'.$i.'. </td><td>'.$r->achieve->name.'</td>';
                          if($hasWinnerTitleSelection){
                              echo '<td>';
                              $winnerTitles = $winnerTitlesByAchievement[$r->achieve_id] ?? [];
                              if($winnerTitles){
                                  echo '<div class="d-flex flex-column gap-1">';
                                  echo Html::radio('achievement_winner_title[' . $r->id . ']', !$r->winner_title_id, [
                                      'value' => 0,
                                      'label' => 'not applicable',
                                  ]);
                                  foreach($winnerTitles as $winnerTitle){
                                      $label = trim((string)$winnerTitle->title_name);
                                      if($label === ''){
                                          $label = 'Winner ' . $winnerTitle->winner_order . ' (no title)';
                                      }
                                      echo Html::radio('achievement_winner_title[' . $r->id . ']', (int)$r->winner_title_id === (int)$winnerTitle->id, [
                                          'value' => $winnerTitle->id,
                                          'label' => $label,
                                      ]);
                                  }
                                  echo '</div>';
                              }else{
                                  echo Html::tag('span', 'No winner title configured.', ['class' => 'text-muted']);
                              }
                              echo '</td>';
                          }
                          echo '<td>'. Html::a('Remove', ['achieve-delete', 'id' => $r->id], ['class' => 'btn btn-outline-danger btn-sm']) .'</td></tr>';
                          $i++;
                      }
                  }else{
                      echo '<tr><td colspan="' . ($hasWinnerTitleSelection ? 4 : 3) . '" class="text-muted">No achievement awarded yet.</td></tr>';
                  }
                  ?> 
              </tbody>
          </table>
          <?php if($model->achievements && $hasWinnerTitleSelection): ?>
              <?= Html::submitButton('<i class="bi bi-check2-circle"></i> Save Winner Title', ['class' => 'btn btn-primary btn-sm']) ?>
          <?php elseif($model->achievements && !$hasWinnerTitleSelection): ?>
              <div class="alert alert-warning mb-0">
                  Winner title selection needs <code>db/2026-05-10_update_program_reg_achieve_add_winner_title.sql</code> and the achievement-based winner title table.
              </div>
          <?php endif; ?>
                  <?php ActiveForm::end(); ?>
                      </div>
                  </div>

             
