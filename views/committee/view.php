<?php

use app\models\Committee;
use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var app\models\Committee $model */

$this->title = $model->com_name_en ?: 'Committee Details';
$this->params['breadcrumbs'][] = ['label' => 'Committees', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="committee-view">
    <div class="pagetitle">
        <h1><?= Html::encode($this->title) ?></h1>
    </div>

    <section class="section dashboard">
        <div class="card">
            <div class="card-body pt-4">
                <p>
                    <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
                    <?= Html::a('Delete', ['delete', 'id' => $model->id], [
                        'class' => 'btn btn-danger',
                        'data' => [
                            'confirm' => 'Are you sure you want to delete this committee?',
                            'method' => 'post',
                        ],
                    ]) ?>
                    <?= Html::a('Back', ['index'], ['class' => 'btn btn-secondary']) ?>
                </p>

                <?= DetailView::widget([
                    'model' => $model,
                    'attributes' => [
                        'id',
                        'com_name_en',
                        'com_name',
                        [
                            'attribute' => 'is_jawatankuasa',
                            'value' => Committee::yesNoOptions()[(int)$model->is_jawatankuasa] ?? 'No',
                        ],
                        [
                            'attribute' => 'is_student',
                            'value' => Committee::yesNoOptions()[(int)$model->is_student] ?? 'No',
                        ],
                        [
                            'attribute' => 'is_pengarah',
                            'value' => Committee::yesNoOptions()[(int)$model->is_pengarah] ?? 'No',
                        ],
                        [
                            'attribute' => 'can_approve',
                            'value' => Committee::yesNoOptions()[(int)$model->can_approve] ?? 'No',
                        ],
                        [
                            'attribute' => 'cert_only',
                            'value' => Committee::yesNoOptions()[(int)$model->cert_only] ?? 'No',
                        ],
                        [
                            'label' => 'Members',
                            'value' => $model->getUserRoles()->count(),
                        ],
                    ],
                ]) ?>
            </div>
        </div>
    </section>
</div>
