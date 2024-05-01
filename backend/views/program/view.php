<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model backend\models\Program */

$this->title = $model->prog_name;
$this->params['breadcrumbs'][] = ['label' => 'Programs', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);

if($model->prog_category == 1){
    $progOther;
}

?>
<div class="program-view">
    <p>
        <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <br/>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'entrepreneurName',
            'prog_name',
            [
             'label' => \Yii::t('app', 'Program Date'),
             'value' => function($model){
                return date('d M Y', strtotime($model->prog_date));
             }
            ],
            [
             'label' => \Yii::t('app', 'Category'),
             'value' => function($model){
                return $model->progCategory->category_name;
             }
            ],
            'prog_other',
            'prog_description:ntext',
            [
             'label' => \Yii::t('app', 'Organize'),
             'value' => function($model){
                return $model->anjuranText;
             }
            ],
            'anjuran_other',
        ],
    ]) ?>

</div>
