<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\SectorEntrepreneur */

$this->title = 'Update Sector Beneficiary';
$this->params['breadcrumbs'][] = ['label' => 'Sector Beneficiaries', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->entrepreneurName, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="sector-entrepreneur-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
