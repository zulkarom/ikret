<?php

/** @var yii\web\View $this */
/** @var app\models\JuryRequirement $model */

 $programList = $programList ?? [];
 $subList = $subList ?? [];
 $sessionList = $sessionList ?? [];

$this->title = 'Create Jury Requirement';
$this->params['breadcrumbs'][] = ['label' => 'Jury Requirements', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>

<div class="pagetitle">
    <h1><?=$this->title?></h1>
</div>

</div><!-- End Page Title -->

<section class="section">
    <div class="card">
        <div class="card-body pt-4">
            <?= $this->render('_form', [
                'model' => $model,
                'programList' => $programList,
                'subList' => $subList,
                'sessionList' => $sessionList,
            ]) ?>
        </div>
    </div>
</section>
