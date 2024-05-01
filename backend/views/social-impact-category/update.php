<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\SocialImpactCategory */

$this->title = 'Update Social Impact Category';
$this->params['breadcrumbs'][] = ['label' => 'Social Impact Categories', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="social-impact-category-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
