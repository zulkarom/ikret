<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\User $model */
/** @var array $assignments */
/** @var array $existingKeys */

$this->title = 'Assign Manager: ' . $model->fullname;
$this->params['breadcrumbs'][] = ['label' => 'All Users', 'url' => ['all']];
$this->params['breadcrumbs'][] = ['label' => $model->fullname, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Assign Manager';
?>

<div class="pagetitle">
<h1><?= Html::encode($this->title) ?></h1></div>

    </div><!-- End Page Title -->

    <section class="section dashboard">

    <div class="card">
            <div class="card-body pt-4">

    <p>
        <?= Html::a('Back to User', ['view', 'id' => $model->id], ['class' => 'btn btn-secondary']) ?>
    </p>

    <?php if(empty($assignments)): ?>
        <div class="alert alert-warning">No available program found.</div>
    <?php else: ?>
        <?= Html::beginForm(['assign-manager', 'id' => $model->id], 'post') ?>

        <div class="table-responsive">
            <table class="table table-bordered table-sm">
                <thead>
                    <tr>
                        <th style="width: 60px;">Assign</th>
                        <th>Program</th>
                        <th>Competition</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($assignments as $program): ?>
                        <?php if(empty($program['items'])): ?>
                            <tr>
                                <td></td>
                                <td><?= Html::encode($program['name']) ?></td>
                                <td></td>
                                <td><span class="badge bg-warning">No active competition</span></td>
                            </tr>
                        <?php endif; ?>

                        <?php foreach($program['items'] as $item): ?>
                            <?php $exists = array_key_exists($item['key'], $existingKeys); ?>
                            <tr>
                                <td class="text-center">
                                    <?php if($exists): ?>
                                        <input type="checkbox" class="form-check-input" checked disabled>
                                    <?php else: ?>
                                        <input type="checkbox" class="form-check-input" name="assignments[]" value="<?= Html::encode($item['key']) ?>">
                                    <?php endif; ?>
                                </td>
                                <td><?= Html::encode($program['name']) ?></td>
                                <td><?= Html::encode($item['label']) ?></td>
                                <td>
                                    <?php if($exists): ?>
                                        <span class="badge bg-success">Already assigned</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">Available</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="form-group mt-3">
            <?= Html::submitButton('Assign Selected', [
                'class' => 'btn btn-success',
                'data-confirm' => 'Assign selected manager access to this user?',
            ]) ?>
        </div>

        <?= Html::endForm() ?>
    <?php endif; ?>

</div>
            </div>
        </div>

    </section>
