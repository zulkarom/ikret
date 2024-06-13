<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\ProgramRegistrationSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Certificate';
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
                                <tr><th>No.</th><th>Committee</th><th></th></tr>
                                <?php 
            if($list){
                $i=1;
                foreach($list as $c){
                    $r = $c->committee;
                    echo '<tr>
                    <td>'.$i.'. </td>
                    <td>'.$r->com_name_en.'</td>
                    <td>';

                    echo Html::a('<i class="bi bi-download"></i>  Certificate', ['certificate', 'id' => $c->id], ['class' => 'btn btn-primary btn-sm', 'target' => '_blank']);
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

