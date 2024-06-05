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

$this->title = 'Registration - ' . $model->program_name;

?>
    <div class="d-flex justify-content-center py-4">
                <a href="index.html" class="logo d-flex align-items-center w-auto">
          
                  <span class="d-none d-lg-block"><?=$model->program_name?></span>
                </a>
              </div><!-- End Logo -->
              <?php $arr_fields = $register->getProgramFields($register->program_id);?>

              <?=$this->render('_view_register', [    
        'register' => $register,
        'arr_fields' => $arr_fields
    ]);
    ?>

              <div class="card">
              <div class="card-header">Program Information</div>
                      <div class="card-body pt-4">
                          <?=$model->reg_info?>
                      </div>
                  </div>

                  <?php if($register->status == 0){?>
              <div class="card mb-3">
              <div class="card-header">Registration Form</div>
                <div class="card-body">

                  <div class="pt-4 pb-2">

                    <p class="small">Enter your project details to register in this program.</p>
                  </div>

                  <?php $form = ActiveForm::begin(['class' => 'row g-3 needs-validation','id' => 'dynamic-form', 'action' => Url::to(['register']), 'options' => ['enctype' => 'multipart/form-data']]); ?>

                  <input type="hidden" name="program_id" value="<?=$model->id?>" />

                  <?php 
                  if(!$register->isNewRecord && !$err){
                      echo '<input type="hidden" name="reg_id" value="'.$register->id.'" />';
                  }
                  ?>

                   

                    <div class="col-12">

                    <?php 
                    
                    if(in_array('project_name',$arr_fields)){
                      echo $form
                      ->field($register, 'project_name')->textarea(['rows' => 2]);
                    }
                    
            ?>
            </div>

            <div class="col-12">

                    <?php 
                    if(in_array('project_desc',$arr_fields)){
                    echo $form
            ->field($register, 'project_desc')->textarea(['rows' => 4]);
                    }
                    ?>
            </div>

            <div class="col-12">

            <?php 
                    if(in_array('participant_cat_local',$arr_fields)){
                    echo $form
            ->field($register, 'participant_cat_local')->radioList($register->listParticipantLocal());
        }
          ?>
            </div>

           

            <div class="col-12">

            <?php 
                    if(in_array('participant_cat_group',$arr_fields)){
                    echo $form
            ->field($register, 'participant_cat_group')->radioList($register->listParticipantGroup());
        }
          ?>
            </div>

            

            <div class="col-12">

            <?php 
                    if(in_array('competition_type',$arr_fields)){
                    echo $form
            ->field($register, 'competition_type')->radioList([
              1 => 'Community Project Ideation', 
              2 => 'Community Project Implementation'
          ]);
        }
          ?>
            </div>

            <div class="col-12">

            <?php 
                    if(in_array('program_sub',$arr_fields)){
                    echo $form
            ->field($register, 'program_sub')->dropDownList($register->program->listSubPrograms(),['prompt' => 'Select Category']);
        }
          ?>
            </div>



            <?php 
                    if(in_array('advisor_dropdown',$arr_fields)){
                    echo $form
            ->field($register, 'advisor_dropdown')->dropDownList($register->listNeweekAdvisor(), ['prompt' => 'Selct Lecturer']);
        }
          ?>


<?php 
        if(in_array('booth_number',$arr_fields)){
        echo $form
->field($register, 'booth_number')->dropDownList($register->listNeweekBooth(), ['prompt' => 'Select Booth']);
}
?>

            
           


<?php 
                    if(in_array('nric',$arr_fields)){
                  echo '<div class="col-12">';
                    echo $form
->field($register, 'nric')->textInput();
echo '</div>';

                    } ?>


<?php 
        if(in_array('participant_mode',$arr_fields)){
        echo $form
->field($register, 'participant_mode')->radioList($register->listParticipantMode());
}
?>



<?php /* 
                    if(in_array('competition_type',$arr_fields)){
                    echo $form
->field($register, 'competition_type')->textInput();
                    } */?>

<?php 
        if(in_array('participant_cat_umk',$arr_fields)){
        echo $form
->field($register, 'participant_cat_umk')->radioList($register->listParticipantUMK());
}
?>




<?php 
        if(in_array('participant_program',$arr_fields)){
        echo $form
->field($register, 'participant_program')->radioList($register->listParticipantProgram());
echo '<input class="form-control" name="ProgramRegistration[other_program]" placeholder="Specify other program..." /><br />';
}
?>


<?php 
                    if(in_array('advisor',$arr_fields)){

                    echo $form
->field($register, 'advisor')->textInput();

                    } ?>


<?php 
                    if(in_array('institution',$arr_fields)){
                  echo '<div class="col-12">';
                    echo $form
->field($register, 'institution')->textInput();
echo '</div>';

                    } ?>



<?php 
if(in_array('group_member',$arr_fields)){
  $register->group_member = 1;
  $show_group = '';
}else{
  $show_group = 'style="display:none"';
}
  ?>





  <div class="col-12" <?=$show_group?>>
<br />
<?php DynamicFormWidget::begin([
        'widgetContainer' => 'dynamicform_wrapper',
        'widgetBody' => '.container-items',
        'widgetItem' => '.member-item',
        'limit' => 10,
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
                <th width="25%">Matric No.</th>
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
                
                <td class="vcenter">
                <?= $form->field($member, "[{$i}]member_matric")->label(false) ?>

                </td>

                <td class="text-center vcenter" style="width: 90px;">
                    <button type="button" class="remove-member btn btn-default btn-sm"><span class="bi bi-trash"></span></button>
                </td>
            </tr>
         <?php endforeach; ?>
        </tbody>
        
        <tfoot>
            <tr>
   
                <td colspan="2">
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
                  




                    } ?></div>
      <div class="col-md-6"><?php 
                    if(in_array('mentor_co',$arr_fields)){


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




                    } ?></div>
  </div>
<i>* Try to search your mentor, if not found, you need to ask your mentor to register to the system as mentor.</i>



    <?php if(in_array('poster_file', $arr_fields)){?>
    <br /><br />
    <div class="form-group">
<?php 
if(!$register->isNewRecord && $register->poster_file){
echo Html::a('<i class="bi bi-file-earmark-pdf"></i> Uploaded Poster' , Url::to(['download-poster-file','id' => $register->id]), ['target' => '_blank']);
}
?>
</div>
<?= $form->field($register, 'poster_instance')->fileInput() ?>
<i>(Please upload poster in PDF format only)</i>
<?php } ?>
<br /><br />

<?php if(in_array('payment_file', $arr_fields)){?>
    <div class="form-group">
<?php 
if(!$register->isNewRecord && $register->payment_file){
echo Html::a('<i class="bi bi-file-earmark-pdf"></i> Uploaded Proof of Payment' , Url::to(['download-payment-file','id' => $register->id]), ['target' => '_blank']);
}
?>
</div>
<?= $form->field($register, 'payment_instance')->fileInput() ?>
<?php if($model->payment_short){ ?>
<i><?=$model->payment_short?></i>
<br /><br />
<?php 
} 

}?>



      <?php if(!$demo){?>
      <div class="col-12">
      <?= Html::submitButton('Save as Draft', ['class' => 'btn btn-warning', 'name' => 'action', 'value' => 'draft']) ?>
        <?= Html::submitButton('Submit Registration', ['class' => 'btn btn-primary', 'name' => 'action', 'value' => 'submit']) ?>
      </div> 
      <?php } ?>
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
    var first = $(item).find("input")[0];
    first.setAttribute("value", "");
    var second = $(item).find("input")[1];
    second.setAttribute("value", "");
    var third = $(item).find("input")[2];
    third.setAttribute("value", "");
});



EOD;


$this->registerJs($js);
?>
