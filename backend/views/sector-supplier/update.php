<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\SectorSupplier */

$this->title = 'Update Sector Supplier';
$this->params['breadcrumbs'][] = ['label' => 'Sector Suppliers', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->supplierName, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="sector-supplier-update">


    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
