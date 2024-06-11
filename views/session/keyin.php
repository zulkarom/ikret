<?php

use yii\bootstrap5\ActiveForm;
use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\Session $model */

$this->title = 'Key In Attendance : ' . $model->session_name;

?>
<div class="session-update">

<div class="pagetitle">
<h1><?= Html::encode($this->title) ?></h1></div>

<div class="card">
    <div class="card-header">Recent 5 Participants</div>
        <div class="card-body">
        <table class="table">
                <tbody>
                    <tr><th>No.</th><th>Participant</th><th>Time</th></tr>
                    <?php 
                    if($model->sessionAttendancesRecent){
                        $i=1;
                        foreach($model->sessionAttendancesRecent as $r){
                            $style = $i == 1 ? 'style="background-color:yellow"' : '';
                            echo '<tr><td>'.$i.'. </td><td><span  '.$style.'>'.$r->user->fullname.'</span></td><td><span  '.$style.'>'.$r->scanned_at.'</span></td></tr>';
                            $i++;
                        }
                    }
                    ?> 
                </tbody>
            </table>
        </div>
    </div>

    <div class="card">
  
            <div class="card-body pt-4">
                
            <?php $form = ActiveForm::begin(); ?>

<?= $form->field($att, 'user_matric')->textInput(['placeholder' => 'Matric/ Staff No. / Email ', 'class' => 'form-control form-control-lg'])->label(false) ?>

<div class="form-group">
* key in your matric/ staff no. or your email and press enter
</div>

<?php ActiveForm::end(); ?>
            </div>
        </div>


    </div>



</div>
