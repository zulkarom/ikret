<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\Agency */

$this->title = 'Update Agency';
$this->params['breadcrumbs'][] = ['label' => 'Agencies', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->nama_agensi, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="agency-update">
    
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
