<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\SectorEntrepreneur */

$this->title = 'Create Sector Beneficiary';
$this->params['breadcrumbs'][] = ['label' => 'Sector Entrepreneurs', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="sector-entrepreneur-create">


    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
