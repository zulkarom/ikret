<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\Sector */

$this->title = 'Update Sector';
$this->params['breadcrumbs'][] = ['label' => 'Sectors', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="sector-update">


    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
