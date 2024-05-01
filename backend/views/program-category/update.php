<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\ProgramCategory */

$this->title = 'Update Program Category';
$this->params['breadcrumbs'][] = ['label' => 'Program Categories', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="program-category-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
