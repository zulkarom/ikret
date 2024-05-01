
<?php

use yii\helpers\Html;
use kartik\widgets\ActiveForm;
use common\models\Common;
use frontend\models\Alert;


$this->title = 'Sign Up';
$this->params['breadcrumbs'][] = $this->title;


$web = Yii::$app->assetManager->getPublishedUrl('@backend/assets/web');
?>



 <style>

.form-group{
margin-bottom:14px;
}
.form-group.required .has-star:not(.custom-control-label):not(.custom-file-label)::after,
.is-required::after {
    content: "*";
    margin-left: 3px;
    font-weight: normal;
    font-family: SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;
    color: tomato;
}
label {
    display: inline-block;
    font-size:14px;
}
.btn-block {
  display: block;
  width: 100%;
  background-color:#2937f0;
}
.btn-submit{
margin-top:20px;
}
.btn-kembali{
margin-top:20px;
}
</style>
 <header class="masthead">
            <div class="container px-5">
                <div class="row gx-5 align-items-center">
                  <div class="col-lg-2"></div>
                    <div class="col-lg-8">
                        <!-- Mashead text and app badges-->
                         
                        
                           <div class="mb-5 mb-lg-0 text-lg-start">
                        
                        <div class="text-center"> 
                        
                        <img src="<?=$web?>/images/logo_umk_hubsoe.png" style="max-width: 100%"/>
                        <br /> <br /> 
                       
                            <h2 class="lh-1 mb-3">Pendaftaran</h2>
                        
                        </div>
                        
                         <?= Alert::widget() ?>
                       
                                       <?php $form = ActiveForm::begin([
                    'id' => 'signup-form',
                ]); ?>
                
               <div class="row">
                  <div class="col-lg-6"><?= $form->field($model, 'fullname')->textInput()
                        ?></div>
                    <div class="col-lg-6">  <?= $form->field($model, 'email')->textInput()
                        ?>
                        </div>
                    </div>
                
                
                <div class="row">
                  <div class="col-lg-6"> <?= $form->field($model, 'role')->dropDownList(Common::role(), ['disabled' => 'disabled'])
                        ?></div>
                    <div class="col-lg-6">  <?= $form->field($model, 'username')->textInput(['disabled' => 'disabled'])
                        ?>
                        </div>
                    </div>
                 
                        
                       
                        <div class="row">
                  <div class="col-lg-6">  <?= $form->field($model, 'password')->passwordInput()
                        ?></div>
                    <div class="col-lg-6">   <?= $form->field($model, 'password_repeat')->passwordInput()
                        ?>
                        </div>
                    </div>
                     
                
                    <div class="form-group">
                    
                    <?= Html::a('<i class="bi-arrow-left"></i> Kembali', ['/user-register/register'], ['class' => 'btn btn-warning btn-kembali']) ?>
                        <?= Html::submitButton('Daftar', ['class' => 'btn btn-primary btn-submit', 'name' => 'submit']) ?>
                    </div>

                    
        

                    <?php ActiveForm::end(); ?>


    
                  
                        </div>
                    </div>
           
                </div>
            </div>
        </header>





