<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var array $roles */
/** @var array $descriptions */

$this->title = 'Role List';
$this->params['breadcrumbs'][] = ['label' => 'All Users', 'url' => ['all']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="pagetitle">
    <h1><?= Html::encode($this->title) ?></h1>
</div>

</div><!-- End Page Title -->

<section class="section dashboard">
    <div class="card">
        <div class="card-body pt-4">
            <div class="table-responsive">
                <table class="table table-bordered table-striped align-middle">
                    <thead>
                        <tr>
                            <th style="width: 200px;">Role</th>
                            <th style="width: 260px;">Role Name</th>
                            <th>Description / Access</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($roles as $roleKey => $roleLabel): ?>
                            <tr>
                                <td><code><?= Html::encode($roleKey) ?></code></td>
                                <td><?= Html::encode($roleLabel) ?></td>
                                <td><?= Html::encode($descriptions[$roleKey] ?? '-') ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>
