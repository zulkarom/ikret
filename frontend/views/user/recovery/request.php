<?php


use yii\helpers\Html;
use yii\widgets\ActiveForm;

/**
 * @var yii\web\View $this
 * @var yii\widgets\ActiveForm $form
 * @var dektrium\user\models\RecoveryForm $model
 */

$this->title = Yii::t('user', 'Recover your password');
$this->params['breadcrumbs'][] = $this->title;
$web = Yii::getAlias('@web');
?>



  <style>

.form-group{
margin-bottom:14px;

}

</style>
 <header class="masthead">
            <div class="container px-5">
                <div class="row gx-5 align-items-center">
                  <div class="col-lg-4"></div>
                    <div class="col-lg-4">
                        <!-- Mashead text and app badges-->
                         
                        
                        <div class="mb-5 mb-lg-0 text-center text-lg-start">
                        
                        <img src="<?=$web?>/images/logo_umk_hubsoe.png" style="max-width: 100%"/>
                        <br /> <br /> 
                       
                            <h2 class="lh-1 mb-3">Recover Your Password</h2>


               <?php $form = ActiveForm::begin([
                    'id' => 'password-recovery-form',
                    'enableAjaxValidation' => true,
                    'enableClientValidation' => false,
                ]); ?>

                <?= $form->field($model, 'email')->textInput(['autofocus' => true]) ?>

                <?= Html::submitButton(Yii::t('user', 'Continue'), ['class' => 'btn btn-primary btn-block']) ?><br>

                <?php ActiveForm::end(); ?>

                 
                               
                  
                        </div>
                    </div>
           
                </div>
            </div>
        </header>






