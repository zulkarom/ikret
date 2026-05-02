<?php

use yii\helpers\Html;
use yii\helpers\Url;

/** @var yii\web\View $this */
/** @var app\models\UserRole $role */
/** @var app\models\Program $program */
/** @var app\models\ProgramSub|null $programSub */

$title = $program->program_name;
if($programSub){
    $title .= ' / ' . $programSub->sub_name;
}

$this->title = 'Manager Dashboard - ' . $title;

$id = (int)$program->id;
$sub = $programSub ? (int)$programSub->id : null;

$cards = [];

if((int)$program->program_type === 1){
    $cards[] = [
        'title' => 'Participants & Juries Assignment',
        'url' => Url::to(['program-registration/manager', 'id' => $id, 'sub' => $sub]),
        'class' => 'border-primary',
    ];
    $cards[] = [
        'title' => 'Result By Assignments',
        'url' => Url::to(['program-registration/jury-result', 'id' => $id, 'sub' => $sub]),
        'class' => 'border-primary',
    ];
    $cards[] = [
        'title' => 'Analysis & Achievement',
        'url' => Url::to(['program-registration/manager-analysis', 'id' => $id, 'sub' => $sub]),
        'class' => 'border-primary',
    ];
    $cards[] = [
        'title' => 'Certificates',
        'url' => Url::to(['program-registration/manager-view-certs', 'id' => $id, 'sub' => $sub]),
        'class' => 'border-primary',
    ];
    $cards[] = [
        'title' => 'Registration Fields',
        'url' => Url::to(['program/register-fields', 'id' => $id, 'sub' => $sub]),
        'class' => 'border-secondary',
    ];
    $cards[] = [
        'title' => 'Rubrics',
        'url' => Url::to(['program/rubrics', 'id' => $id, 'sub' => $sub]),
        'class' => 'border-secondary',
    ];
    $cards[] = [
        'title' => 'Achievements',
        'url' => Url::to(['program/achievement', 'id' => $id, 'sub' => $sub]),
        'class' => 'border-secondary',
    ];
}else{
    $cards[] = [
        'title' => 'Participants & Certificates',
        'url' => Url::to(['program-registration/manager-session', 'id' => $id, 'sub' => $sub]),
        'class' => 'border-primary',
    ];
}

$cards[] = [
    'title' => 'Program Info',
    'url' => Url::to(['program/info', 'id' => $id, 'sub' => $sub]),
    'class' => 'border-dark',
];

?>

<div class="pagetitle">
    <h1><?=$this->title?></h1>
</div>

</div><!-- End Page Title -->

<section class="section dashboard">

    <div class="row">
        <?php foreach($cards as $card){ ?>
            <div class="col-12 col-md-6 col-lg-4 mb-3">
                <div class="card <?=$card['class']?>">
                    <div class="card-body pt-4">
                        <h5 class="card-title"><?= Html::encode($card['title']) ?></h5>
                        <?= Html::a('Open', $card['url'], ['class' => 'btn btn-primary']) ?>
                    </div>
                </div>
            </div>
        <?php } ?>
    </div>

</section>
