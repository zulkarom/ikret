<?php

/* @var $this yii\web\View */
/* @var array $publishedTemplates */

use app\models\Program;
use app\models\ProgramRegistration;
use yii\helpers\Html;
use yii\helpers\Url;

$soonText = '<span class="text-muted">To be available soon</span>';
$downloadButton = function ($url, $templateId) use ($publishedTemplates, $soonText) {
    if (empty($publishedTemplates[$templateId])) {
        return $soonText;
    }

    return '<a href="' . Url::to($url) . '" class="btn btn-sm btn-primary" target="_blank">DOWNLOAD</a>';
};
?>
  <div class="pagetitle">
<h1>Certificates</h1></div>

    </div><!-- End Page Title -->

    <section class="section dashboard">

    <div class="card">
            <div class="card-body">
            <div class="table-responsive">
    <table class="table">
    <tbody>
        <tr><th>No.</th><th>Type</th><th>Programs</th><th></th></tr>
    <?php
    $i = 1;
        if($registered){
          
          foreach($registered as $program){
            echo ' <tr><td>'.$i.'. </td><td>Certificate of Participation</td><td>'.$program->programNameLong;
            echo '<div style="font-size:12px;">';
            echo $program->memberStr;
            echo '</div></td><td>' . $downloadButton(['cert-participation','reg' => $program->id], 1) . '</td></tr>';
            $i++;
          }
        }

        if($sessions){
          
          foreach($sessions as $s){
            echo ' <tr><td>'.$i.'. </td><td>Certificate of Participation</td><td>'.$s->program->program_name ;
            echo '<div style="font-size:12px;">';
            echo $s->session_name;
            echo '</div>';
            echo '<div style="font-size:12px;">BY ';
            echo strtoupper($s->speaker);
            echo '</div>';
            echo '</td><td>' . $downloadButton(['cert-participation-session','reg' => $s->reg_id, 's' => $s->id, 'u' => Yii::$app->user->identity->id], 7) . '</td></tr>';
            $i++;
          }
        }

        if($medals){
          foreach($medals as $a){
            echo ' <tr><td>'.$i.'. </td><td>Certificate of Achievement
            <br /><b>('.$a->awardTextColor().')</b>
            </td><td>'.$a->programNameLong;
            echo '<div style="font-size:12px;">';
            echo $a->memberStr;
            echo '</div></td><td>' . $downloadButton(['cert-achievement','reg' => $a->id], 4) . '</td></tr>';
            $i++;
          }
        }

        if($excel){
          foreach($excel as $b){
           //echo '<pre>';
           // print_r($b);
            $reg = ProgramRegistration::findOne($b["id"]);
            echo ' <tr><td>'.$i.'. </td><td>Certificate of Excellence
            <br /><b>(<span style="color:#DA9100">'.strtoupper($b["achieve_name"]).'</span>)</b>
            </td><td>'.$reg->programNameLong;
            echo '<div style="font-size:12px;">';
            echo $reg->memberStr;
            echo '</div></td><td>' . $downloadButton(['cert-excellence','reg' => $reg->id], 5) . '</td></tr>';
            $i++;
          }
        }
        if($i == 1){
          echo '<tr><td colspan="4">No Certificate found.</td></tr>';
        }

    ?>
       
    </tbody>
</table>
</div>
            </div>
        </div>

  
    </section>
