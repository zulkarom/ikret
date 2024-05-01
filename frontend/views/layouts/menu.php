<?php 

use yii\helpers\Url;
use common\widgets\Menu_crypto;

?> 
<nav class="sidebar dark_sidebar">
    <div class="logo d-flex justify-content-between">
        <a class="large_logo" href="<?php echo Url::to(['/'])?>"><img src="<?= $dirAssests?>/img/logo_white3.png" alt=""></a>
        <a class="small_logo" href="<?php echo Url::to(['/'])?>"><img src="<?= $dirAssests?>/img/mini_logo3.png" alt=""></a>
        <div class="sidebar_close_icon d-lg-none">
            <i class="ti-close"></i>
        </div>
    </div>
    <ul id="sidebar_menu"> 
                

    <?=Menu_crypto::widget(
    [
            
        ['label' => \Yii::t('app', 'Dashboard'), 'level' => 1, 'url' => ['/entrepreneur/dashboard/index'], 'icon' => $dirAssests.'/img/menu-icon/1.svg', 'children' => []],
        ['label' => \Yii::t('app', 'Profile'), 'level' => 1, 'url' => ['/entrepreneur/profile/index'], 'icon' => $dirAssests.'/img/menu-icon/4.svg', 'children' => []],
            // ['label' => 'Announcement', 'level' => 1, 'url' => ['/announcement/index'], 'icon' => 'fa fa-bullhorn', 'children' => []],
        ['label' => \Yii::t('app', 'Location'), 'level' => 1, 'url' => ['/entrepreneur/profile/location'], 'icon' => $dirAssests.'/img/menu-icon/map.svg', 'children' => []],
        
        
        ['label' => \Yii::t('app', 'Sectors'), 'level' => 1, 'url' => ['/entrepreneur/sector/index'], 'icon' => $dirAssests.'/img/menu-icon/17.svg', 'children' => []],
        ['label' => \Yii::t('app', 'Competency'), 'level' => 1, 'url' => ['/entrepreneur/competency/index'], 'icon' => $dirAssests.'/img/menu-icon/icon.svg', 'children' => []],
        
        ['label' => \Yii::t('app', 'Social Impact'), 'level' => 1, 'url' => ['/entrepreneur/social-impact/index'], 'icon' => $dirAssests.'/img/menu-icon/14.svg', 'children' => []],
        
        ['label' => \Yii::t('app', 'Economics'), 'level' => 1, 'url' => ['/entrepreneur/economic/index'], 'icon' => $dirAssests.'/img/menu-icon/2.svg', 'children' => []],
        ['label' => \Yii::t('app', 'Agency'), 'level' => 1, 'url' => ['/entrepreneur/agency/index'], 'icon' => $dirAssests.'/img/menu-icon/10.svg', 'children' => []],
        ['label' => \Yii::t('app', 'Program'), 'level' => 1, 'url' => ['/entrepreneur/program/index'], 'icon' => $dirAssests.'/img/menu-icon/Pages.svg', 'children' => []],
        
        ['label' => \Yii::t('app', 'Suppliers'), 'level' => 1, 'url' => ['/entrepreneur/supplier/index'], 'icon' => $dirAssests.'/img/menu-icon/4.svg', 'children' => []],
            

        
        ]
    
    )?>

                    
                    
                    
<br /><br /><br /><br /><br /><br />
                    
                    
                    
                    
                    
                    
                    
                    
                    
                    
                    
    </ul>
</nav>
<?php 
/* 
$this->registerJs('

$(".has-treeview").click(function(){
    
    if($(this.hasClass("menu-open") == false){
        $(".has-treeview").each(function(i, obj) {
            $(this).removeClass("menu-open");
        });
    }
    
    
});

');


 */
?>