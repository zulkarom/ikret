<?php

use yii\bootstrap5\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var app\models\ProgramRegistration $model */

$this->title = 'Registration Details / Achievement';
$this->params['breadcrumbs'][] = ['label' => 'Program Registrations', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>




              <div class="pagetitle">
<h1><?=$this->title?></h1></div>

<nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="<?=Url::to(['/'])?>"><i class="bi bi-house-door"></i></a></li>
        <li class="breadcrumb-item"><a href="<?=Url::to(['/program-registration/manager', 'id' => $model->program_id, 'sub' => $model->program_sub])?>">Participants</a></li>
        <li class="breadcrumb-item active"><?=$this->title?></li>
        </ol>
        </nav>
      

              <?php 
              /* 
               <div class="card">
              <div class="card-header">Juries Assignment</div>
                      <div class="card-body pt-4">
                         <?php 
                         $juries = $model->juries;
                         $html = '';
                         if($juries){
                             $html .= '<ul>';
                             foreach($juries as $jury){
                                 $html .= $jury->infoHtml(true);
                             }
                             $html .= '<ul>';
                         }
                         echo $html;
                         
                         ?>
                      </div>
                  </div>
              */
              
              ?>
             


              <?php $arr_fields = $model->getProgramFields($model->program_id);?>

<?=$this->render('../program/_view_register', [    
'register' => $model,
'arr_fields' => $arr_fields,
'edit' => false
]);
?>