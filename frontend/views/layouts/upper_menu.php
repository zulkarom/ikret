<?php
use yii\helpers\Html;
use yii\helpers\Url;
?>

<div class="container-fluid no-gutters">
        <div class="row">
            <div class="col-lg-12 p-0 ">
                <div class="header_iner d-flex justify-content-between align-items-center">
                    <div class="sidebar_icon d-lg-none">
                        <i class="ti-menu"></i>
                    </div>
                    <div class="line_icon open_miniSide d-none d-lg-block">
                        <img src="<?= $dirAssests?>/img/line_img.png" alt="">
                    </div>
                    <div class="header_right d-flex justify-content-between align-items-center">
                        <div class="header_notification_warp d-flex align-items-center">
                        <ul>
                 
                    
                            <li>
                            
                            <?php 
                            //echo \Yii::$app->language;
                            $icon = 'en';
                            if(\Yii::$app->language == 'ms-my'){
                                $icon = 'ms-my';
                            }
                            
                            ?>
                            
                                <a class="bell_notification_clicker nav-link-notify" href="#"> <img src="<?= $dirAssests?>/img/icon/<?=$icon?>.svg" alt="">
                                    <!-- <span>2</span> -->
                                </a>
                                <!-- Menu_NOtification_Wrap  -->
                            <div class="Menu_NOtification_Wrap">
                                <div class="notification_Header">
                                    <h4><?=\Yii::t('app', 'Change Language')?></h4>
                                </div>
                                <div class="Notification_body">
                                
                                
                                    <!-- single_notify  -->
                                    <div class="single_notify d-flex align-items-center">
                                    
                                    
                                        <div class="notify_thumb">
                                            <a href="javascript:void(0)"><img src="<?= $dirAssests?>/img/icon/ms-my.svg" alt=""></a>
                                        </div>
                                        <div class="notify_content">
                                            <h5> <a href="javascript:void(0)" id="ms-my" class="lang">Bahasa Melayu</a></h5>
                            
                                        </div>
                                    </div>
                                    <!-- single_notify  -->
                                    <div class="single_notify d-flex align-items-center">
                                        <div class="notify_thumb">
                                            <a href="javascript:void(0)" ><img src="<?= $dirAssests?>/img/icon/en.svg" alt=""></a>
                                        </div>
                                        <div class="notify_content">
                                           <h5> <a href="javascript:void(0)" id="en" class="lang">English</a></h5>
                                  
                                        </div>
                                    </div>
                                    
                                    
                                   
                                </div>
                       
                            </div>
                            <!--/ Menu_NOtification_Wrap  -->
                            </li>
                            </ul>
                            
                        </div>
                        <div class="profile_info d-flex align-items-center">
                            <div class="profile_thumb mr_20">
                                <img src="<?=Url::to(['/entrepreneur/profile/profile-image', 'id' => Yii::$app->user->identity->entrepreneur->id])?>" alt="#">
                            </div>
                            <div class="author_name">
                                <h4 class="f_s_15 f_w_500 mb-0"><?=Yii::$app->user->identity->fullname?></h4>
                                <?=\Yii::t('app', 'Beneficiary')?></p>
                            </div>
                            <div class="profile_info_iner">
                                <div class="profile_author_name">
                                    <p><?=\Yii::t('app', 'Beneficiary')?></p>
                                    <h5><?=Yii::$app->user->identity->fullname?></h5>
                                </div>
                                <div class="profile_info_details">
                                    <a href="#"><?=\Yii::t('app', 'My Profile')?></a>
                                    <a href="#"><?=\Yii::t('app', 'Settings')?></a>
                                    <?= Html::a(\Yii::t('app', 'Log Out'),['/site/logout'],['data-method' => 'post']) ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
    </div>
    
    <?php 

$this->registerJs("
    
 $(document).on('click', '.lang', function(e){
    e.preventDefault();
  var lang = $(this).attr('id');

   $.post('". Url::to(['/site/language']) ."', {'lang': lang}, function(data){
console.log(data);
    location.reload();
  }); 
    
});

");

?>