<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\Competency */

$this->title = 'Create Competency';
$this->params['breadcrumbs'][] = ['label' => 'Competencies', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

   <div class="card">
    <div class="card-body">
<div class="competency-create">


    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>

  </div>
    </div>

