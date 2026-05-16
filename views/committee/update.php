<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\Committee $model */

$this->title = 'Update Committee: ' . ($model->com_name_en ?: $model->id);
$this->params['breadcrumbs'][] = ['label' => 'Committees', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->com_name_en ?: $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>

<div class="committee-update">
    <div class="pagetitle">
        <h1><?= Html::encode($this->title) ?></h1>
    </div>

    <section class="section dashboard">
        <div class="card">
            <div class="card-body pt-4">
                <?= $this->render('_form', [
                    'model' => $model,
                ]) ?>
            </div>
        </div>
    </section>
</div>
