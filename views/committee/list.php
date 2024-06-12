<?php

use app\models\Program;
use app\models\ProgramRegistration;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;

/** @var yii\web\View $this */
/** @var app\models\ProgramRegistrationSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Committee';
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
                                <tr><th>No.</th><th width="40%">Jawatankuasa</th><th width="60%"></th></tr>
                                <?php 
                                if($list){
                                    $i=1;
                                    foreach($list as $r){
                                        echo '<tr>
                                        <td>'.$i.'. </td>
                                        <td>'.$r->com_name.'</td>
                                        <td>';
                                        renderComm($r);
                                        
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

<?php

function renderComm($u){
    ?>
<table>
    <tbody>
        <?php 
        if($u->userRoles){
            $i=1;
            foreach($u->userRoles as $c){
                $style='style="color:red"';
                if($c->status == 10){
                    $style='';
                }

                echo '<tr>
                <td width="90%"><span '.$style.'>'.$c->user->fullname.'</span></td>
                <td width="10%">'. Html::a('pdf', ['/committee/letter-pdf', 'id' => $c->id], ['class' => 'btn btn-primary btn-sm', 'target' => '_blank']) .'</td>
                </tr>';
                $i++;
            }
        }
        ?> 
    </tbody>
</table>

<?php
}
?>