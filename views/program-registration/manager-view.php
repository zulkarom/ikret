<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var app\models\ProgramRegistration $model */

$this->title = 'Registration Details / Juries';
$this->params['breadcrumbs'][] = ['label' => 'Program Registrations', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>


<div class="d-flex justify-content-center py-4">
                <a href="index.html" class="logo d-flex align-items-center w-auto">
          
                  <span class="d-none d-lg-block"><?=$this->title?></span>
                </a>
              </div>

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
'arr_fields' => $arr_fields
]);
?>