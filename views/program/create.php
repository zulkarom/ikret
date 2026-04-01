<?php

use yii\helpers\Html;

$this->title = 'Create Program';
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
