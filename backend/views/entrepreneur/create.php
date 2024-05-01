<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\Entrepreneur */

$this->title = \Yii::t('app', 'Add Beneficiary');
$this->params['breadcrumbs'][] = ['label' => 'Beneficiaries', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="entrepreneur-create">

    <?= $this->render('_form', [
        'model' => $model,
        'modelUser' => $modelUser,
    ]) ?>

</div>
