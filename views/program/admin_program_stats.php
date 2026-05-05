<?php

use yii\helpers\Html;

$this->title = 'Program Stats';

$rows = isset($rows) && is_array($rows) ? $rows : [];

?>
<div class="pagetitle">
<h1><?=$this->title?></h1></div>

</div><!-- End Page Title -->

<section class="section dashboard">

<div class="card">
    <div class="card-body pt-4">
        <div class="table-responsive">
            <table class="table">
                <tbody>
                    <tr>
                        <th style="width: 40px;">No.</th>
                        <th>Program / Sub</th>
                        <th style="width: 180px;">Group Count</th>
                        <th style="width: 180px;">Participant Count</th>
                        <th style="width: 180px;">Rubrics Sessions Count</th>
                    </tr>
                    <?php $i = 1; foreach($rows as $row){ ?>
                        <tr>
                            <td><?=$i?>.</td>
                            <td><?= Html::encode($row['label']) ?></td>
                            <td><?= (int)($row['group_count'] ?? 0) ?></td>
                            <td><?= (int)$row['participant_count'] ?></td>
                            <td><?= (int)$row['session_count'] ?></td>
                        </tr>
                    <?php $i++; } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

</section>
