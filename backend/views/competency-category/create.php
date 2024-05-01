<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\CompetencyCategory */

$this->title = 'Create Competency Category';
$this->params['breadcrumbs'][] = ['label' => 'Competency Categories', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="competency-category-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
