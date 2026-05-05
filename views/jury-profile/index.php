<?php

use yii\grid\GridView;
use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\JuryProfileSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Jury Profiles';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="pagetitle">
    <h1><?= Html::encode($this->title) ?></h1>
</div>

</div><!-- End Page Title -->

<section class="section">
    <div class="mb-3">
        <?= Html::a('Import CSV', ['import'], ['class' => 'btn btn-primary']) ?>
    </div>

    <div class="card">
        <div class="card-body pt-4">
            <div class="table-responsive">
                <?= GridView::widget([
                    'dataProvider' => $dataProvider,
                    'filterModel' => $searchModel,
                    'columns' => [
                        ['class' => 'yii\\grid\\SerialColumn'],
                        [
                            'attribute' => 'user_id',
                            'label' => 'User ID',
                        ],
                        [
                            'attribute' => 'fullname',
                        ],
                        [
                            'attribute' => 'category',
                        ],
                        [
                            'attribute' => 'phone',
                        ],
                        [
                            'attribute' => 'institution',
                        ],
                        [
                            'attribute' => 'designation',
                        ],
                    ],
                ]); ?>
            </div>
        </div>
    </div>
</section>
