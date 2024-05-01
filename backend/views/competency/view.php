<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model backend\models\Competency */

$this->title = \Yii::t('app', 'View Competency');;
$this->params['breadcrumbs'][] = ['label' => 'Competencies', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="competency-view">


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
    
    <br />
    
       <div class="card">
    <div class="card-body">
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'entrepreneurName',
            [
                'label' => 'Competency',
                'value' => function($model){
                    if($model->category_id == 1){
                        return 'Other ('.$model->other.')';
                    }else{
                        return $model->category->category_name;
                    }
                }
            ],
            'description',
        ],
    ]) ?>

  </div>
    </div>



</div>
