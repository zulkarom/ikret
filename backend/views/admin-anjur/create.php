<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\AdminAnjur */

$this->title = 'Create Admin Anjur';
$this->params['breadcrumbs'][] = ['label' => 'Admin Anjur', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="admin-anjur-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
