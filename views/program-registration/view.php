<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var app\models\ProgramRegistration $model */
/** @var bool $canDelete */

$this->title = 'Registration Details';
$this->params['breadcrumbs'][] = ['label' => 'Program Registrations', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>


<div class="d-flex justify-content-center py-4">
                <a href="index.html" class="logo d-flex align-items-center w-auto">
          
                  <span class="d-none d-lg-block"><?=$this->title?></span>
                </a>
              </div><!-- End Logo -->

              <?php if($canDelete && (Yii::$app->user->identity->isAdmin || Yii::$app->user->identity->isManager)): ?>
                <div class="text-end mb-3">
                    <?= Html::a('<i class="bi bi-trash"></i> Delete Registration', ['delete', 'id' => $model->id], [
                        'class' => 'btn btn-danger btn-sm',
                        'data-confirm' => 'Are you sure to delete this registration? This action cannot be undone!',
                        'data-method' => 'post',
                    ]) ?>
                </div>
              <?php elseif(!$canDelete && (Yii::$app->user->identity->isAdmin || Yii::$app->user->identity->isManager)): ?>
                <div class="alert alert-warning">
                    This registration cannot be deleted because it has been assigned to juries.
                </div>
              <?php endif; ?>

              <?php $arr_fields = $model->getProgramFields($model->program_id);?>

<?=$this->render('../program/_view_register', [    
'register' => $model,
'arr_fields' => $arr_fields,
'edit' => false
]);
?>
