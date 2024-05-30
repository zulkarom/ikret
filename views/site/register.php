<?php
use yii\helpers\Html;
//use yii\bootstrap5\ActiveForm;
use kartik\widgets\ActiveForm;
use yii\helpers\Url;
$web = Yii::getAlias('@web');

$this->title = 'I-CREATE - Register';

?>

<div class="row">
<div class="col-md-3"></div>
    <div class="col-md-5">


    <div class="d-flex justify-content-center py-4">
                <a href="index.html" class="logo d-flex align-items-center w-auto">
          
                  <span class="d-none d-lg-block">I-CREATE Registration Form</span>
                </a>
              </div><!-- End Logo -->

              <div class="card mb-3">

                <div class="card-body">

                  <div class="pt-4 pb-2">
                    <h5 class="card-title text-center pb-0 fs-4">Create an Account</h5>
                    <p class="text-center small">Enter your personal details to create account</p>
                  </div>

             
                  <?= $this->render('_register_form', [
        'model' => $model,
    ]) ?>

                </div>
              </div>





    </div>
    
</div>

<?php 
$this->registerJs('

$("#registerform-is_internal").change(function(){
  var val = $(this).val();
  if(val == 1){
    $("#con-matric").show();
    $("#con-institution").hide();
  }else if(val == 2){
    $("#con-matric").hide();
    $("#con-institution").show();
  }

});

');

?>