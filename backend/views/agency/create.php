<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\Agency */

$this->title = 'Create Agency';
$this->params['breadcrumbs'][] = ['label' => 'Agencies', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="card">
    <div class="card-body">
<div class="agency-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
</div>
</div>
