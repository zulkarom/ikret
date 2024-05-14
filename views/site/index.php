<?php

/* @var $this yii\web\View */

use yii\helpers\Html;

$this->title = 'HOME - I-CREATE - The International Convention on Resourceful Entrepreneurs Achieving Tomorrow\'s Excellence';
?>
  <div class="pagetitle">
      <div align="center" style="text-align:center">
      <img src="<?=Yii::getAlias('@web')?>/images/logo-sub.png" style="max-width:500px" width="100%" />
      </div>

    </div><!-- End Page Title -->

    <?php 
    if(Yii::$app->user->isGuest){
      ?>
      <div style="text-align:center" align="center"><?=Html::a('<i class="bi bi-box-arrow-in-right"></i> Login',['/site/login'],['class' => 'btn btn-primary'])?> <?=Html::a('<i class="bi bi-card-list"></i> Register',['/site/register'],['class' => 'btn btn-success'])?></div>
      <?php
    }else{
      ?>
      <div style="text-align:center" align="center">
      <?=Yii::$app->user->identity->isParticipant ? Html::a('<i class="bi bi-easel"></i> List of Programs',['/program/index'],['class' => 'btn btn-primary']) : Html::a('<i class="bi bi-file-earmark-person"></i> Profile',['/user/index'],['class' => 'btn btn-primary'])?>
       <?=Html::a('<i class="bi bi-box-arrow-right"></i> Logout',['/site/logout'],['class' => 'btn btn-success'])?></div>
      <?php
    }
    
    
    ?>
    


<br />

    <section style="text-align:justify">
    The International Convention on Resourceful Entrepreneurs Achieving Tomorrow’s Excellence (I-CREATE) serves as an academic nexus, consolidating diverse entrepreneurial innovation initiatives involving six sub-programs. COMEI 3.0 cultivates entrepreneurial zeal among students via workshops and pitch sessions, while JFED nurtures franchise business expertise, facilitating dialogue with industry elites. AIFIF augments student understanding of finance through seminars and career expos, harmonizing educational and industrial demands. NEWeek instills practical entrepreneurship by enabling students to enact theoretical concepts. IMPACT fosters community engagement and problem-solving acumen, while RISE provides a platform for showcasing innovative business concepts. At I-CREATE, these programs converge, fostering interdisciplinary discourse, creativity, and advancing entrepreneurship scholarship for future leaders.
    </section>