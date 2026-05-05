<?php

use app\models\UserRole;
use app\models\ProgramSub;
use app\models\AppSetting;
use yii\helpers\Url;

?>
<aside id="sidebar" class="sidebar">
    <ul class="sidebar-nav" id="sidebar-nav">
      <?php
      
      $menu[] = ['name' => 'Home', 'url' => ['/'], 'icon' => 'bi bi-house'];

      if(Yii::$app->user->isGuest){
        
        $menu[] = ['name' => 'Login', 'url' => ['/site/login'], 'icon' => 'bi bi-box-arrow-in-right'];
        $menu[] = ['name' => 'Program Registration', 'url' => ['/program/public-programs'], 'icon' => 'bi bi-card-list'];
        if(AppSetting::getBool('call_for_juries_enabled', true)){
          $menu[] = ['name' => 'Call for Juries', 'url' => ['/site/jury-apply'], 'icon' => 'bi bi-person-plus'];
        }
      }else{

        $menu[] = ['name' => 'Attendance & Certificate', 'url' => ['/session/participant'], 'icon' => 'bi bi-upc-scan'];
     

        if(Yii::$app->user->identity->isParticipant){
          $menu[] = ['name' => 'Participant Menu', 'heading' => true];
          $menu[] = ['name' => 'Public Registration', 'url' => ['/program/public-programs'], 'icon' => 'bi bi-card-list'];
          // $menu[] = ['name' => 'Pre-Event Questionnaire', 'url' => ['/program/prequestion'], 'icon' => 'bi bi-patch-question'];
          // $menu[] = ['name' => 'Post-Event Questionnaire', 'url' => ['/program/postquestion'], 'icon' => 'bi bi-patch-question-fill'];
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
            $byProgram = [];
            foreach($pro as $r){
              $pid = (int)$r->program_id;
              if(!array_key_exists($pid, $byProgram)){
                $byProgram[$pid] = [];
              }
              $byProgram[$pid][] = $r;
            }

            foreach($byProgram as $pid => $roles){
              $first = $roles ? $roles[0] : null;
              if(!$first || !$first->program){
                continue;
              }

              $program = $first->program;

              if((int)$program->has_sub === 1){
                $hasProgramLevel = false;
                $allowedSubs = [];
                foreach($roles as $r){
                  if(empty($r->program_sub)){
                    $hasProgramLevel = true;
                    break;
                  }
                  $allowedSubs[(int)$r->program_sub] = true;
                }

                $subs = $program->programSubs;
                $subTable = Yii::$app->db->schema->getTableSchema(ProgramSub::tableName());
                $hasSubActiveColumn = $subTable && $subTable->getColumn('is_active');
                $sub_menu = [];

                $activeSubIds = [];
                if($subs){
                  foreach($subs as $sp){
                    if($hasSubActiveColumn && (int)$sp->getAttribute('is_active') !== 1){
                      continue;
                    }
                    $activeSubIds[(int)$sp->id] = true;
                  }
                }

                $hasAllActiveSubs = true;
                if(!$hasProgramLevel){
                  foreach(array_keys($activeSubIds) as $sid){
                    if(!array_key_exists((int)$sid, $allowedSubs)){
                      $hasAllActiveSubs = false;
                      break;
                    }
                  }
                }

                if($hasProgramLevel || $hasAllActiveSubs){
                  $sub_menu[] = [
                    'name' => 'Parent',
                    'url' => ['/program-registration/manager-parent', 'id' => $pid]
                  ];
                }
                if($subs){
                  foreach($subs as $sp){
                    if($hasSubActiveColumn && (int)$sp->getAttribute('is_active') !== 1){
                      continue;
                    }
                    if(!$hasProgramLevel && !array_key_exists((int)$sp->id, $allowedSubs)){
                      continue;
                    }
                    $sub_menu[] = [
                      'name' => $sp->sub_name,
                      'url' => ['/program-registration/manager-dashboard', 'id' => $pid, 'sub' => $sp->id]
                    ];
                  }
                }

                $menu[] = [
                  'name' => $program->program_abbr,
                  'url' => ['/'],
                  'icon' =>  'bi bi-list-stars',
                  'children' => $sub_menu
                ];
              }else{
                $menu[] = [
                  'name' => $program->program_abbr,
                  'url' => ['/program-registration/manager-dashboard', 'id' => $pid],
                  'icon' =>  'bi bi-list-stars'
                ];
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

          $menu[] = ['name' => 'Call for Juries Config', 'url' => ['/jury-requirement/index'], 'icon' => 'bi bi-person-plus'];
          

          $menu[] = ['name' => 'List of Committees', 'url' => ['/committee/index'], 'icon' => 'bi bi-diagram-2'];

          $menu[] = ['name' => 'Program Registration (All)', 'url' => ['/program-registration/index'], 'icon' => 'bi bi-list-stars'];

          $menu[] = ['name' => 'All Users', 'url' => ['/user/all'], 'icon' => 'bi bi-person-lines-fill'];

          $menu[] = ['name' => 'Registration Fields Config', 'url' => ['/program-reg-field/index'], 'icon' => 'bi bi-ui-checks-grid'];

          $menu[] = ['name' => 'Program Sub Programs', 'url' => ['/program/admin-program-subs'], 'icon' => 'bi bi-diagram-3'];

          $menu[] = ['name' => 'Program Stats', 'url' => ['/program/admin-program-stats'], 'icon' => 'bi bi-bar-chart'];

          $menu[] = ['name' => 'Settings', 'url' => ['/setting/update'], 'icon' => 'bi bi-gear'];

          
          
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
        if(array_key_exists('url', $item) || array_key_exists('post_url', $item)){
          $active = array_key_exists('url', $item) ? isItemActive($item['url']) : false;
          
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
          $html .=  '<i class="'. $item['icon'] .'"></i>
          <span>'. $item['name'] .'</span>';
          $html .= '<i class="bi bi-chevron-down ms-auto"></i>';
          $html .= '</a>';
        }else if(array_key_exists('post_url', $item)){
          $html .= '<form method="post" action="' . Url::to($item['post_url']) . '" style="margin:0;">';
          $html .= \yii\helpers\Html::hiddenInput(Yii::$app->request->csrfParam, Yii::$app->request->getCsrfToken());
          foreach($item['post_data'] as $postKey => $postValue){
            $html .= \yii\helpers\Html::hiddenInput($postKey, $postValue);
          }
          $html .= '<button type="submit" class="nav-link '.$collapse.'" style="width:100%;border:none;background:none;text-align:left;">';
          $html .=  '<i class="'. $item['icon'] .'"></i>
          <span>'. $item['name'] .'</span>';
          $html .= '</button>';
          $html .= '</form>';
        }else{
          $html .= '<a class="nav-link '.$collapse.'" href="' . Url::to($item['url']) . '">';
          $html .=  '<i class="'. $item['icon'] .'"></i>
          <span>'. $item['name'] .'</span>';
          $html .= '</a>';
        }
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

        if($count === 1 && array_key_exists(0,$url) && $url[0] === '/'){
          if(Yii::$app->controller->id === 'site' && Yii::$app->controller->action->id === 'index'){
            return true;
          }
        }

        if(!array_key_exists(0, $url) || !is_string($url[0])){
          return false;
        }

        $route = ltrim($url[0], '/');
        $currentRoute = Yii::$app->controller->id . '/' . Yii::$app->controller->action->id;

        if($route !== $currentRoute){
          return false;
        }

        if(array_key_exists('id', $url)){
          $id_get  = Yii::$app->request->get('id');
          if((string)$url['id'] !== (string)$id_get){
            return false;
          }
        }

        if(array_key_exists('sub', $url)){
          $sub_get  = Yii::$app->request->get('sub');
          if((string)$url['sub'] !== (string)$sub_get){
            return false;
          }
        }

        return true;
      }
			
      
			



        return false;
    }
  ?>


