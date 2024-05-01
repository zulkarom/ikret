<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\ProgramCategory */

$this->title = 'Create Program Category';
$this->params['breadcrumbs'][] = ['label' => 'Program Categories', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="program-category-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
