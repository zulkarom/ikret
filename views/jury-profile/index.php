<?php

use yii\grid\GridView;
use yii\helpers\Html;
use app\models\JuryAssign;

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
        <?= Html::a('Create Jury Manually', ['create'], ['class' => 'btn btn-success']) ?>
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
                            'format' => 'html',
                            'value' => function($model){
                                $email = $model->user ? $model->user->email : '';
                                return Html::encode($model->fullname) . '<br />' . Html::encode($email);
                            }
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
                        [
                            'label' => 'Action',
                            'format' => 'html',
                            'value' => function($model){
                                $hasAssignment = JuryAssign::find()->where(['user_id' => $model->user_id])->exists();
                                if($hasAssignment){
                                    return '<span class="badge bg-warning">Assigned</span>';
                                }

                                return Html::a('<i class="bi bi-trash"></i>', ['delete', 'id' => $model->id], [
                                    'class' => 'btn btn-danger btn-sm',
                                    'data-confirm' => 'Delete this jury profile? This will also remove jury applications and jury role access for this user.',
                                    'title' => 'Delete',
                                ]);
                            }
                        ],
                    ],
                ]); ?>
            </div>
        </div>
    </div>
</section>
