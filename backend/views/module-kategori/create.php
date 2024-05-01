<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\moduleKategori */

$this->title = 'Create Module Category';
$this->params['breadcrumbs'][] = ['label' => 'Module Program', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="module-kategori-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
