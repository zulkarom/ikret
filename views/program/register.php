<?php
use yii\helpers\Html;
//use yii\bootstrap5\ActiveForm;
use kartik\widgets\ActiveForm;
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

              <div class="card mb-3">

                <div class="card-body">

                  <div class="pt-4 pb-2">

                  <?=$model->reg_info?>
                <br />
                    <p class="small">Enter your project details to register in this program. You will be the group leader in this project.</p>
                  </div>

             
                 
                  <?php $form = ActiveForm::begin(['class' => 'row g-3 needs-validation']); ?>

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

            
                    <div class="col-12">
                    <?= Html::submitButton('Save as Draft', ['class' => 'btn btn-warning', 'name' => 'action-button', 'value' => 'draft']) ?>
                      <?= Html::submitButton('Submit Registration', ['class' => 'btn btn-primary', 'name' => 'action-button', 'value' => 'submit']) ?>
                    </div> 
                    <br />
   
       
                    <?php ActiveForm::end(); ?>

                </div>
              </div>





    </div>
    
</div>
             