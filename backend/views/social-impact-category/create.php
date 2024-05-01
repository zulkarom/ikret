<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\SocialImpactCategory */

$this->title = 'Create Social Impact Category';
$this->params['breadcrumbs'][] = ['label' => 'Social Impact Categories', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="social-impact-category-create">
    
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
