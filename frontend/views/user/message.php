<?php
use yii\helpers\Url;
$this->title = $title;
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
                 
                        <br /> <br /> 
                       
                          <?= $this->render('/_alert', ['module' => $module])?>


    
                  
                        </div>
                    </div>
           
                </div>
            </div>
        </header>






