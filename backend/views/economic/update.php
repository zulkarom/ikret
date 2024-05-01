<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\Economic */

$this->title = 'Update Economic';
$this->params['breadcrumbs'][] = ['label' => 'Economic Beneficiaries', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->entrepreneurName, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="economic-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
