<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\EconomicCategory */

$this->title = 'Create Economic Category';
$this->params['breadcrumbs'][] = ['label' => 'Economic Categories', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="economic-category-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
