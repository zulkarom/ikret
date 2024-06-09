<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\Session $model */

$this->title = 'Create Session';
$this->params['breadcrumbs'][] = ['label' => 'Sessions', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="session-create">


    <div class="pagetitle">
<h1><?= Html::encode($this->title) ?></h1></div>

    </div>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
