<?php

use app\models\UserRole;
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

        $menu[] = ['name' => 'Attendance & Certificate', 'url' => ['/session/participant'], 'icon' => 'bi bi-upc-scan'];
     

        if(Yii::$app->user->identity->isParticipant){
          $menu[] = ['name' => 'Participant Menu', 'heading' => true];
          $menu[] = ['name' => 'List of Programs', 'url' => ['/program/index'], 'icon' => 'bi bi-easel'];
          $menu[] = ['name' => 'Pre-Event Questionnaire', 'url' => ['/program/prequestion'], 'icon' => 'bi bi-patch-question'];
          $menu[] = ['name' => 'Post-Event Questionnaire', 'url' => ['/program/postquestion'], 'icon' => 'bi bi-patch-question-fill'];
          $menu[] = ['name' => 'Certificates & Awards', 'url' => ['/program/certificate'], 'icon' => 'bi bi-award'];
        }

        if(Yii::$app->user->identity->isJury){
          $menu[] = ['name' => 'Jury Menu', 'heading' => true];
          $menu[] = ['name' => 'List of Assignments', 'url' => ['/program-registration/jury-assignment'], 'icon' => 'bi bi-file-earmark-medical'];
          $menu[] = ['name' => 'Jury Certificate', 'url' => ['/program-registration/jury-cert-page'], 'icon' => 'bi bi-award'];
        }

        if(Yii::$app->user->identity->isCommittee){
          $menu[] = ['name' => 'Committee Menu', 'heading' => true];

          

          $staff = UserRole::find()->alias('a')
          ->joinWith(['committee c'])
          ->where(['a.user_id' => Yii::$app->user->identity->id, 
          'a.role_name' => 'committee', 
          'a.status' => 10,
          'c.is_student' => 0,
          'c.cert_only' => 0
          ])
          ->one();

          if($staff){
            $menu[] = ['name' => 'Letter of Appointment', 'url' => ['/committee/letter'], 'icon' => 'bi bi-file-earmark-medical'];
          }
          $menu[] = ['name' => 'Committee Certificate', 'url' => ['/committee/certificate-page'], 'icon' => 'bi bi-award'];

          //head
          $head = UserRole::find()->alias('a')
          ->joinWith(['committee c'])
          ->where(['a.user_id' => Yii::$app->user->identity->id, 
          'a.role_name' => 'committee', 
          'a.status' => 10,
          'a.is_leader' => 1,
          'c.is_jawatankuasa' => 1
          ])
          ->one();
          $canApprove = UserRole::find()->alias('a')
          ->joinWith(['committee c'])
          ->where(['a.user_id' => Yii::$app->user->identity->id, 
          'a.role_name' => 'committee', 
          'a.status' => 10,
          'c.can_approve' => 1
          ])
          ->one();
          if($head || $canApprove){
            $menu[] = ['name' => 'Committee Request', 'url' => ['/committee/action-committee'], 'icon' => 'bi bi-brightness-high-fill'];
          }
          
        }

        if(Yii::$app->user->identity->isMentor){
          $menu[] = ['name' => 'Mentor Menu', 'heading' => true];
          $menu[] = ['name' => 'Mentees & Certificates', 'url' => ['/program-registration/mentor-mentees'], 'icon' => 'bi bi-file-earmark-medical'];
        }

        if(Yii::$app->user->identity->isManager){
          $pro = UserRole::find()->where(['user_id' => Yii::$app->user->identity->id, 'role_name' => 'manager', 'status' => 10])->all();
          if($pro){
            $menu[] = ['name' => 'Manager Menu', 'heading' => true];
            foreach($pro as $p){
              if($p->program){
                $sub = '';
                $url = ['/program-registration/manager','id' => $p->program_id];
                $urls = ['/program-registration/manager-session','id' => $p->program_id];
                $url7 = ['/program-registration/manager-analysis','id' => $p->program_id];
                $url2 = ['/program-registration/jury-result','id' => $p->program_id];
                $url3 = ['/program/register-fields','id' => $p->program_id];
                $url4 = ['/program/rubrics','id' => $p->program_id];
                $url5 = ['/program/achievement','id' => $p->program_id];
                $url6 = ['/program/info','id' => $p->program_id];
                $url8 = ['/program-registration/manager-view-certs','id' => $p->program_id];
                

                if($p->programSub){
                  $sub = '/' . $p->programSub->sub_abbr;
                  $url = ['/program-registration/manager','id' => $p->program_id, 'sub' => $p->program_sub];
                  $urls = ['/program-registration/manager-session','id' => $p->program_id, 'sub' => $p->program_sub];
                  $url7 = ['/program-registration/manager-analysis','id' => $p->program_id, 'sub' => $p->program_sub];
                  $url2 = ['/program-registration/jury-result','id' => $p->program_id, 'sub' => $p->program_sub];
                  $url4 = ['/program/rubrics','id' => $p->program_id, 'sub' => $p->program_sub];
                  $url3 = ['/program/register-fields','id' => $p->program_id, 'sub' => $p->program_sub];
                  $url5 = ['/program/achievement','id' => $p->program_id, 'sub' => $p->program_sub];
                  $url8 = ['/program-registration/manager-view-certs','id' => $p->program_id, 'sub' => $p->program_sub];
                }
                $sub_menu = [];

                if($p->program->program_type == 1){

                  $sub_menu[] = ['name' => 'Participants & Juries Assignment', 'url' => $url];
                  $sub_menu[] = ['name' => 'Result By Assignments', 'url' => $url2];
                  $sub_menu[] = ['name' => 'Analysis & Achievement', 'url' => $url7];
                  $sub_menu[] = ['name' => 'Certificates', 'url' => $url8];
                  $sub_menu[] = ['name' => 'Registration Fields', 'url' => $url3];
                  $sub_menu[] = ['name' => 'Rubrics', 'url' => $url4];
                  $sub_menu[] = ['name' => 'Achievements', 'url' => $url5];
                }else{
                  $sub_menu[] = ['name' => 'Participants & Certificates', 'url' => $urls];
                }
                

                $sub_menu[] = ['name' => 'Program Info', 'url' => $url6];

                $menu[] = ['name' => $p->program->program_abbr.$sub, 'url' => ['/'], 'icon' =>  'bi bi-list-stars', 'children' => $sub_menu];

              }
              
            }

            $menu[] = ['name' => 'List of Juries', 'url' => ['/user/jury'], 'icon' => 'bi bi-person-badge'];
            $menu[] = ['name' => 'List of Mentors', 'url' => ['/user/mentor'], 'icon' => 'bi bi-person-badge'];
            $menu[] = ['name' => 'All Users', 'url' => ['/user/all'], 'icon' => 'bi bi-person-lines-fill'];

            $menu[] = ['name' => 'Session Attendance', 'url' => ['/'], 'icon' => 'bi bi-upc-scan', 'children' => [
              ['name' => 'Session List', 'url' => ['/session/index']],
              ['name' => 'Attendance List', 'url' => ['/session/attendance']],
          ]];


          }
          
          

        }
        

        if(Yii::$app->user->identity->isAdmin){
          $menu[] = ['name' => 'Admin Menu', 'heading' => true];
          $menu[] = ['name' => 'User Role Request', 'url' => ['/committee/request'], 'icon' => 'bi bi-brightness-high-fill'];
          

          $menu[] = ['name' => 'List of Committees', 'url' => ['/committee/index'], 'icon' => 'bi bi-diagram-2'];

          $menu[] = ['name' => 'Program Registration (All)', 'url' => ['/program-registration/index'], 'icon' => 'bi bi-list-stars'];

          $menu[] = ['name' => 'All Users', 'url' => ['/user/all'], 'icon' => 'bi bi-person-lines-fill'];

          
          
        }

        $menu[] = ['name' => 'User Menu', 'heading' => true];
        $menu[] = ['name' => 'Profile', 'url' => ['/user/index'], 'icon' => 'bi bi-file-earmark-person'];
        $menu[] = ['name' => 'User Role', 'url' => ['/user/add-role'], 'icon' => 'bi bi-person-plus'];

        $session = Yii::$app->session;
        if ($session->has('or-usr')){
          $menu[] = ['name' => 'Return Role', 'url' => ['/user/return-role'], 'icon' => 'bi bi-person-lines-fill'];
        }
        
        $menu[] = ['name' => 'Change Password', 'url' => ['/user/change-password'], 'icon' => 'bi bi-lock'];
        $menu[] = ['name' => 'Logout', 'url' => ['/site/logout'], 'icon' => 'bi bi-box-arrow-right'];



      }
      

      $i=1;
      foreach($menu as $item){
        echoMenuItem($item, $i);
        $i++;
      }
      ?>
    </ul>

  </aside>

  <?php 

    function echoMenuItem($item, $i){
        $html= '';
        if(array_key_exists('url', $item)){
          $active = isItemActive($item['url']);
          
        $collapse = $active ? '' : 'collapsed';
        $children=null;
        if(array_key_exists('children', $item)){
            $children = $item['children'];
        }
        
        $children_has_active = childrenActive($children);
        $collapse = $children_has_active ? '' : 'collapsed';
        //echo $children_has_active ? 'children_has_active' :'';
        /* ok dah hightlight menu tak 
        collapse
        echo
 */
        $html .= '<li class="nav-item">';
        
        if($children){
          $html .= '<a class="nav-link '.$collapse.'" data-bs-target="#components-nav-'.$i.'" data-bs-toggle="collapse" href="#">';
        }else{
          $html .= '<a class="nav-link '.$collapse.'" href="' . Url::to($item['url']) . '">';
        }
        $html .=  '<i class="'. $item['icon'] .'"></i>
          <span>'. $item['name'] .'</span>';
          if($children){
            $html .= '<i class="bi bi-chevron-down ms-auto"></i>';
          }
          $html .= '</a>';
        if($children){
          $show = $children_has_active ? 'show' : '';
          $html .= '<ul id="components-nav-'.$i.'" class="nav-content collapse '.$show.'" data-bs-parent="#sidebar-nav">';
          foreach($children as $child){
            $child_active = isItemActive($child['url']) ? 'class="active"' : '';
            $html .= '<li>
            <a href="'.Url::to($child['url']).'" '.$child_active.'>
              <i class="bi bi-circle"></i><span>'.$child['name'].'</span>
            </a>
          </li>';
          }
          $html .= '</ul>';
        }
        $html .= '</li>';
        }else if(array_key_exists('heading', $item)){
          $html .= '<li class="nav-heading">'. $item['name'] .'</li>';
        }

        echo $html;
    }

    function childrenActive($children){
      if($children){
        foreach($children as $child){
          if(array_key_exists('url', $child)){
            $active = isItemActive($child['url']);
            if($active){ //even satu pun dah cukup
              return true;
            }
          }
        }
      }
      return false;
    }

  function isItemActive($url)
    {
      //echo Yii::$app->controller->id;
      //echo  Yii::$app->controller->action->id;
      $count = 0;
      if(is_array($url)){
        $count = count($url);
        if($count == 1 && array_key_exists(0,$url) && $url[0] == '/'){
          if(Yii::$app->controller->id == 'site' && Yii::$app->controller->action->id == 'index'){
            //echo 'masuk';
            //die();
          return true;
          }
        }
        //die(); 
        $url_str = $url[0];
        $arr = explode('/', $url_str);
        //assuming tak de module
        // url kena start dengan /
        //kita nak cari ada ?
        $id = null;
        $sub = null;
        if(array_key_exists('id', $url)){
          $id = $url['id'];
        }
        if(array_key_exists('sub', $url)){
          $sub = $url['sub'];
        }
  
      
        //klu ada explode
        //cari ada = ke
        //klu ada explode
  
        $id_get  = Yii::$app->request->get('id');
        $sub_get  = Yii::$app->request->get('sub');
  
        //echo $sub_get;
        
        if($id && $sub){
          if($arr[1] == Yii::$app->controller->id && 
          $arr[2] == Yii::$app->controller->action->id && $id == $id_get && $sub == $sub_get){
           // echo 'id=' .$id, 'sub='.$sub;
            return true;
          }
        }else if($id){
          if($arr[1] == Yii::$app->controller->id && 
          $arr[2] == Yii::$app->controller->action->id && $id == $id_get && $sub == null){
           // echo 'id=' .$id, 'sub='.$sub;
            return true;
          }
        }else{
          if($arr[1] == Yii::$app->controller->id && 
          $arr[2] == Yii::$app->controller->action->id){
            return true;
          }
        }
      }
			
      
			



        return false;
    }
  ?>