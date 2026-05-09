<?php

/* @var $this yii\web\View */

use app\models\Common;
use app\models\Setting;
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'HOME - I-CREATE - The International Convention on Resourceful Entrepreneurs Achieving Tomorrow\'s Excellence';

$set = Setting::findOne(1);

$programmeBookUrl = 'https://fkp-portal.umk.edu.my/icreate/docs/I-CREATE-2026-BUKU-PROGRAM.pdf';
if ($set && !empty($set->programme_book_url)) {
  $programmeBookUrl = $set->programme_book_url;
}

$programmeBookQr = Yii::getAlias('@web') . '/docs/qr-buku.png';
if ($set && !empty($set->programme_book_qr)) {
  $programmeBookQr = Yii::getAlias('@web') . '/' . ltrim($set->programme_book_qr, '/');
}

$programDescription = "The International Convention on Resourceful Entrepreneurs Achieving Tomorrow’s Excellence (I-CREATE) serves as an academic nexus, consolidating diverse entrepreneurial innovation initiatives involving six sub-programs. COMEI 3.0 cultivates entrepreneurial zeal among students via workshops and pitch sessions, while JFED nurtures franchise business expertise, facilitating dialogue with industry elites. AIFIF augments student understanding of finance through seminars and career expos, harmonizing educational and industrial demands. NEWeek instills practical entrepreneurship by enabling students to enact theoretical concepts. IMPACT fosters community engagement and problem-solving acumen, while RISE provides a platform for showcasing innovative business concepts. At I-CREATE, these programs converge, fostering interdisciplinary discourse, creativity, and advancing entrepreneurship scholarship for future leaders.";
if ($set && !empty($set->program_description)) {
  $programDescription = $set->program_description;
}

$current = $current ?? null;
$next = $next ?? null;
$previous = $previous ?? null;
?>
  <div class="pagetitle">
      <div align="center" style="text-align:center">
      <?php
      $heroImage = Yii::getAlias('@web') . '/images/logo-icreate-subs.png';
      if ($set && !empty($set->banner_image)) {
        $heroImage = Yii::getAlias('@web') . '/' . ltrim($set->banner_image, '/');
      }
      ?>
      <img src="<?= Html::encode($heroImage) ?>" style="width:100%; max-width:80%; height:auto;" />
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
       <?=Html::button('<i class="bx bx-qr-scan"></i> Scan Attendance',['class' => 'btn btn-warning', 'id' => 'scanner'])?></div>
      <?php
    }
    
    
    ?>
    


<br />
<?php 
//ketika masih berjalan
$start = $set ? strtotime($set->date_start) : 0;
$end = $set ? strtotime($set->date_end) : 0;
$running = time() >= $start && time() <= $end;
$grid = 12;
?>

<div class="row">
  <?php if($running && $set && ($set->show_icreate_list_event === null || $set->show_icreate_list_event)){
    $grid = 6;
    ?>
    <div class="col-md-6">

    <?php if($current){?>
    <table class="table" style="background:none;">
                    <tbody>
                        <tr><th align="center" style="text-align: center;background:none">CURRENT I-CREATE EVENTS</th></tr>
                        <?php 
                        //$sessions = [];
                        
                            $i=1;
                            foreach($current as $r){
                                generateTr($r, true);
                                $i++;
                            }
                        
                        ?> 
                    </tbody>
                </table>
                <?php 
                }
if($next){?>
    <table class="table" style="background:none;">
                    <tbody>
                        <tr><th align="center" style="text-align: center;background:none">NEXT I-CREATE EVENTS</th></tr>
                        <?php 
                        //$sessions = [];
                        
                            $i=1;
                            foreach($next as $r){
                                generateTr($r);
                                $i++;
                            }
                        
                        ?> 
                    </tbody>
                </table>
                <?php 
                }


                if($previous){
                ?>
                <table class="table">
                    <tbody>
                        <tr><th align="center" style="text-align: center; background:none">PREVIOUS I-CREATE EVENTS</th></tr>
                        <?php 
                        
                            $i=1;
                            foreach($previous as $r){
                                generateTr($r);
                                $i++;
                            }
                        
                        ?> 
                    </tbody>
                </table>
                <?php } ?>

    </div>
    <?php } ?>
    <div class="col-md-<?=$grid?>">
    <section style="text-align:justify;padding:15px">
    <?= nl2br(Html::encode($programDescription)) ?>
    </section>

    <div style="text-align: center; margin-top:5px" align="center">
    <a href="<?= Html::encode($programmeBookUrl) ?>" target="_blank"><img src="<?= Html::encode($programmeBookQr) ?>" style="max-width:125px" width="90%" /></a> 
    <div> <a href="<?= Html::encode($programmeBookUrl) ?>" target="_blank">Programme Book</a> </div>


    </div>
</div>

  
  </div>
  <?php

  function generateTr($r, $curr = false){
    $start = $r->datetime_start;
        $end = $r->datetime_end;
        $date_start = date('Y-m-d', strtotime($start));
        $date_end = date('Y-m-d', strtotime($end));
        $date = Common::dateStartEndFormat($date_start,$date_end);
        $time_start = date('h:i A', strtotime($start));
        $time_end = date('h:i A', strtotime($end));

                              $program = '';
        if($r->program){
            $program = '<br />('.$r->programNameShort . ')';
        }
        $style = $curr ? 'style="background-color:yellow"' : '';
        echo '<tr><td style="text-align:center;background:none"><span '.$style.'>' . $r->session_name. '</span>' .$program.'
        <div><span>'.$date.' '.$time_start.' - '.$time_end.'</span></div></td>
        </tr>';

  }
$this->registerJs('

$("#scanner").click(function(){
    window.open("'. Url::to(['/session/qrscanner']) .'", "_blank", "toolbar=yes,scrollbars=yes,resizable=yes,top=0,left=0,width="+screen.width+",height="+screen.height);
});

');
?>