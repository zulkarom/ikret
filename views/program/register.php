<?php
use yii\helpers\Html;
//use yii\bootstrap5\ActiveForm;
use kartik\widgets\ActiveForm;
use wbraganca\dynamicform\DynamicFormWidget;
use yii\helpers\Url;
$web = Yii::getAlias('@web');

$this->title = 'Registration - ' . $model->program_name;

?>

<div class="row">

    <div class="col-md-10">


    <div class="d-flex justify-content-center py-4">
                <a href="index.html" class="logo d-flex align-items-center w-auto">
          
                  <span class="d-none d-lg-block"><?=$model->program_name?></span>
                </a>
              </div><!-- End Logo -->


              <div class="card">
              <div class="card-header">Program Information</div>
                      <div class="card-body pt-4">
                          <?=$model->reg_info?>
                      </div>
                  </div>

              <div class="card mb-3">
              <div class="card-header">Registration Form</div>
                <div class="card-body">

                  <div class="pt-4 pb-2">

                    <p class="small">Enter your project details to register in this program. You will be the group leader in this project.</p>
                  </div>

    

                  <?php $form = ActiveForm::begin(['class' => 'row g-3 needs-validation','id' => 'dynamic-form', 'options' => ['enctype' => 'multipart/form-data']]); ?>

                    <div class="col-12">

                    <?= $form
            ->field($register, 'project_name')->textarea(['rows' => 2])?>
            </div>

            <div class="col-12">

                    <?= $form
            ->field($register, 'project_desc')->textarea(['rows' => 4])?>
            </div>

            <div class="col-12">

                    <?= $form
            ->field($register, 'competition_type')->radioList([
              1 => 'Community Project Ideation', 
              2 => 'Community Project Implementation'
          ]);
          ?>
            </div>
           
            <div class="col-12">

<?= $form
->field($register, 'institution')->textInput()?>
</div>
<br /><br />
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

    
<label class="form-label pt-0">Group Members:</label>
    <table class="table">
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
                    <?= $form->field($member, "[{$i}]member_name")->label(false) ?>
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

    <br /><br />
    <div class="form-group">
<?php 
if(!$register->isNewRecord && $register->poster_file){
echo Html::a('<i class="bi bi-file-earmark-pdf"></i> Uploaded Poster' , Url::to(['download-poster-file','id' => $register->id]));
}
?>
</div>
<?= $form->field($register, 'poster_instance')->fileInput() ?>


    <br />
    <div class="form-group">
<?php 
if(!$register->isNewRecord && $register->payment_file){
echo Html::a('<i class="bi bi-file-earmark-pdf"></i> Uploaded Proof of Payment' , Url::to(['download-payment_file','id' => $register->id]));
}
?>
</div>
<?= $form->field($register, 'payment_instance')->fileInput() ?>


                    <div class="col-12">
                    <?= Html::submitButton('Save as Draft', ['class' => 'btn btn-warning', 'name' => 'action', 'value' => 'draft']) ?>
                      <?= Html::submitButton('Submit Registration', ['class' => 'btn btn-primary', 'name' => 'action', 'value' => 'submit']) ?>
                    </div> 
                    <br />
   
       
                    <?php ActiveForm::end(); ?>

                </div>
              </div>





    </div>
    
</div>
             

<?php

$js = <<<'EOD'

jQuery(".dynamicform_wrapper").on("afterInsert", function(e, item) {
    $( ".krajee-datepicker" ).each(function() {
       $(this).removeData().kvDatepicker('destroy');
        $(this).kvDatepicker(eval($(this).attr('data-krajee-kvdatepicker')));
  });          
});



jQuery(".dynamicform_wrapper").on("afterInsert", function(e, item) {
    var first = $(item).find("input")[0];
    first.setAttribute("value", "");
});



EOD;


$this->registerJs($js);
?>