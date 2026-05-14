<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var array $rows */

$this->title = 'Achievement Summary';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="pagetitle">
    <h1><?= Html::encode($this->title) ?></h1>
</div>

<section class="section dashboard">
    <div class="card">
        <div class="card-header">Achievements & Winners</div>
        <div class="card-body pt-4">
            <?php if($rows): ?>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th style="min-width: 220px;">Program/Sub</th>
                                <th style="min-width: 420px;">Achievement Name</th>
                                <th style="width: 180px;">Number of Winner</th>
                                <th style="min-width: 380px;">Awarded Participants</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($rows as $row): ?>
                                <tr>
                                    <td><?= Html::encode($row['program']) ?></td>
                                    <td><?= Html::encode($row['name']) ?></td>
                                    <td>
                                        <span class="badge bg-primary"><?= (int)$row['assigned_count'] ?></span>
                                        <span class="text-muted ms-2">/ <?= (int)$row['winner_count'] ?></span>
                                    </td>
                                    <td>
                                        <?php if(!empty($row['participants'])): ?>
                                            <?= implode('<br />', $row['participants']) ?>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <span class="text-muted">No achievements configured.</span>
            <?php endif; ?>
        </div>
    </div>
</section>
