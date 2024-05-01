<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\module */

$this->title = 'Update Module: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Modules', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="module-update">


    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
