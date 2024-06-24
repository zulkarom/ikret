<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\ProgramRegistrationSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Committee Certificate';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="pagetitle">

<h1><?=$this->title?></h1>

</div>

    </div><!-- End Page Title -->

    <section class="section dashboard">

    <div class="card">
            <div class="card-body pt-4">
            <table class="table">
                            <tbody>
                                <tr><th>No.</th><th>Name</th><th>Committee</th><th></th></tr>
                                <?php 
            if($list){
                $i=1;
                foreach($list as $r){
                    //$r = $c->committee;
                    echo '<tr>
                    <td>'.$i.'. </td>
                    <td>'.$r->user->fullname.'. </td>
                    <td>';
                    //$r->com_name_en;

                    $str = '';
                      if($r->role_name == 'committee'){
                        if($r->committee){
                            $str .= $r->committee->com_name_en ;
                          if($r->committee->is_jawatankuasa == 1){
                            if($r->is_leader == 1){
                                $str .= '<b> - Leader</b>';
                            }else{
                                $str .= '<b> - Member</b>';
                            }
                            
                          }
                        }
                      }

                    echo $str;
                    
                    echo '</td>
                    <td>';

                    echo Html::a('<i class="bi bi-download"></i>  Certificate', ['certificate', 'id' => $r->id], ['class' => 'btn btn-primary btn-sm', 'target' => '_blank']);
                    echo '</td>
                    </tr>';
                    $i++;
                }
            }
                                ?> 
                            </tbody>
                        </table>
            </div>
        </div>



    </section>

