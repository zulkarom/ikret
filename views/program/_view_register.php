<?php

use app\models\Mentor;
use app\models\Setting;
use yii\helpers\Html;
use yii\helpers\Url;

 $publicMode = $publicMode ?? false;
 $storageEntry = $storageEntry ?? false;

 if($edit == false){?>
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
      <div class="col-lg-9 col-md-8"><?php
      if($register->user){
        echo $register->user->fullname;
      }else if($register->contact_person){
        echo $register->contact_person;
      }else{
        echo $register->contact_email;
      }
      ?></div>
    </div>

    <?php
    if($register->user){
    ///showFieldUser($register->user, 'fullname');
    showFieldUser($register->user, 'matric');
    showFieldUser($register->user, 'phone');
    showFieldUser($register->user, 'email');
    }

    showField($register, $arr_fields,'nric');
    showField($register, $arr_fields,'project_name');
    showField($register, $arr_fields,'project_desc', true);
    showFieldList($register, $arr_fields,'participant_cat_local','listParticipantLocal');
    showFieldList($register, $arr_fields,'participant_cat_group','listParticipantGroup');
    showFieldList($register, $arr_fields,'competition_type','listCompetitionType');
    //showFieldList($register, $arr_fields,'program_sub','listCategoryCome');
    showFieldModel($register, $arr_fields, 'program_sub', 'programSub', 'sub_name');
    showFieldList($register, $arr_fields,'advisor_dropdown','listNeweekAdvisor');
    showField($register, $arr_fields,'advisor');
    showFieldList($register, $arr_fields,'booth_number','listNeweekBooth');
    showFieldList($register, $arr_fields,'participant_mode','listParticipantMode');
    showFieldCatProgram($register, $arr_fields,'participant_cat_program');
    showFieldCompetitionCatProgram($register, $arr_fields,'competition_cat_program');
    showFieldList($register, $arr_fields,'participant_cat_umk','listParticipantUMK');
    showFieldList($register, $arr_fields,'participant_program','listParticipantProgram','other_program');
    showField($register, $arr_fields,'institution');
    showField($register, $arr_fields,'contact_person');
    showField($register, $arr_fields,'contact_no');
    showField($register, $arr_fields,'contact_email');
    showField($register, $arr_fields,'group_code');
    showField($register, $arr_fields,'group_name');
    showFieldMentor($register, $arr_fields, 'mentor_main');
    showFieldMentor($register, $arr_fields, 'mentor_co');

    


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



<?php if(in_array('poster_file', $arr_fields) && (int)$register->participant_mode === 2){?>

    <div class="row">
      <div class="col-lg-3 col-md-4 label "><?=$register->getAttributeLabel('poster_file')?></div>
      <div class="col-lg-9 col-md-8">
      <?php 
if($register->poster_file){
echo Html::a('<i class="bi bi-file-earmark-pdf"></i> Uploaded Poster' , Url::to(['/program/download-poster-file','id' => $register->id]), ['target' => '_blank']);
}
?>
    </div>
    </div>

<?php } ?>


<?php if(in_array('abstract_file', $arr_fields)){?>

    <div class="row">
      <div class="col-lg-3 col-md-4 label "><?=$register->getAttributeLabel('abstract_file')?></div>
      <div class="col-lg-9 col-md-8">
      <?php 
if($register->abstract_file){
echo Html::a('<i class="bi bi-file-earmark-text"></i> Uploaded Abstract' , Url::to(['/program/download-abstract-file','id' => $register->id]), ['target' => '_blank']);
}
?>
    </div>
    </div>

<?php } ?>


<?php if(in_array('video_link', $arr_fields) && (int)$register->participant_mode === 2){?>

<div class="row">
  <div class="col-lg-3 col-md-4 label "><?=$register->getAttributeLabel('video_link')?></div>
  <div class="col-lg-9 col-md-8">
  <?php 
if($register->video_link){
echo Html::a('<i class="bi bi-link-45deg"></i> Video Link', $register->video_link, ['target' => '_blank']);
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
echo Html::a('<i class="bi bi-file-earmark-pdf"></i> Uploaded Proof of Payment' , Url::to(['/program/download-payment-file','id' => $register->id]), ['target' => '_blank']);
}
?>
</div>
</div>

<?php } ?>


<?php

$set = Setting::findOne(1);
$due = strtotime($set->allow_edit_reg_until.' 23:59:59');
$allowUpdate = false;
$updateUrl = null;

if($publicMode && !$register->isNewRecord){
    $allowUpdate = true;
    $updateUrl = ['public-register-form', 'id' => $register->program_id, 'reg' => $register->id, 'edit' => 1];
}else if(!Yii::$app->user->isGuest && Yii::$app->user->identity->id == $register->user_id){
    $allowUpdate = true;
    $updateUrl = ['register-form', 'id' => $register->program_id, 'reg' => $register->id, 'edit' => true];
}

if($allowUpdate){
?>
<div class="mt-4">
<?php if(time() < $due){ ?>
<?= Html::a('<i class="bi bi-pencil"></i> Update', $updateUrl, ['class' => 'btn btn-outline-warning']) ?><br />
<?php } else { ?>
<?= Html::button('<i class="bi bi-pencil"></i> Update', ['class' => 'btn btn-outline-secondary', 'disabled' => true]) ?><br />
<?php } ?>
<i style="font-size: 12px;">* finalise before/at <?=date('d/m/Y', strtotime($set->allow_edit_reg_until))?></i>
</div>
<?php
}
?>

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

function showFieldCatProgram($register, $arr_fields, $attr){
  if(in_array($attr, $arr_fields)){
  ?>
  <div class="row">
    <div class="col-lg-3 col-md-4 label "><?=$register->getAttributeLabel($attr)?></div>
    <div class="col-lg-9 col-md-8">
    <?php
    $list = $register->listParticipantCatProgram($register->program_id);
    $id = $register->$attr;
    echo array_key_exists($id, $list) ? $list[$id] : '';
    ?>
    </div>
  </div>
  <?php
  }
}

function showFieldCompetitionCatProgram($register, $arr_fields, $attr){
  if(in_array($attr, $arr_fields)){
  ?>
  <div class="row">
    <div class="col-lg-3 col-md-4 label "><?=$register->getAttributeLabel($attr)?></div>
    <div class="col-lg-9 col-md-8">
    <?php
    $list = $register->listCompetitionCatProgram($register->program_id);
    $id = $register->$attr;
    echo array_key_exists($id, $list) ? $list[$id] : '';
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
    if(in_array($attr, $arr_fields)){
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

function showFieldModel($register, $arr_fields, $attr, $method, $method_attr){
  if(in_array($attr, $arr_fields)){
  ?>
  <div class="row">
    <div class="col-lg-3 col-md-4 label "><?=$register->getAttributeLabel($attr)?></div>
    <div class="col-lg-9 col-md-8">

    <?php 
    if($register->$method){
      echo $register->$method->$method_attr;
    }
    ?>

  </div>
  </div>
  <?php
  }
}

function showFieldMentor($register, $arr_fields, $attr){
  if(in_array($attr, $arr_fields)){
  ?>
  <div class="row">
    <div class="col-lg-3 col-md-4 label "><?=$register->getAttributeLabel($attr)?></div>
    <div class="col-lg-9 col-md-8">

    <?php 
    $main = $attr == 'mentor_main' ? 1 : 0;
    $m = Mentor::findOne(['program_reg_id' => $register->id, 'is_main' => $main]);
    if($m){
       if($m->user){
         echo $m->user->fullname;
       }
    }
    ?>

  </div>
  </div>
  <?php
  }
}




