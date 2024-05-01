<style type="text/css">
  
.center {
  padding: 200px 0;
  text-align: center;
}
.p-t-136 {
    padding-top: 30px !important;
}

.wrap-login100 {
    padding: 50px 130px 33px 95px !important;
}
</style>

<?php

use yii\helpers\Html;
use kartik\widgets\ActiveForm;
use yii\helpers\Url;
use common\models\Common;
use frontend\models\Alert;


$web = Yii::$app->assetManager->getPublishedUrl('@backend/assets/web');

$this->title = 'Sign Up';
$this->params['breadcrumbs'][] = $this->title;

?>


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
                       


    
                  
                        </div>
                    </div>
           
                </div>
            </div>
        </header>


