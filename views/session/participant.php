<?php

use yii\helpers\Html;
use yii\helpers\Url;

/** @var yii\web\View $this */
/** @var app\models\Session $model */

$this->title = 'Attendance & Certificate';
$this->params['breadcrumbs'][] = ['label' => 'Sessions', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="session-create">


    <div class="pagetitle">
<h1><?= Html::encode($this->title) ?></h1>

<div>
                <button class="btn btn-primary" id="scanner" style="margin-top: 20px; margin-bottom:20px" type="button"> <i class="bx bx-qr-scan"></i>  SCAN NOW</button> <a href="<?=Url::to(['cert-qr'])?>" class="btn btn-warning" style="margin-top: 20px; margin-bottom:20px" target="_blank"><i class="bi bi-award"></i>  CERTIFICATE</a>
              </div>

<?php
$this->registerJs('

$("#scanner").click(function(){

    var id = $(this).attr("value");
    window.open("'. Url::to(['/session/qrscanner']) .'?m="+id , "_blank", "toolbar=yes,scrollbars=yes,resizable=yes,top=0,left=0,width="+screen.width+",height="+screen.height);
});

');
?>


</div>



    </div>

 <div class="card">
 <div class="card-header">Recorded Attendance</div>
         <div class="card-body pt-4">
<table class="table">
            <tbody>
                <tr><th>No.</th><th>Session</th><th>Date Time</th></tr>
                <?php 
                if($list){
                    $i=1;
                    foreach($list as $r){
                        echo '<tr><td>'.$i.'. </td><td>'.$r->session->session_name.'</td><td>'.date("d M Y h:i:s A", strtotime($r->scanned_at)).'</td></tr>';
                        $i++;
                    }
                }else{
                    echo '<tr><td colspan="3">You do not have any recorded attendance.</td></tr>';
                     }
                ?> 
            </tbody>
        </table>
             
         </div>
     </div>

</div>
