<?php

use app\models\Mentor;
use app\models\User;
use kartik\select2\Select2;
use yii\helpers\Html;
//use yii\bootstrap5\ActiveForm;
use kartik\widgets\ActiveForm;
use wbraganca\dynamicform\DynamicFormWidget;
use yii\helpers\Url;
use yii\web\JsExpression;

$web = Yii::getAlias('@web');

$edit = $edit ?? false;
$err = $err ?? false;
$demo = $demo ?? false;
$publicMode = $publicMode ?? false;
$storageEntry = $storageEntry ?? false;

$this->title = 'Registration - ' . $model->program_name;

?>
    <div class="d-flex justify-content-center py-4">
                <a href="index.html" class="logo d-flex align-items-center w-auto">
          
                  <span class="d-none d-lg-block"><?=$model->program_name?></span>
                </a>
              </div><!-- End Logo -->
              <?php $arr_fields = $register->getProgramFields($register->program_id);?>
              <?php $fieldLayouts = $register->getProgramFieldLayouts($register->program_id); ?>
              <?php $fieldColClass = function($fieldName, $default = 'col-12') use ($fieldLayouts) {
                  if(!array_key_exists($fieldName, $fieldLayouts)){
                      return $default;
                  }

                  return (int)$fieldLayouts[$fieldName] === 6 ? 'col-md-6' : 'col-12';
              }; ?>

              <?php if(!$publicMode || !$register->isNewRecord){ ?>
              <?=$this->render('_view_register', [    
        'register' => $register,
        'arr_fields' => $arr_fields,
        'edit' => $edit,
        'publicMode' => $publicMode,
        'storageEntry' => $storageEntry,
    ]);
    ?>
    <?php } ?>

      <?php if(!$edit){ //program information 
        ?>
    <div class="card">
    <div class="card-header">Program Information</div>
            <div class="card-body pt-4">
                <?=$model->reg_info?>
            </div>
        </div>
      <?php } ?>

                  <?php if($register->status == 0 || $edit || $err){?>
              <div class="card mb-3">
              <div class="card-header">Registration Form</div>
                <div class="card-body">

                  <div class="pt-4 pb-2">

                    <p class="small">Enter your project details to register in this program.</p>
                  </div>

                  <?php $form = ActiveForm::begin([
                      'id' => 'dynamic-form',
                      'action' => Url::to([$publicMode ? '/storage/index' : 'register']),
                      'options' => [
                          'class' => 'row g-3 needs-validation',
                          'enctype' => 'multipart/form-data',
                      ],
                      'enableClientValidation' => true,
                  ]); ?>

                  <?= $form->errorSummary($register, ['class' => 'alert alert-danger']) ?>

                  <input type="hidden" name="program_id" value="<?=$model->id?>" />
                  <?php if($publicMode){ ?>
                  <input type="hidden" name="storage_action" value="public-register" />
                  <input type="hidden" name="storage_entry" value="1" />
                  <?php } ?>

                  <?php 
                  if(!$register->isNewRecord && !$err){
                      echo '<input type="hidden" name="reg_id" value="'.$register->id.'" />';
                  }
                  if($edit){
                    echo '<input type="hidden" name="edit" value="1" />';
                  }
                  ?>

                   

                    <?php if(in_array('project_name',$arr_fields)){ ?>
                    <div class="<?=$fieldColClass('project_name')?>">
                    <?= $form->field($register, 'project_name')->textarea(['rows' => 2]) ?>
                    </div>
                    <?php } ?>

                    <?php if(in_array('project_desc',$arr_fields)){ ?>
                    <div class="<?=$fieldColClass('project_desc')?>">
                    <?= $form->field($register, 'project_desc')->textarea(['rows' => 4]) ?>
                    </div>
                    <?php } ?>

                    <?php if(in_array('participant_cat_local',$arr_fields)){ ?>
                    <div class="<?=$fieldColClass('participant_cat_local')?>">
                    <?= $form->field($register, 'participant_cat_local')->radioList($register->listParticipantLocal()) ?>
                    </div>
                    <?php } ?>

                    <?php if(in_array('participant_cat_group',$arr_fields)){ ?>
                    <div class="<?=$fieldColClass('participant_cat_group')?>">
                    <?= $form->field($register, 'participant_cat_group')->radioList($register->listParticipantGroup()) ?>
                    </div>
                    <?php } ?>

                    <?php if(in_array('competition_type',$arr_fields)){ ?>
                    <div class="<?=$fieldColClass('competition_type')?>">
                    <?= $form->field($register, 'competition_type')->radioList([
              1 => 'Community Project Ideation', 
              2 => 'Community Project Implementation'
          ]) ?>
                    </div>
                    <?php } ?>

                    <?php if(in_array('program_sub',$arr_fields)){ ?>
                    <div class="<?=$fieldColClass('program_sub')?>">
                    <?= $form->field($register, 'program_sub')->dropDownList($register->program->listSubPrograms(),['prompt' => 'Select Category']) ?>
                    </div>
                    <?php } ?>

                    <?php if(in_array('advisor_dropdown',$arr_fields)){ ?>
                    <div class="<?=$fieldColClass('advisor_dropdown')?>">
                    <?= $form->field($register, 'advisor_dropdown')->dropDownList($register->listNeweekAdvisor(), ['prompt' => 'Selct Lecturer']) ?>
                    </div>
                    <?php } ?>


<?php 
        if(in_array('booth_number',$arr_fields)){
        echo '<div class="' . $fieldColClass('booth_number') . '">';
        echo $form
->field($register, 'booth_number')->dropDownList($register->listNeweekBooth(), ['prompt' => 'Select Booth']);
        echo '</div>';
}
?>

            
           


<?php 
                    if(in_array('nric',$arr_fields)){
                  echo '<div class="' . $fieldColClass('nric') . '">';
                    echo $form
->field($register, 'nric')->textInput();
echo '</div>';

                    } ?>


<?php 
        if(in_array('participant_mode',$arr_fields)){
        echo '<div class="' . $fieldColClass('participant_mode') . '">';
        echo $form
->field($register, 'participant_mode')->radioList($register->listParticipantMode());
        echo '</div>';
}
?>


<?php 
        if(in_array('participant_cat_program',$arr_fields)){
        echo '<div class="' . $fieldColClass('participant_cat_program') . '">';
        echo $form
->field($register, 'participant_cat_program')->dropDownList($register->listParticipantCatProgram($register->program_id, $register->participant_mode), ['prompt' => 'Select Category']);
        echo '</div>';
}
?>


<?php 
        if(in_array('competition_cat_program',$arr_fields)){
        echo '<div class="' . $fieldColClass('competition_cat_program') . '">';
        echo $form
->field($register, 'competition_cat_program')->dropDownList($register->listCompetitionCatProgram($register->program_id), ['prompt' => 'Select Category']);
        echo '</div>';
}
?>



<?php /* 
                    if(in_array('competition_type',$arr_fields)){
                    echo $form
->field($register, 'competition_type')->textInput();
                    } */?>

<?php 
        if(in_array('participant_cat_umk',$arr_fields)){
        echo '<div class="' . $fieldColClass('participant_cat_umk') . '">';
        echo $form
->field($register, 'participant_cat_umk')->radioList($register->listParticipantUMK());
        echo '</div>';
}
?>




<?php 
        if(in_array('participant_program',$arr_fields)){
        echo '<div class="' . $fieldColClass('participant_program') . '">';
        echo $form
->field($register, 'participant_program')->radioList($register->listParticipantProgram());
echo '<input class="form-control" name="ProgramRegistration[other_program]" placeholder="Specify other program..." /><br />';
echo '</div>';
}
?>


<?php 
                    if(in_array('advisor',$arr_fields)){

                    echo '<div class="' . $fieldColClass('advisor') . '">';

                    echo $form
->field($register, 'advisor')->textInput();

                    echo '</div>';

                    } ?>


<?php 
                    if(in_array('institution',$arr_fields)){
                  echo '<div class="' . $fieldColClass('institution') . '">';
                    echo $form
->field($register, 'institution')->textInput();
echo '</div>';

                    } ?>


<?php 
                    if(in_array('contact_person',$arr_fields)){
                  echo '<div class="' . $fieldColClass('contact_person') . '">';
                    echo $form
->field($register, 'contact_person')->textInput();
echo '</div>';

                    } ?>

<?php 
                    if(in_array('contact_no',$arr_fields)){
                  echo '<div class="' . $fieldColClass('contact_no') . '">';
                    echo $form
->field($register, 'contact_no')->textInput();
echo '</div>';

                    } ?>

<?php 
                    if(in_array('contact_email',$arr_fields)){
                  echo '<div class="' . $fieldColClass('contact_email') . '">';
                    echo $form
->field($register, 'contact_email')->textInput();
echo '</div>';

                    } ?>

<?php if($publicMode && $register->isNewRecord){ ?>
                    <div class="<?=$fieldColClass('edit_password')?>">
                    <?= $form->field($register, 'edit_password')->passwordInput() ?>
                    </div>
                    <div class="<?=$fieldColClass('edit_password_confirm')?>">
                    <?= $form->field($register, 'edit_password_confirm')->passwordInput() ?>
                    </div>
<?php } ?>



<?php 
if(in_array('group_member',$arr_fields)){
  $register->group_member = 1;
  $show_group = '';
}else{
  $show_group = 'style="display:none"';
}

 $show_matric = $register->groupMemberShowMatric($register->program_id);
  ?>





  <div class="col-12" <?=$show_group?>>
<br />
<?php DynamicFormWidget::begin([
        'widgetContainer' => 'dynamicform_wrapper',
        'widgetBody' => '.container-items',
        'widgetItem' => '.member-item',
        'limit' => 20,
        'min' => 1,
        'insertButton' => '.add-member',
        'deleteButton' => '.remove-member',
        'model' => $members[0],
        'formId' => 'dynamic-form',
        'formFields' => [
            'id',
            'member_name',
            'member_matric'
        ],
    ]); ?>

    
<label class="form-label pt-0"><?=$register->getAttributeLabel('group_member')?> </label><br />
<i>(Make sure you type the full name (include title if any) correctly since it will be used in certificates. Put the group leader on the top of the list. If individual participant, make sure put only your name)</i>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Full Name</th>
                <?php if($show_matric){ ?>
                <th width="25%">Matric No.</th>
                <?php } ?>
                <th class="text-center">
                    
                </th>
            </tr>
        </thead>
        <tbody class="container-items">
        <?php foreach ($members as $i => $member): ?>
            <tr class="member-item">
            
                <td class="vcenter">
                    <?php
                        if (! $member->isNewRecord) {
                            echo Html::activeHiddenInput($member, "[{$i}]id");
                        }
                    ?>
                    <?= $form->field($member, "[{$i}]member_name")->textInput(['style' => 'text-transform: uppercase'])->label(false) ?>
                </td>
                
                <?php if($show_matric){ ?>
                <td class="vcenter">
                <?= $form->field($member, "[{$i}]member_matric")->label(false) ?>

                </td>
                <?php } ?>

                <td class="text-center vcenter" style="width: 90px;">
                    <button type="button" class="remove-member btn btn-default btn-sm"><span class="bi bi-trash"></span></button>
                </td>
            </tr>
         <?php endforeach; ?>
        </tbody>
        
        <tfoot>
            <tr>
   
                <td colspan="<?= $show_matric ? 2 : 1 ?>">
                <button type="button" class="add-member btn btn-outline-success btn-sm"><span class="bi bi-plus"></span> Add members</button>
                
                </td>
                <td>
                
                
                </td>
            </tr>
        </tfoot>
        
    </table>
    <?php DynamicFormWidget::end(); ?>
  </div>

  <br />
  <?php if(in_array('group_code', $arr_fields) || in_array('group_name', $arr_fields)){ ?>
<div class="row">
    <div class="col-md-6">  <?php if(in_array('group_code', $arr_fields)){
    echo $form->field($register, 'group_code')->textInput();
   }?></div>
    <div class="col-md-6">  <?php if(in_array('group_code', $arr_fields)){
    echo $form->field($register, 'group_name')->textInput();
   }?></div>
</div>
<?php } ?>
<br />

  <div class="row">
      <div class="col-md-6"><?php 
                    if(in_array('mentor_main',$arr_fields)){

                      if(!$register->isNewRecord){
                        $main = Mentor::findOne(['program_reg_id' => $register->id, 'is_main' => 1]);
                        if($main){
                           if($main->user){
                            $register->mentor_main = $main->user_id;
                           }
                        }
                      }

                    echo $form->field($register, 'mentor_main')->widget(Select2::classname(), [
                        'data' => $register->mentorList(),
                        'options' => ['multiple' => false,'placeholder' => 'Select mentor'],
                        'pluginOptions' => [
                            'allowClear' => true
                        ],
                      ]);
/* 
                      //cari mentor
                      $userDesc = '';
                      if(!$register->isNewRecord){
                        
                        $main = Mentor::findOne(['program_reg_id' => $register->id, 'is_main' => 1]);
                        if($main){
                          if($main->user){
                            $userDesc = $main->user->fullname;
                            $register->mentor_main = $main->user_id;
                          }
                        }else{
                          if($register->mentor_main){
                            $u = User::findOne($register->mentor_main);
                            if($u){
                              $userDesc = $u->fullname;
                            }
                          }
                        }
                      }
                  
                  $url = Url::to(['/program-registration/mentor-list-json']);
                  echo $form->field($register, 'mentor_main')->widget(Select2::classname(), [
                      'initValueText' => $userDesc, // set the initial display text
                      'options' => ['placeholder' => 'Find a main mentor ...'],
                  'pluginOptions' => [
                      'allowClear' => true,
                      'minimumInputLength' => 3,
                      'language' => [
                          'errorLoading' => new JsExpression("function () { return 'Waiting for results...'; }"),
                      ],
                      'ajax' => [
                          'url' => $url,
                          'dataType' => 'json',
                          'data' => new JsExpression('function(params) { return {q:params.term}; }')
                      ],
                      'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
                      'templateResult' => new JsExpression('function(user) { return user.text; }'),
                      'templateSelection' => new JsExpression('function (user) { return user.text; }'),
                  ],
                  ]);
                   */




                    } ?></div>
      <div class="col-md-6"><?php 
                    if(in_array('mentor_co',$arr_fields)){

                      if(!$register->isNewRecord){
                        $co = Mentor::findOne(['program_reg_id' => $register->id, 'is_main' => 0]);
                        if($co){
                           if($co->user){
                            $register->mentor_co = $co->user_id;
                           }
                        }
                      }

                      echo $form->field($register, 'mentor_co')->widget(Select2::classname(), [
                        'data' => $register->mentorList(),
                        'options' => ['multiple' => false,'placeholder' => 'Select mentor'],
                        'pluginOptions' => [
                            'allowClear' => true
                        ],
                      ]);

/* 

         //cari mentor
         $userDesc = '';
         if(!$register->isNewRecord){
           $main = Mentor::findOne(['program_reg_id' => $register->id, 'is_main' => 0]);
           if($main){
              if($main->user){
                $userDesc = $main->user->fullname;
                $register->mentor_co = $main->user_id;
              }
             
           }else{
            if($register->mentor_co){
              $u = User::findOne($register->mentor_co);
              if($u){
                $userDesc = $u->fullname;
              }
            }
           }
         }
     
     $url = Url::to(['/program-registration/mentor-list-json']);
     echo $form->field($register, 'mentor_co')->widget(Select2::classname(), [
         'initValueText' => $userDesc, // set the initial display text
         'options' => ['placeholder' => 'Find a co mentor ...'],
     'pluginOptions' => [
         'allowClear' => true,
         'minimumInputLength' => 3,
         'language' => [
             'errorLoading' => new JsExpression("function () { return 'Waiting for results...'; }"),
         ],
         'ajax' => [
             'url' => $url,
             'dataType' => 'json',
             'data' => new JsExpression('function(params) { return {q:params.term}; }')
         ],
         'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
         'templateResult' => new JsExpression('function(user) { return user.text; }'),
         'templateSelection' => new JsExpression('function (user) { return user.text; }'),
     ],
     ]);

 */


                    } ?></div>
  </div>

<?php if(in_array('mentor_main',$arr_fields) || in_array('mentor_co',$arr_fields)){ ?>
<i>* Try to search your mentor, if not found, you need to ask your mentor to register to the system as a mentor.</i>
<?php } ?>
<?php if(in_array('abstract_file', $arr_fields)){?>
    <br /><br />
    <div class="form-group">
<?php 
if(!$register->isNewRecord && $register->abstract_file){
echo Html::a('<i class="bi bi-file-earmark-text"></i> Uploaded Abstract' , Url::to(['/program/download-abstract-file','id' => $register->id]), ['target' => '_blank']);
}
?>
</div>
<?= $form->field($register, 'abstract_instance')->fileInput() ?>
<i>(Please upload abstract in MS Word format: .doc or .docx)</i>
<?php } ?>

<br /><br />


<div id="online-mode-only">


    <?php if(in_array('poster_file', $arr_fields)){?>
    <div class="form-group">
<?php 
if(!$register->isNewRecord && $register->poster_file){
echo Html::a('<i class="bi bi-file-earmark-pdf"></i> Uploaded Poster' , Url::to(['/program/download-poster-file','id' => $register->id]), ['target' => '_blank']);
}
?>
</div>
<?= $form->field($register, 'poster_instance')->fileInput() ?>
<i>(Allowed formats: PDF, PPTX, JPG, JPEG, PNG)</i>
<?php } ?>

<br /><br />

<?php if(in_array('video_link', $arr_fields)){?>
    <div class="form-group">
<?= $form->field($register, 'video_link')->textInput(['placeholder' => 'Paste YouTube / Google Drive link here']) ?>
<i>(Upload your video to YouTube/Google Drive and paste the link here)</i>
    </div>
<?php } ?>

<br /><br />

</div>

<?php if(in_array('payment_file', $arr_fields)){?>
    <div class="form-group">
<?php 
if(!$register->isNewRecord && $register->payment_file){
echo Html::a('<i class="bi bi-file-earmark-pdf"></i> Uploaded Proof of Payment' , Url::to(['/program/download-payment-file','id' => $register->id]), ['target' => '_blank']);
}
?>
</div>
<?= $form->field($register, 'payment_instance')->fileInput() ?>
<?php if($model->payment_short){ ?>
<i><?=$model->payment_short?></i>
<br /><br />
<?php 
} 

}




?>



      <?php if(!$demo && !$publicMode && (!$edit || (int)$register->status === 0)){?>
      <div class="col-12">

      <?= Html::submitButton('Save as Draft', ['class' => 'btn btn-warning', 'name' => 'action', 'value' => 'draft']) ?>

        <?= Html::submitButton('Submit Registration', ['class' => 'btn btn-primary', 'name' => 'action', 'value' => 'submit']) ?>

      </div> 
      <?php } 
      
      if($publicMode && !$edit){
        echo Html::submitButton('Submit Registration', ['class' => 'btn btn-primary', 'name' => 'action', 'value' => 'submit']);
      }

      if($edit && ($publicMode || (int)$register->status !== 0)){
        //echo '<input type="hidden" name="edit" value="1" />';
        echo Html::submitButton('Update', ['class' => 'btn btn-primary', 'name' => 'action', 'value' => 'update']);
      }
      
      ?>
      <br />



      <?php ActiveForm::end(); 
      
      ?>

                </div>
              </div>

<?php } ?>

<?php if($model->payment_info && $register->status == 0){ ?>
<div class="card">
<div class="card-header">Payment Guideline</div>
                      <div class="card-body pt-4">
        <?=$model->payment_info?>
        </div>
    </div>

    <?php } ?>


             

<?php

$js = <<<'EOD'


jQuery(".dynamicform_wrapper").on("afterInsert", function(e, item) {
    $(item).find("input, textarea").each(function() {
        $(this).val("");
    });
});



EOD;


$this->registerJs($js);


if(in_array('participant_cat_program',$arr_fields) && in_array('participant_mode',$arr_fields)){
  $catPhysical = $register->listParticipantCatProgram($register->program_id, 1);
  $catOnline = $register->listParticipantCatProgram($register->program_id, 2);

  $catsByMode = [
    1 => $catPhysical,
    2 => $catOnline,
  ];

  $jsonCatsByMode = json_encode($catsByMode);

  $jsCat = <<<JS
(function(){
  var catsByMode = $jsonCatsByMode;
  var modeName = 'ProgramRegistration[participant_mode]';
  var catId = 'programregistration-participant_cat_program';
  var catEl = document.getElementById(catId);

  if(!catEl){
    return;
  }

  function selectedMode(){
    var checked = document.querySelector('input[name="' + modeName + '"]:checked');
    return checked ? checked.value : '';
  }

  function rebuildOptions(mode){
    var currentValue = catEl.value;
    var map = (mode && catsByMode[mode]) ? catsByMode[mode] : {};

    while (catEl.options.length > 0) {
      catEl.remove(0);
    }

    catEl.options.add(new Option('Select Category', ''));

    var hasCurrent = false;
    Object.keys(map).forEach(function(key){
      var opt = new Option(map[key], key);
      if(String(key) === String(currentValue)){
        opt.selected = true;
        hasCurrent = true;
      }
      catEl.options.add(opt);
    });

    if(!hasCurrent){
      catEl.value = '';
    }
  }

  document.addEventListener('change', function(e){
    if(e.target && e.target.name === modeName){
      rebuildOptions(selectedMode());
    }
  });

  rebuildOptions(selectedMode());
})();
JS;

  $this->registerJs($jsCat);
}


if(in_array('participant_mode',$arr_fields) && (in_array('poster_file',$arr_fields) || in_array('video_link',$arr_fields))){
  $mode = (string)$register->participant_mode;
  $jsOnline = <<<JS
(function(){
  var modeName = 'ProgramRegistration[participant_mode]';
  var box = document.getElementById('online-mode-only');
  if(!box){
    return;
  }
  function selectedMode(){
    var checked = document.querySelector('input[name="' + modeName + '"]:checked');
    return checked ? checked.value : '';
  }
  function toggle(){
    var mode = selectedMode();
    box.style.display = (String(mode) === '2') ? '' : 'none';
  }
  document.addEventListener('change', function(e){
    if(e.target && e.target.name === modeName){
      toggle();
    }
  });
  toggle();
})();
JS;
  $this->registerJs($jsOnline);
}

?>
