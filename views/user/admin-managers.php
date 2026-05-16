<?php

use app\models\UserRole;
use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var array $groups */

$this->title = 'Admins & Managers';
$this->params['breadcrumbs'][] = ['label' => 'All Users', 'url' => ['all']];
$this->params['breadcrumbs'][] = $this->title;

$labels = UserRole::listRoles();
$order = [
    'superadmin',
    'admin-registration',
    'admin-jury',
    'admin-certificate',
    'manager',
];
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
                            <th style="width: 220px;">Role</th>
                            <th style="width: 260px;">Program/Sub</th>
                            <th>Name</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $hasAny = false;
                        foreach($order as $roleName){
                            $roles = $groups[$roleName] ?? [];
                            if(!$roles){
                                continue;
                            }
                            $hasAny = true;
                            $roleLabel = $labels[$roleName] ?? $roleName;

                            $byProgram = [];
                            foreach($roles as $ur){
                                $programSubText = '-';
                                if($roleName === 'manager'){
                                    $programSubText = $ur->program ? (string)$ur->program->program_abbr : '-';
                                    $subAbbr = $ur->programSub ? (string)$ur->programSub->sub_abbr : '';
                                    if($subAbbr !== ''){
                                        $programSubText .= ' / ' . $subAbbr;
                                    }
                                }
                                if(!isset($byProgram[$programSubText])){
                                    $byProgram[$programSubText] = [];
                                }
                                $byProgram[$programSubText][] = $ur;
                            }

                            $roleRowspan = 0;
                            foreach($byProgram as $urs){
                                $roleRowspan += count($urs);
                            }

                            $rolePrinted = false;
                            foreach($byProgram as $programSubText => $urs){
                                $programRowspan = count($urs);
                                foreach($urs as $pIdx => $ur){
                                    echo '<tr>';

                                    if(!$rolePrinted){
                                        echo '<td rowspan="' . (int)$roleRowspan . '" class="fw-semibold">' . Html::encode($roleLabel) . '</td>';
                                        $rolePrinted = true;
                                    }

                                    if($pIdx === 0){
                                        echo '<td rowspan="' . (int)$programRowspan . '">' . Html::encode($programSubText) . '</td>';
                                    }

                                    $name = $ur->user ? $ur->user->fullname : '';
                                    $userLink = $ur->user ? Html::a(Html::encode($name), ['/user/view', 'id' => $ur->user->id]) : Html::encode($name);
                                    echo '<td>' . $userLink . '</td>';
                                    echo '</tr>';
                                }
                            }
                        }

                        if(!$hasAny){
                            echo '<tr><td colspan="3" class="text-muted text-center">No admin or manager roles found.</td></tr>';
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>
