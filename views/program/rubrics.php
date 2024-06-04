<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\ProgramRegistration $model */

$this->title = 'Rubric: ' . $model->program_name;
?>


<div class="pagetitle">
<h1><?=$this->title?></h1>
<?php 
if($programSub){
    echo $programSub->sub_name;
}

?>
</div>

    </div><!-- End Page Title -->

    <section class="section dashboard">

    <div class="card">
            <div class="card-body pt-4">

            <table class="table">
                <tbody>
                    <tr><th>No.</th><th>Rubric Name</th><th></th></tr>
                    <?php 
                    if($rubrics){
                        $i=1;
                        foreach($rubrics as $r){
                            echo '<tr><td>'.$i.'. </td><td>'.$r->rubric->rubric_name.'</td><td>'. Html::a('View', ['view-rubric', 'id' => $r->rubric_id], ['class' => 'btn btn-primary btn-sm']) .'</td></tr>';
                            $i++;
                        }
                    }
                    ?> 
                </tbody>
            </table>

    

</div>
            </div>
        </div>



    </section>


