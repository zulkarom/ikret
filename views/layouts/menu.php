<?php
use yii\helpers\Url;

?>
<aside id="sidebar" class="sidebar">
    <ul class="sidebar-nav" id="sidebar-nav">
      <?php
      
      $menu[] = ['name' => 'Home', 'url' => ['/'], 'icon' => 'bi bi-house'];

      if(Yii::$app->user->isGuest){
        
        $menu[] = ['name' => 'Login', 'url' => ['/site/login'], 'icon' => 'bi bi-box-arrow-in-right'];
        $menu[] = ['name' => 'Register', 'url' => ['/site/register'], 'icon' => 'bi bi-card-list'];
      }else{

        if(Yii::$app->user->identity->isParticipant){
          $menu[] = ['name' => 'Participant Menu'];
          $menu[] = ['name' => 'List of Programs', 'url' => ['/program/index'], 'icon' => 'bi bi-easel'];
          $menu[] = ['name' => 'Pre-Event Questionnaire', 'url' => ['/program/prequestion'], 'icon' => 'bi bi-patch-question'];
          $menu[] = ['name' => 'Post-Event Questionnaire', 'url' => ['/program/postquestion'], 'icon' => 'bi bi-patch-question-fill'];
          $menu[] = ['name' => 'Certificate', 'url' => ['/program/certificate'], 'icon' => 'bi bi-award'];
        }

        if(Yii::$app->user->identity->isCommittee){
          $menu[] = ['name' => 'Committee Menu'];
          $menu[] = ['name' => 'Letter of Appointment', 'url' => ['/committee/letter'], 'icon' => 'bi bi-file-earmark-medical'];

        }
        

        if(Yii::$app->user->identity->isAdmin){
          $menu[] = ['name' => 'Admin Menu'];
          $menu[] = ['name' => 'Program Registration', 'url' => ['/program-registration/index'], 'icon' => 'bi bi-list-stars'];
          $menu[] = ['name' => 'List of Committees', 'url' => ['/committee/index'], 'icon' => 'bi bi-diagram-2'];
          $menu[] = ['name' => 'User Role Request', 'url' => ['/committee/request'], 'icon' => 'bi bi-brightness-high-fill'];

        }

        $menu[] = ['name' => 'User Menu'];
        $menu[] = ['name' => 'Profile', 'url' => ['/user/index'], 'icon' => 'bi bi-file-earmark-person'];
        $menu[] = ['name' => 'Add Role', 'url' => ['/user/add-role'], 'icon' => 'bi bi-person-plus'];
        $menu[] = ['name' => 'Change Password', 'url' => ['/user/change-password'], 'icon' => 'bi bi-lock'];
        $menu[] = ['name' => 'Logout', 'url' => ['/site/logout'], 'icon' => 'bi bi-box-arrow-right'];



      }
      


      foreach($menu as $item){

        
        if(array_key_exists('url', $item)){
          $active = isItemActive($item['url']);
        $collapse = $active ? '' : 'collapsed';
          echo '<li class="nav-item">
        <a class="nav-link '.$collapse.'" href="' . Url::to($item['url']) . '">
          <i class="'. $item['icon'] .'"></i>
          <span>'. $item['name'] .'</span>
        </a>
      </li>';
        }else{
          echo '<li class="nav-heading">'. $item['name'] .'</li>';
        }
        
      }
      ?>
    </ul>

  </aside>

  <?php 
  function isItemActive($url)
    {
      //echo Yii::$app->controller->id;
      //echo  Yii::$app->controller->action->id;
			$count = count($url);
      if($count == 1 && array_key_exists(0,$url) && $url[0] == '/'){
        if(Yii::$app->controller->id == 'site' && Yii::$app->controller->action->id == 'index'){
          //echo 'masuk';
          //die();
				return true;
			  }
      }
      //die(); 
			$arr = explode('/', $url[0]);
			//assuming tak de module
      // url kena start dengan /
			if($arr[1] == Yii::$app->controller->id && 
			$arr[2] == Yii::$app->controller->action->id){
				return true;
			}
        return false;
    }
  ?>