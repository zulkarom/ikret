<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\User $model */
/** @var array $deleteBlockers */

$this->title = 'UPDATE: ' . $model->fullname;
$this->params['breadcrumbs'][] = ['label' => 'Program Registrations', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>


<div class="pagetitle">
<h1><?=$this->title?></h1></div>

    </div><!-- End Page Title -->

    <section class="section dashboard">

    <div class="card">
            <div class="card-body pt-4">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

    <hr />

    <?php if(!empty($deleteBlockers)): ?>
        <div class="alert alert-warning">
            User cannot be deleted because related records exist:
            <?= Html::encode(implode(', ', $deleteBlockers)) ?>
        </div>
        <?= Html::button('<i class="bi bi-trash"></i> Delete User', [
            'class' => 'btn btn-danger',
            'disabled' => true,
        ]) ?>
    <?php else: ?>
        <?= Html::a('<i class="bi bi-trash"></i> Delete User', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data-confirm' => 'Are you sure to delete this user? This action cannot be undone.',
            'data-method' => 'post',
        ]) ?>
    <?php endif; ?>

</div>
            </div>
        </div>



    </section>
