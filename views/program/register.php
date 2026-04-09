<?php

use app\models\Mentor;
use app\models\ParticipantCatProgram;
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
$participantCategoryFee = null;

if(!empty($register->participant_cat_program)){
    $participantCategory = ParticipantCatProgram::findOne((int)$register->participant_cat_program);
    if($participantCategory && !empty($participantCategory->fee)){
        $participantCategoryFee = $participantCategory->fee;
    }
}

$this->title = 'Registration - ' . $model->program_name;

$this->registerCss(<<<CSS
.register-select-arrow {
    appearance: none;
    -webkit-appearance: none;
    -moz-appearance: none;
    background-image: linear-gradient(45deg, transparent 50%, #4154f1 50%), linear-gradient(135deg, #4154f1 50%, transparent 50%);
    background-position: calc(100% - 18px) calc(50% - 3px), calc(100% - 12px) calc(50% - 3px);
    background-size: 6px 6px, 6px 6px;
    background-repeat: no-repeat;
    padding-right: 2.5rem;
}

.register-select-arrow:focus {
    background-image: linear-gradient(45deg, transparent 50%, #4154f1 50%), linear-gradient(135deg, #4154f1 50%, transparent 50%);
}
CSS);

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
->field($register, 'participant_cat_program')->dropDownList($register->listParticipantCatProgram($register->program_id, $register->participant_mode), ['prompt' => 'Select Category', 'class' => 'form-select register-select-arrow']);
        echo '</div>';
}
?>


<?php 
        if(in_array('competition_cat_program',$arr_fields)){
        echo '<div class="' . $fieldColClass('competition_cat_program') . '">';
        echo $form
->field($register, 'competition_cat_program')->dropDownList($register->listCompetitionCatProgram($register->program_id), ['prompt' => 'Select Category', 'class' => 'form-select register-select-arrow']);
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
                    <div class="col-12">
                    <i>Please keep this Password / PIN safe. You will need it later to edit your registration and for certificate-related access.</i>
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
    <div class="mt-4 pt-2">
    <hr class="mb-3"/>
    <h5 class="fw-bold mb-3">Abstract</h5>
    <i>Abstract must be submitted in Microsoft Word format (.doc or .docx only).</i>
    <div class="mt-3">It should include the following key elements:</div>
    <ul class="mb-3">
        <li>Title</li>
        <li>Problem Statement</li>
        <li>Objective</li>
        <li>Target User</li>
        <li>Features/Impact</li>
        <li>Conclusion</li>
    </ul>
    <div class="mb-3">Submitted abstracts will be included in the event’s e-proceedings publication.</div>
    <div class="form-group">
<?php 
if(!$register->isNewRecord && $register->abstract_file){
echo Html::a('<i class="bi bi-file-earmark-text"></i> Uploaded Abstract' , Url::to(['/program/download-abstract-file','id' => $register->id]), ['target' => '_blank']);
}
?>
</div>
<?= $form->field($register, 'abstract_instance')->fileInput() ?>
    </div>
<?php } ?>

<br /><br />


<div id="online-mode-only">


    <?php if(in_array('poster_file', $arr_fields)){?>
    <div class="mt-4 pt-2">
    <hr class="mb-3"/>
    <h5 class="fw-bold mb-3">Poster</h5>
    <i>Poster must be in A2 size and submitted in PDF, PPTX, JPG, JPEG, or PNG format.</i>
    <div class="mt-3">The poster must clearly include the following content elements:</div>
    <ul class="mb-3">
        <li>Background</li>
        <li>Problem Statement</li>
        <li>Objectives</li>
        <li>Methodologies</li>
        <li>Impacts</li>
        <li>Project Visualisation / Potential Application/ Commercialisation/ Photo</li>
        <li>Achievement/ Recognition/ Award</li>
    </ul>
    <div>The same approved design must be used for physical printing during the event.</div>
    <div class="mt-2 mb-3">Participants are responsible for printing and bringing their poster unless otherwise stated by the organiser.</div>
    <div class="form-group">
<?php 
if(!$register->isNewRecord && $register->poster_file){
echo Html::a('<i class="bi bi-file-earmark-pdf"></i> Uploaded Poster' , Url::to(['/program/download-poster-file','id' => $register->id]), ['target' => '_blank']);
}
?>
</div>
<?= $form->field($register, 'poster_instance')->fileInput() ?>
    </div>
<?php } ?>

<br /><br />

<?php if(in_array('video_link', $arr_fields)){?>
    <div class="mt-4 pt-2">
<hr class="mb-3"/>
    <h5 class="fw-bold mb-3">Video</h5>
    <i>(Upload your video to YouTube/Google Drive and paste the link here)</i>
    <div class="mt-3">Each submission must include a presentation video between 3 to 5 minutes in length.</div>
    <div class="mt-3">The content of the video must clearly present the following:</div>
    <ul class="mb-3">
        <li>Background</li>
        <li>Problem Statement</li>
        <li>Objective</li>
        <li>Methodology</li>
        <li>Innovation Impact / Benefit</li>
        <li>Conclusion</li>
    </ul>
    <div class="form-group">
<?= $form->field($register, 'video_link')->textInput(['placeholder' => 'Paste YouTube / Google Drive link here']) ?>
    </div>
    </div>
<?php } ?>

<br /><br />

</div>

<?php if(in_array('payment_file', $arr_fields)){?>
    <div class="mt-4 pt-2">
<hr class="mb-3"/>
    <h5 class="fw-bold mb-3">Proof of Payment</h5>
<?php if($model->payment_short){ ?>
<i><?=$model->payment_short?></i>
<div class="mb-3"></div>
<?php 
} 


if($participantCategoryFee){
echo '<div id="required-payment-amount" class="mb-3"><strong>Required Amount:</strong> ' . Html::encode($participantCategoryFee) . '</div>';
}else{
echo '<div id="required-payment-amount" class="mb-3" style="display:none;"></div>';
}


echo '<div><strong>Account Holder Name:</strong> Universiti Malaysia Kelantan</div>';
echo '<div><strong>Account Number:</strong> 553038019271</div>';
echo '<div><strong>Bank Name:</strong> Maybank Berhad</div>';
echo '<div><strong>SWIFT Code:</strong> MBBEMYKL</div><br />';
echo '<div><strong>Reference:</strong> IISEIC2026</div><br />';
?>
    <div class="form-group">
<?php 
if(!$register->isNewRecord && $register->payment_file){
echo Html::a('<i class="bi bi-file-earmark-pdf"></i> Uploaded Proof of Payment' , Url::to(['/program/download-payment-file','id' => $register->id]), ['target' => '_blank']);
}
?>
</div>
<?= $form->field($register, 'payment_instance')->fileInput() ?>
    </div>

<?php } ?>







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
  $catPhysical = ParticipantCatProgram::find()
    ->where(['program_id' => $register->program_id, 'mode' => 1, 'is_active' => 1])
    ->orderBy(['sort_order' => SORT_ASC, 'id' => SORT_ASC])
    ->all();
  $catOnline = ParticipantCatProgram::find()
    ->where(['program_id' => $register->program_id, 'mode' => 2, 'is_active' => 1])
    ->orderBy(['sort_order' => SORT_ASC, 'id' => SORT_ASC])
    ->all();

  $catsByMode = [
    1 => array_reduce($catPhysical, function($carry, $item){
      $carry[$item->id] = ['label' => $item->cat_name, 'fee' => $item->fee];
      return $carry;
    }, []),
    2 => array_reduce($catOnline, function($carry, $item){
      $carry[$item->id] = ['label' => $item->cat_name, 'fee' => $item->fee];
      return $carry;
    }, []),
  ];

  $jsonCatsByMode = json_encode($catsByMode);

  $jsCat = <<<JS
(function(){
  var catsByMode = $jsonCatsByMode;
  var modeName = 'ProgramRegistration[participant_mode]';
  var catId = 'programregistration-participant_cat_program';
  var catEl = document.getElementById(catId);
  var amountEl = document.getElementById('required-payment-amount');

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
      var opt = new Option(map[key].label, key);
      if(String(key) === String(currentValue)){
        opt.selected = true;
        hasCurrent = true;
      }
      catEl.options.add(opt);
    });

    if(!hasCurrent){
      catEl.value = '';
    }

    updateAmount();
  }

  function updateAmount(){
    if(!amountEl){
      return;
    }

    var mode = selectedMode();
    var map = (mode && catsByMode[mode]) ? catsByMode[mode] : {};
    var selected = catEl.value && map[catEl.value] ? map[catEl.value] : null;

    if(selected && selected.fee){
      amountEl.innerHTML = '<strong>Required Amount:</strong> ' + selected.fee;
      amountEl.style.display = '';
    }else{
      amountEl.innerHTML = '';
      amountEl.style.display = 'none';
    }
  }

  document.addEventListener('change', function(e){
    if(e.target && e.target.name === modeName){
      rebuildOptions(selectedMode());
    }
    if(e.target && e.target.id === catId){
      updateAmount();
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
