<?php

/* @var $this yii\web\View */

use yii\helpers\Url;

$this->title = 'Post-Event Questionnaire';
?>
  <div class="pagetitle">
<h1>Post-Event Questionnaire</h1></div>

    </div><!-- End Page Title -->

    <section class="section dashboard">

    
    
    
    <?=$this->render('_questionnaire', [    
        'quest_likert' => $quest_likert,
        'quest_checkbox' => $quest_checkbox,
        'model' => $model,
        'pre_post' => 2
    ]);
    ?>


    </section>
