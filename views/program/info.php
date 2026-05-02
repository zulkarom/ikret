<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\ProgramRegistration $model */

$this->title = 'Update Program Info';

$programSub = $programSub ?? null;

$this->params['breadcrumbs'][] = [
    'label' => $model->program_abbr . ($programSub ? ' / ' . $programSub->sub_abbr : ''),
    'url' => ['/program-registration/manager-dashboard', 'id' => $model->id, 'sub' => $programSub ? $programSub->id : null]
];
$this->params['breadcrumbs'][] = $this->title;
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

</div>
            </div>
        </div>



    </section>


