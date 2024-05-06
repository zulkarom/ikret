<?php

/* @var $this yii\web\View */

use yii\helpers\Url;

$this->title = 'Pre-Event Questionnaire';
?>
  <div class="pagetitle">
<h1>Pre-Event Questionnaire</h1></div>

    </div><!-- End Page Title -->

    <section class="section dashboard">

    
    <?=$this->render('_questionnaire', [    
        'quest_likert' => $quest_likert,
        'quest_essay' => $quest_essay,
        'model' => $model,
        'pre_post' => 1
    ]);
    ?>
    



    </section>