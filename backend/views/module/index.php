<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\moduleSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Module';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="module-index">

    <p>
        <?= Html::a('Create module', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'module_name',
            'kategori_id',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
