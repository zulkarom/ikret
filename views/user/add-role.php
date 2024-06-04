<?php

use app\models\Committee;
use app\models\Program;
use app\models\UserRole;
use yii\helpers\Html;
//use yii\bootstrap5\ActiveForm;
use kartik\widgets\ActiveForm;
use wbraganca\dynamicform\DynamicFormWidget;
use yii\helpers\Url;
$web = Yii::getAlias('@web');

$this->title = 'Request for Additional User Role';

?>



    <div class="d-flex justify-content-center py-4">
                <a href="index.html" class="logo d-flex align-items-center w-auto">
          
                  <span class="d-none d-lg-block"><?=$this->title?></span>
                </a>
              </div><!-- End Logo -->



              <div class="card">
              <div class="card-header">Additional User Role Form</div>
                      <div class="card-body pt-4">



                      <?php $form = ActiveForm::begin(); ?>

<?= $form->field($model, 'role_name')->dropDownList($model->listRolesRequest(), ['prompt' => 'Select Role']) ?>

<div style="display:none" id="con-program">
<?= $form->field($model, 'program_id')->dropDownList(Program::listPrograms(), ['prompt' => 'Select Program','onchange'=>'
                         $.get("'.Url::to(['/user/sub-program-options', 'program' => '']).
                       '"+$(this).val(),function( data ) 
                        {$( "select#userrole-program_sub" ).html( data );
                             });']) ?>
</div>

<div style="display:none" id="con-committee">
<?= $form->field($model, 'committee_id')->dropDownList(Committee::listCommittees(), ['prompt' => 'Select Committee']) ?>
</div>

<div style="display:none" id="con-leader">
<?= $form->field($model, 'is_leader')->dropDownList(UserRole::listCommitteeRoles(), ['prompt' => 'Select Role']) ?>
</div>

<div style="display:none" id="con-program-sub">
<?= $form->field($model, 'program_sub')->dropDownList(['']) ?>
</div>

<?php 

//prepare arr jawatankuasa
$str_arr = '[';
$jw = Committee::find()->where(['is_jawatankuasa' => 1])->all();
$i=1;
foreach($jw as $j){
  $comma = $i == 1 ? '':',';
  $str_arr .= $comma.$j->id;
  $i++;
}
$str_arr .= ']';

//prepare arr for sub program
$str_sub = '[';
$sb = Program::find()->where(['has_sub' => 1])->all();
$i=1;
foreach($sb as $s){
  $comma = $i == 1 ? '':',';
  $str_sub .= $comma.$s->id;
  $i++;
}
$str_sub .= ']';

$this->registerJs('
$("#userrole-role_name").change(function(){
  var val = $(this).val();
  if(val == "manager"){
      $("#con-program").show();
      //kena check lg sub
  }else{
    $("#con-program-sub").hide();
      $("#con-program").hide();
  }
  if(val == "committee"){
    $("#con-committee").show();
  }else{
      $("#con-committee").hide();
  }

});

$("#userrole-program_id").change(function(){
  //console.log("chnage");
  const arr_sb = '.$str_sub.';
var comm = $(this).val();
//console.log(comm);
//console.log(arr_sb.includes(comm));
if(arr_sb.includes(parseInt(comm))){
 // console.log("show");
  $("#con-program-sub").show();
}else{
  $("#con-program-sub").hide();
}
});


$("#userrole-committee_id").change(function(){
  //console.log("chnage");
  const arr_jw = '.$str_arr.';
var comm = $(this).val();
//console.log(comm);
//console.log(arr_jw.includes(comm));
if(arr_jw.includes(parseInt(comm))){
 // console.log("show");
  $("#con-leader").show();
}else{
  $("#con-leader").hide();
}
});

');


?>




<div class="form-group">
    
<?= Html::submitButton('Request', ['class' => 'btn btn-primary']) ?>
</div>

<?php ActiveForm::end(); ?>


                          
                      </div>
                  </div>
  

<div class="card">
<div class="card-header">Current User Role</div>
    <div class="card-body pt-4">

    <table class="table">
        <tbody>
            <tr><th>No.</th><th>Role Name</th><th>Status</th><th></th></tr>
            <?php 

if($roles){
  $i = 1;
  foreach($roles as $r){
    echo '<tr><td>'.$i.'. </td><td>'.$r->roleText;
    if($r->role_name == 'manager'){
      if($r->program){
        $sub = '';
        if($r->programSub){
          $sub = '/' . $r->programSub->sub_name;
        }
        echo '<br />('.$r->program->program_abbr. $sub . ')';
      }
    }
    if($r->role_name == 'committee'){
      if($r->committee){
        echo '<br />('.$r->committee->com_name.')';
        if($r->committee->is_jawatankuasa == 1){
          if($r->is_leader == 1){
            echo '<b> - Leader</b>';
          }else{
            echo '<b> - Member</b>';
          }
          
        }
      }
    }
    echo '</td><td>'.$r->statusLabel.'</td><td><a href="'.Url::to(['/user/remove-role', 'id' => $r->id]).'" data-confirm="Are sure to delete this user role?"><i class="bi bi-trash"></i></a></td></tr>';
    $i++;
  }
}


?>
            
        </tbody>
    </table>

        
    </div>
</div>