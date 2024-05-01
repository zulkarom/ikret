<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\Competency */

$this->title = 'Update Competency';
$this->params['breadcrumbs'][] = ['label' => 'Competencies', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->entrepreneurName, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="competency-update">


    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
