<?php
use yii\helpers\Html;
use kartik\widgets\ActiveForm;
use common\models\Common;
use frontend\models\Alert;

$this->title = 'HubSoE | Log Masuk';
$web = Yii::$app->assetManager->getPublishedUrl('@backend/assets/web');

?>
  <style>

.form-group{
margin-bottom:14px;

}
.btn-block {
  display: block;
  width: 100%;
  background-color:#2937f0;
}

.form-group.required .has-star:not(.custom-control-label):not(.custom-file-label)::after,
.is-required::after {
    content: "*";
    margin-left: 3px;
    font-weight: normal;
    font-family: SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;
    color: tomato;
}

</style>
 <header class="masthead">
            <div class="container px-5">
                <div class="row gx-5 align-items-center">
                  <div class="col-lg-4"></div>
                    <div class="col-lg-4">
                        <!-- Mashead text and app badges-->
                         
                        
                        <div class="mb-5 mb-lg-0 text-lg-start">
                        
                         <div class="text-center">  <img src="<?=$web?>/images/logo_umk_hubsoe.png" style="max-width: 100%"/>
                        <br /> <br /> 
                       
                            <h2 class="lh-1 mb-3">Log Masuk</h2>
                        </div>
                       
                            
                              <?= Alert::widget() ?>
                            
       <?php $form = ActiveForm::begin(['id' => 'login-form']); ?>
                    <?php 
                    if(!Yii::$app->getRequest()->post()){
                        $model->role = 1;
                    }
                    
                    
                    echo $form->field($model, 'role')->dropDownList(Common::role(), ['prompt' => 'Select User Category'])->label(false)
                    
                        ?>
                        
                        
                        
                        
                    <?= $form->field($model, 'login')->textInput(['placeholder' => $model->getAttributeLabel('login')])->label(false)
                    ?>
                    <?= $form->field($model, 'password')->passwordInput(['placeholder' => 'Password'])->label(false)
                        ?>
                        
                        
                        
                    <div class="form-group">
                        <?= Html::submitButton('Login', ['class' => 'btn btn-primary btn-block', 'name' => 'submit']) ?>
                    </div>

             

             
                    <?php ActiveForm::end(); ?>
                            
                            
                            
                            
                            
                              
                               
                  
                        </div>
                    </div>
           
                </div>
            </div>
        </header>
