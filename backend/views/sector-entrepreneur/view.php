<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model backend\models\SectorEntrepreneur */

$this->title = 'View Sector Beneficiary';
$this->params['breadcrumbs'][] = ['label' => 'Sector Beneficiaries', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="sector-entrepreneur-view">
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
    <br/>
<div class="white_card card_height_100 mb_30">
<div class="white_card_header">
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'entrepreneurName',
            'sectorName',
            'description:ntext',
            
        ],
    ]) ?>
</div>
</div>

</div>
