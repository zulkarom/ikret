<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\EconomicCategory */

$this->title = 'Update Economic Category';
$this->params['breadcrumbs'][] = ['label' => 'Economic Categories', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="economic-category-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
