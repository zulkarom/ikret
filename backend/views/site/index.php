<?php
use backend\models\Entrepreneur;
use backend\models\Supplier;
use backend\models\Competency;
use backend\models\SocialImpact;
use backend\models\Economic;
use backend\models\Agency;
use backend\models\Program;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */

$this->title = 'Dashboard';
?>
<br />
<div class="row">
    <div class="col-md-1"></div>
    <div class="col-md-10">
    
<?php $form = ActiveForm::begin(['method' => 'get', 'action' => ['entrepreneur/index']]); ?>
    <div class="input-group mb-3">
  <input type="text" class="form-control form-control-lg" name="EntrepreneurSearch[fullname]" placeholder="Search Beneficiaries" aria-label="Search Beneficiaries" aria-describedby="basic-addon2">
  <div class="input-group-append">
    <button class="btn btn-outline-primary" type="submit"><i class="fa fa-search"></i> Search</button>
  </div>
</div>
<?php ActiveForm::end(); ?>

    </div>
</div>



    <div class="main_content_iner overly_inner ">
        <div class="container-fluid p-0 ">
            <!-- page title  -->
            <div class="row">
                <div class="col-12">
                    <div class="page_title_box d-flex flex-wrap align-items-center justify-content-between">
                        <div class="page_title_left">
          
                        </div>
                        <div class="monitor_list_widget">
                            <div class="simgle_monitor_list">
                                <div class="simgle_monitor_count d-flex align-items-center">
                                    <span>Number of Beneficiaries</span>
                                    <div id="monitor_1"></div>
                                </div>
                                <h4 class="counter"><?php echo Entrepreneur::countEntrepreneur()?></h4>
                            </div>
                            <div class="simgle_monitor_list">
                                <div class="simgle_monitor_count d-flex align-items-center">
                                    <span>Number of Suppliers</span>
                                    <div id="monitor_2"></div>
                                </div>
                                <h4 ><span class="counter"><?php echo Supplier::countSupplier()?></span> </h4>
                            </div>
                            <!-- <div class="simgle_monitor_list">
                                <div class="simgle_monitor_count d-flex align-items-center">
                                    <span>Purchase</span>
                                    <div id="monitor_3"></div>
                                </div>
                                <h4 >$ <span class="counter">451.6 </span>M </h4>
                            </div> -->
                        </div>
                    </div>
                </div>
            </div>
            <div class="row ">
                <div class="col-xl-12">
                    <div class="white_card  mb_30">

                        <div class="white_card_body anlite_table p-0">
                            <div class="row">
                                <div class="col-lg-1">
                                </div>
                                <div class="col-lg-2">
                                    <div class="single_analite_content">
                                        <h4>Number of Competency</h4>
                                        <h3><span class="counter"><?php echo Competency::countCompetency()?></span> </h3>
                                        <!-- <div class="d-flex">
                                            <div>3.78 <i class="fa fa-caret-up"></i></div>
                                            <span>This month</span>
                                        </div> -->
                                    </div>
                                </div>
                                <div class="col-lg-2">
                                    <div class="single_analite_content">
                                        <h4>Number of Social Impact</h4>
                                        <h3><span class="counter"><?php echo SocialImpact::countSocialImpact()?></span> </h3>
                                        <!-- <div class="d-flex">
                                            <div>3.78 <i class="fa fa-caret-up"></i></div>
                                            <span>This month</span>
                                        </div> -->
                                    </div>
                                </div>
                                <div class="col-lg-2">
                                    <div class="single_analite_content">
                                        <h4>Number of Economics</h4>
                                        <h3><span class="counter"><?php echo Economic::countEconomic()?></span> </h3>
                                        <!-- <div class="d-flex">
                                            <div>3.78 <i class="fa fa-caret-up"></i></div>
                                            <span>This month</span>
                                        </div> -->
                                    </div>
                                </div>
                                <div class="col-lg-2">
                                    <div class="single_analite_content">
                                        <h4>Number of Agency</h4>
                                        <h3><span class="counter"><?php echo Agency::countAgency()?></span> </h3>
                                        <!-- <div class="d-flex">
                                            <div>3.78 <i class="fa fa-caret-up"></i></div>
                                            <span>This month</span>
                                        </div> -->
                                    </div>
                                </div>
                                <div class="col-lg-2">
                                    <div class="single_analite_content">
                                        <h4>Number of Program</h4>
                                        <h3><span class="counter"><?php echo Program::countProgram()?></span> </h3>
                                        <!-- <div class="d-flex">
                                            <div>3.78 <i class="fa fa-caret-up"></i></div>
                                            <span>This year</span>
                                        </div> -->
                                    </div>
                                </div>
                                <div class="col-lg-1">
                                </div>
                            </div>   
                        </div>
                    </div>
                </div>
       
   
                
                
                
                
                
                
                
                
            </div>
        </div>
    </div>


