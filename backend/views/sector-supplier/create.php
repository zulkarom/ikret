<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\SectorSupplier */

$this->title = 'Create Sector Supplier';
$this->params['breadcrumbs'][] = ['label' => 'Sector Suppliers', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="sector-supplier-create">


    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
