<?php

use yii\helpers\Html;
use yii\helpers\Url;

 if($register->status > 0){?>
      <div class="card profile">
      <div class="card-header">Registration Details</div>
    <div class="card-body profile-overview pt-4">

    <div class="row">
      <div class="col-lg-3 col-md-4 label ">Status</div>
      <div class="col-lg-9 col-md-8"><?=$register->statusLabel?></div>
    </div>

    <div class="row">
      <div class="col-lg-3 col-md-4 label ">Submitted at</div>
      <div class="col-lg-9 col-md-8"><?=$register->submitted_at?></div>
    </div>

    <div class="row">
      <div class="col-lg-3 col-md-4 label ">Program</div>
      <div class="col-lg-9 col-md-8"><?=$register->program->program_name?></div>
    </div>

    <div class="row">
      <div class="col-lg-3 col-md-4 label ">Registration by</div>
      <div class="col-lg-9 col-md-8"><?=$register->user->fullname?></div>
    </div>

    <?php
    ///showFieldUser($register->user, 'fullname');
    showFieldUser($register->user, 'matric');
    showFieldUser($register->user, 'phone');
    showFieldUser($register->user, 'email');

    showField($register, $arr_fields,'nric');
    showField($register, $arr_fields,'project_name');
    showField($register, $arr_fields,'project_desc', true);
    showFieldList($register, $arr_fields,'participant_cat_local','listParticipantLocal');
    showFieldList($register, $arr_fields,'participant_cat_group','listParticipantGroup');
    showFieldList($register, $arr_fields,'competition_type','listCompetitionType');
    showFieldList($register, $arr_fields,'competition_cat','listCategoryCome');
    showFieldList($register, $arr_fields,'advisor_dropdown','listNeweekAdvisor');
    showField($register, $arr_fields,'advisor');
    showFieldList($register, $arr_fields,'booth_number','listNeweekBooth');
    showFieldList($register, $arr_fields,'participant_mode','listParticipantMode');
    showFieldList($register, $arr_fields,'participant_cat_umk','listParticipantUMK');
    showFieldList($register, $arr_fields,'participant_program','listParticipantProgram','other_program');
    showField($register, $arr_fields,'institution');


    ?>

<?php 
    if(in_array('group_member',$arr_fields)){
    ?>
    <div class="row">
      <div class="col-lg-3 col-md-4 label "><?=$register->getAttributeLabel('group_member')?></div>
      <div class="col-lg-9 col-md-8"><?php 
      $members = $register->members;
      if($members){
        $i = 1;
        foreach($members as $m){
            $br = $i == 1 ? '' : '<br />';
            $leader = $i == 1 ? ' - <b>Leader</b>' : '';
            echo $br . $m->member_name;
            if($m->member_matric){
                echo ' ('.$m->member_matric.')';
                echo $leader;
                
            }
        $i++;
        }
      }
      
      
      ?></div>
    </div>
    <?php
    }
    ?>



<?php if(in_array('poster_file', $arr_fields)){?>

    <div class="row">
      <div class="col-lg-3 col-md-4 label "><?=$register->getAttributeLabel('poster_file')?></div>
      <div class="col-lg-9 col-md-8">
      <?php 
if($register->poster_file){
echo Html::a('<i class="bi bi-file-earmark-pdf"></i> Uploaded Poster' , Url::to(['download-poster-file','id' => $register->id]), ['target' => '_blank']);
}
?>
    </div>
    </div>

<?php } ?>


<?php if(in_array('payment_file', $arr_fields)){?>

<div class="row">
  <div class="col-lg-3 col-md-4 label "><?=$register->getAttributeLabel('payment_file')?></div>
  <div class="col-lg-9 col-md-8">
  <?php 
if($register->payment_file){
echo Html::a('<i class="bi bi-file-earmark-pdf"></i> Uploaded Proof of Payment' , Url::to(['download-payment-file','id' => $register->id]), ['target' => '_blank']);
}
?>
</div>
</div>

<?php } ?>








    </div>
  </div>
<?php } ?>

                  
</div>


<?php 

function showField($register, $arr_fields, $attr, $linebreak = false){
    if(in_array($attr ,$arr_fields)){
    ?>
    <div class="row">
      <div class="col-lg-3 col-md-4 label "><?=$register->getAttributeLabel($attr)?></div>
      <div class="col-lg-9 col-md-8">
        <?php
        if($linebreak){
            echo nl2br($register->$attr);
        }else{
            echo $register->$attr;
        }
        ?>
    </div>
    </div>
    <?php
    }
}

function showFieldUser($user, $attr){
    ?>
    <div class="row">
      <div class="col-lg-3 col-md-4 label "><?=$user->getAttributeLabel($attr)?></div>
      <div class="col-lg-9 col-md-8">
        <?php
         echo $user->$attr;
        ?>
    </div>
    </div>
    <?php
}

function showFieldList($register, $arr_fields, $attr, $listName, $otherField = false){
    if(in_array($attr ,$arr_fields)){
    ?>
    <div class="row">
      <div class="col-lg-3 col-md-4 label "><?=$register->getAttributeLabel($attr)?></div>
      <div class="col-lg-9 col-md-8"><?=$register->getListLabel($listName, $register->$attr)?>
      <?php 
      if($otherField){
        if($register->$otherField == 99){
            echo '<br />('.$register->$otherField.')';
        }
      }
    ?>
    </div>
    </div>
    <?php
    }
}


