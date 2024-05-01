<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\moduleKategori */

$this->title = 'Update Module Category';
$this->params['breadcrumbs'][] = ['label' => 'Module Program', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="module-kategori-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
