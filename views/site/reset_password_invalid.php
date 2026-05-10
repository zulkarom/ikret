<?php

/* @var $this yii\web\View */

use yii\helpers\Html;

$this->title = 'Reset link expired';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="site-reset-password-invalid">
    <div class="row justify-content-center">
        <div class="col-lg-5 col-md-7">
            <div class="card border-0 shadow-sm my-5">
                <div class="card-body p-4 p-md-5 text-center">
                    <div class="mb-3">
                        <span class="badge bg-warning text-dark px-3 py-2">Password reset</span>
                    </div>

                    <h1 class="h3 mb-3"><?= Html::encode($this->title) ?></h1>

                    <p class="text-muted mb-4">
                        This password reset link is invalid or has already expired. Please request a new password reset link and use the latest email we send you.
                    </p>

                    <div class="d-grid gap-2 d-sm-flex justify-content-sm-center">
                        <?= Html::a('Request new link', ['/site/forgot-password'], ['class' => 'btn btn-primary']) ?>
                        <?= Html::a('Back to login', ['/site/login'], ['class' => 'btn btn-outline-secondary']) ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
