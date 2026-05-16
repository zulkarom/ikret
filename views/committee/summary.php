<?php

use app\models\UserRole;
use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var array $groups */

$this->title = 'Committee Summary';
$this->params['breadcrumbs'][] = ['label' => 'Committees', 'url' => ['index']];
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
                            <th style="width: 260px;">Committee</th>
                            <th>Name</th>
                            <th style="width: 120px;">Role</th>
                            <th style="width: 120px;">Type</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(empty($groups)): ?>
                            <tr>
                                <td colspan="4" class="text-muted text-center">No committee roles found.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach($groups as $group): ?>
                                <?php
                                $committee = $group['committee'] ?? null;
                                $roles = $group['roles'] ?? [];
                                $rowspan = max(1, count($roles));
                                $committeeLabel = '-';
                                if($committee){
                                    $committeeLabel = trim((string)$committee->com_name_en);
                                    if($committeeLabel === ''){
                                        $committeeLabel = trim((string)$committee->com_name);
                                    }
                                    if($committeeLabel === ''){
                                        $committeeLabel = '-';
                                    }
                                }
                                ?>

                                <?php if(!$roles): ?>
                                    <tr>
                                        <td><?= Html::encode($committeeLabel) ?></td>
                                        <td colspan="3" class="text-muted">No members</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach($roles as $idx => $role): ?>
                                        <tr>
                                            <?php if($idx === 0): ?>
                                                <td rowspan="<?= (int)$rowspan ?>" class="fw-semibold"><?= Html::encode($committeeLabel) ?></td>
                                            <?php endif; ?>

                                            <td><?= Html::encode($role->user ? $role->user->fullname : '-') ?></td>
                                            <td>
                                                <?= Html::encode(((int)$role->is_leader === 1) ? 'Leader' : 'Member') ?>
                                            </td>
                                            <td>
                                                <?php
                                                if($committee){
                                                    if((int)$committee->is_student === 1){
                                                        echo 'Student';
                                                    }else{
                                                        echo 'Staff';
                                                    }
                                                }else{
                                                    echo '-';
                                                }
                                                ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>
