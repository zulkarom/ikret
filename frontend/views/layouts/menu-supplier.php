<?php 

use yii\helpers\Url;
use common\widgets\Menu_crypto;

?> 
<nav class="sidebar dark_sidebar">
    <div class="logo d-flex justify-content-between">
        <a class="large_logo" href="<?php echo Url::to(['/'])?>"><img src="<?= $dirAssests?>/img/logo_whitex.png" alt=""></a>
        <a class="small_logo" href="<?php echo Url::to(['/'])?>"><img src="<?= $dirAssests?>/img/mini_logox.png" alt=""></a>
        <div class="sidebar_close_icon d-lg-none">
            <i class="ti-close"></i>
        </div>
    </div>
    <ul id="sidebar_menu"> 
                

    <?=Menu_crypto::widget(
    [
            
        ['label' => \Yii::t('app', 'Dashboard'), 'level' => 1, 'url' => ['/supplier/dashboard/index'], 'icon' => $dirAssests.'/img/menu-icon/1.svg', 'children' => []],
            
        ['label' => \Yii::t('app', 'Profile'), 'level' => 1, 'url' => ['/supplier/profile/index'], 'icon' => $dirAssests.'/img/menu-icon/4.svg', 'children' => []],
        
        ['label' => \Yii::t('app', 'Location'), 'level' => 1, 'url' => ['/supplier/profile/location'], 'icon' => $dirAssests.'/img/menu-icon/map.svg', 'children' => []],
        
        ['label' => \Yii::t('app', 'Sectors'), 'level' => 1, 'url' => ['/supplier/sector/index'], 'icon' => $dirAssests.'/img/menu-icon/17.svg', 'children' => []],
        ['label' => \Yii::t('app', 'Clients'), 'level' => 1, 'url' => ['/supplier/client/index'], 'icon' => $dirAssests.'/img/menu-icon/5.svg', 'children' => []],
        
      
            // ['label' => 'General', 'level' => 2 , 'icon' => 'fa fa-cog', 'children' => [
            //     ['label' => 'Example 1', 'url' => ['/city'], 'icon' => 'fa fa-circle'],
            //     ['label' => 'Example 2', 'url' => ['/client/prospect-type'], 'icon' => 'fa fa-circle'],
            //     ['label' => 'Example 3', 'url' => ['/staff/grade'], 'icon' => 'fa fa-circle'],
            
            // ]],
        
        ]
    
    )?>

                    
                    
                    

                    
                    
                    
                    
                    
                    
                    
                    
                    
                    
                    
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