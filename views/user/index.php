
<?php 

use kartik\form\ActiveForm;
use yii\helpers\Html;
?>


<div class="pagetitle">
<h1>Profile</h1>

    </div><!-- End Page Title -->

    <section class="section dashboard">


    

           

<div class="card mb-3">

                <div class="card-body pt-4">

        
             
                 
                  <?php


 $form = ActiveForm::begin(); ?>

                    <div class="col-12">

                    <?= $form
            ->field($model, 'fullname')->textInput(['style' => 'text-transform: uppercase'])?>
            </div>
            
            <div class="col-12">
            <?= $form
            ->field($model, 'email')
      
            ->textInput() ?>
            </div>
            <div class="col-12">
            <?= $form
            ->field($model, 'phone')
      
            ->textInput() ?>
            </div>

            <div class="col-12">
            <?= $form
            ->field($model, 'matric')
      
            ->textInput() ?>
            </div>

            <div class="col-12">
            <?= $form
            ->field($model, 'institution')
      
            ->textInput() ?>
            </div>

            <?= $form->field($model, 'is_internal')->dropDownList($model->listIsInternal())->label('Category (Internal)')?>
    <?= $form->field($model, 'is_student')->dropDownList($model->listIsStudent())->label('Category (Student)')?>

 


            
                    <div class="col-12">
          
                      <?= Html::submitButton('Save Profile', ['class' => 'btn btn-primary', 'name' => 'login-button']) ?>
                    </div>
                    <br />
   
                
                    <?php ActiveForm::end(); ?>

                </div>
              </div>    </section>