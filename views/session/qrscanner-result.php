<?php

use app\models\Session;
use app\models\SessionAttendance;
use backend\modules\egayong\models\AnakGayong;
use backend\modules\egayong\models\SesiHadir;
use backend\modules\egayong\models\SesiKelas;
use backend\modules\egayong\models\SesiRegister;
use yii\db\Expression;
use yii\helpers\Url;

date_default_timezone_set("Asia/Kuala_Lumpur");
function record($t, $user_id){
    date_default_timezone_set("Asia/Kuala_Lumpur");
    $msg = '';
    //pastikan kelas wujud
    $session = Session::find()->where(['token' => $t])->one();
    $start = strtotime($session->datetime_start);
    $end = strtotime($session->datetime_end);
    $valid = time() >= $start && time() <= $end;
    if($session){
        if($valid){
            $ada = SessionAttendance::find()->alias('a')
            ->where(['a.session_id' => $session->id, 'a.user_id' => $user_id])
            ->one();
            if($ada){
                $msg = 'Attendance had been recorded already';
                return [true, $msg, $ada];
            }else{
                
                $att = new SessionAttendance();
                $att->user_id = $user_id;
                $att->session_id = $session->id;
                $att->scanned_at = new Expression("NOW()");
                if($att->save()){
                    return [true, $msg, $att];
                }else{
                    $msg = 'Saving Failed';
                }
            }
        }else{
            $msg = 'Invalid Session Time - name - '.$session->session_name.' start-'.$session->datetime_start.'-'.$start. '-end-'. $session->datetime_start . '-' . $end . '-curr-'. time() . '-' . date('Y-m-d h:i:s A', time());
        }
    }else{
        $msg = 'Invalid Session Code';
    }

    return [false, $msg];
}

?>
 <div class="box">
<div class="box-header" align="center">
<div class="box-title">I-CREATE ATTENDANCE</div>
</div>
<div class="box-body">

<div align="center" style="margin-top:3px">

<?php
$user = Yii::$app->user->identity;
$result = record($t,$user->id);
if($result[0]){
    $attend = $result[2];
    $attend = SessionAttendance::findOne($attend->id);
    echo '<h2 style="color:blue">Your attendance has been recorded</h2>';
    echo '<p>'.strtoupper($user->fullname).'<br />
    '. $attend->session->session_name .'
    <br />'.$attend->scanned_at.'</p>';
    echo '<p style="color:red">' . $result[1] . '</p>';
}else{
    echo '<h1 style="color:red">Failed to record attendance</h1>';
    echo '<p>' . $result[1] . '</p>';
}

?>

<button type="button" class="btn btn-primary btn-lg"  id="tutup">Close</button> 
<?=$err?>
</div>
</div>
</div>

<?php
$url = Url::to(['/session/participant'], true);
$this->registerJs('

$("#tutup").click(function(){
window.close();
});

window.onunload = refreshParent;
function refreshParent() {
    window.opener.location.href = "'.$url.'";
}

');
?>