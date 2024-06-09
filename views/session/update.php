<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\Session $model */

$this->title = 'Update Session: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Sessions', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="session-update">

<div class="pagetitle">
<h1><?= Html::encode($this->title) ?></h1></div>

    </div>


    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
