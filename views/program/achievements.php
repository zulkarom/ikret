<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\ProgramRegistration $model */

$this->title = 'Achievement: ' . $model->program_name;

$programSub = $programSub ?? null;

$this->params['breadcrumbs'][] = [
    'label' => $model->program_abbr . ($programSub ? ' / ' . $programSub->sub_abbr : ''),
    'url' => ['/program-registration/manager-dashboard', 'id' => $model->id, 'sub' => $programSub ? $programSub->id : null]
];
$this->params['breadcrumbs'][] = $this->title;
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
                    <tr><th>No.</th><th>Achievement Name</th><th></th></tr>
                    <?php 
                    if($achievement){
                        $i=1;
                        foreach($achievement as $a){
                            echo '<tr><td>'.$i.'. </td><td>'.$a->name.'</td></tr>';
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


