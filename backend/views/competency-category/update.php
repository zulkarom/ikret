<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\CompetencyCategory */

$this->title = 'Update Competency Category';
$this->params['breadcrumbs'][] = ['label' => 'Competency Categories', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="competency-category-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
