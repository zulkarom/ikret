<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\modules\frontend\models\frontend */

$this->title = $model->frontend_name;
$this->params['breadcrumbs'][] = ['label' => 'frontend', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->user_id, 'url' => ['view', 'id' => $model->user_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="frontend-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
