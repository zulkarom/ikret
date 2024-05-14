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


              <div class="card">
              <div class="card-header">Juries Assignment</div>
                      <div class="card-body pt-4">
                          <a href="<?=Url::to(['manager-add-jury', 'id' => $model->id])?>" class="btn btn-sm btn-outline-primary">Add Jury</a>
                      </div>
                  </div>


              <?php $arr_fields = $model->getProgramFields($model->program_id);?>

<?=$this->render('../program/_view_register', [    
'register' => $model,
'arr_fields' => $arr_fields
]);
?>