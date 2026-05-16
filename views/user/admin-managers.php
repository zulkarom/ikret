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
                            <th>Name</th>
                            <th style="width: 240px;">Email</th>
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
                            $rowspan = count($roles);
                            $roleLabel = $labels[$roleName] ?? $roleName;
                            foreach($roles as $idx => $ur){
                                echo '<tr>';
                                if($idx === 0){
                                    echo '<td rowspan="' . (int)$rowspan . '" class="fw-semibold">' . Html::encode($roleLabel) . '</td>';
                                }
                                $name = $ur->user ? $ur->user->fullname : '';
                                $email = $ur->user ? $ur->user->email : '';
                                $userLink = $ur->user ? Html::a(Html::encode($name), ['/user/view', 'id' => $ur->user->id]) : Html::encode($name);
                                echo '<td>' . $userLink . '</td>';
                                echo '<td>' . Html::encode($email) . '</td>';
                                echo '</tr>';
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
