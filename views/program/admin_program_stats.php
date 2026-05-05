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
                        <th style="width: 180px;">Rubric Count</th>
                        <th style="width: 180px;">Rubrics Sessions Count</th>
                    </tr>
                    <?php $i = 1; foreach($rows as $row){ ?>
                        <tr>
                            <td><?=$i?>.</td>
                            <?php
                            $program = $row['program'] ?? null;
                            $sub = $row['sub'] ?? null;
                            $pid = $program ? (int)$program->id : null;
                            $sid = $sub ? (int)$sub->id : null;
                            ?>
                            <td>
                                <?php
                                $label = Html::encode($row['label']);
                                if($pid){
                                    echo Html::a($label, ['/program-registration/manager-dashboard', 'id' => $pid, 'sub' => $sid]);
                                }else{
                                    echo $label;
                                }
                                ?>
                            </td>
                            <td>
                                <?= $pid ? Html::a((string)(int)($row['group_count'] ?? 0), ['/program-registration/index', 'ProgramRegistrationSearch' => ['programx_id' => $pid]]) : (int)($row['group_count'] ?? 0) ?>
                            </td>
                            <td>
                                <?= $pid ? Html::a((string)(int)($row['participant_count'] ?? 0), ['/program-registration/index', 'ProgramRegistrationSearch' => ['programx_id' => $pid]]) : (int)($row['participant_count'] ?? 0) ?>
                            </td>
                            <td>
                                <?= $pid ? Html::a((string)(int)($row['rubric_count'] ?? 0), ['/program/rubrics', 'id' => $pid, 'sub' => $sid]) : (int)($row['rubric_count'] ?? 0) ?>
                            </td>
                            <td>
                                <?= $pid ? Html::a((string)(int)($row['session_count'] ?? 0), ['/program/rubrics', 'id' => $pid, 'sub' => $sid]) : (int)($row['session_count'] ?? 0) ?>
                            </td>
                        </tr>
                    <?php $i++; } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

</section>
