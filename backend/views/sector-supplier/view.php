<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model backend\models\SectorSupplier */

$this->title = 'View Sector Supplier';
$this->params['breadcrumbs'][] = ['label' => 'Sector Suppliers', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="sector-supplier-view">


    <p>
        <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

<br />
   <div class="card">
    <div class="card-body">
 <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'supplierName',
            'sectorName',
            'description:ntext',
            
        ],
    ]) ?>


  </div>
    </div>
   
</div>
