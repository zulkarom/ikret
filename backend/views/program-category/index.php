<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\ProgramCategorySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Program Categories';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="program-category-index">

    <p>
        <?= Html::a('Create Program Category', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <br/>
    
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'category_name',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>


</div>
