<?php

use yii\helpers\Html;
use yii\helpers\Url;

/** @var yii\web\View $this */
/** @var app\models\ProgramRegistration $model */


$this->title = 'Rubric: ' . $model->program_name;

$programSub = $programSub ?? null;
$rubrics = $rubrics ?? [];

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

            <div class="mb-3">
                <form method="post" action="<?= Url::to(['rubric-add', 'id' => $model->id, 'sub' => $programSub ? $programSub->id : null]) ?>">
                    <?= Html::hiddenInput(Yii::$app->request->csrfParam, Yii::$app->request->csrfToken) ?>
                    <div class="row g-2 align-items-center">
                        <div class="col-md-8">
                            <input type="text" name="rubric_name" class="form-control" placeholder="New rubric name" />
                        </div>
                        <div class="col-md-4">
                            <button type="submit" class="btn btn-success">Add Rubric</button>
                        </div>
                    </div>
                </form>
            </div>

            <table class="table">
                <tbody>
                    <tr><th>No.</th><th>Rubric Name</th><th>Actions</th></tr>
                    <?php 
                    if($rubrics){
                        $i=1;
                        foreach($rubrics as $r){

                            $editKey = 'edit_' . $r->id;
                            $isEdit = Yii::$app->request->get($editKey) == 1;

                            echo '<tr>';
                            echo '<td>'.$i.'. </td>';
                            echo '<td>';

                            if($isEdit){
                                echo '<form method="post" action="'.Url::to(['rubric-edit', 'id' => $model->id, 'sub' => $programSub ? $programSub->id : null, 'pr' => $r->id, 'rubric' => $r->rubric_id]).'">';
                                echo Html::hiddenInput(Yii::$app->request->csrfParam, Yii::$app->request->csrfToken);
                                echo '<div class="row g-2 align-items-center">';
                                echo '<div class="col-md-8">';
                                echo '<input type="text" name="rubric_name" class="form-control" value="'.Html::encode($r->rubric->rubric_name).'" />';
                                echo '</div>';
                                echo '<div class="col-md-4">';
                                echo '<button type="submit" class="btn btn-primary btn-sm">Save</button> ';
                                echo Html::a('Cancel', ['rubrics', 'id' => $model->id, 'sub' => $programSub ? $programSub->id : null], ['class' => 'btn btn-secondary btn-sm']);
                                echo '</div>';
                                echo '</div>';
                                echo '</form>';
                            }else{
                                echo Html::encode($r->rubric->rubric_name);
                            }

                            echo '</td>';
                            echo '<td>';

                            echo Html::a('View', ['view-rubric', 'id' => $r->rubric_id], ['class' => 'btn btn-primary btn-sm']);
                            if(!$isEdit){
                                echo ' ' . Html::a('Edit', ['rubrics', 'id' => $model->id, 'sub' => $programSub ? $programSub->id : null, $editKey => 1], ['class' => 'btn btn-warning btn-sm']);
                                echo ' <form method="post" action="'.Url::to(['rubric-delete', 'id' => $model->id, 'sub' => $programSub ? $programSub->id : null, 'pr' => $r->id]).'" style="display:inline;">';
                                echo Html::hiddenInput(Yii::$app->request->csrfParam, Yii::$app->request->csrfToken);
                                echo '<button type="submit" class="btn btn-danger btn-sm" onclick="return confirm(\'Delete this rubric from this program?\')">Delete</button>';
                                echo '</form>';
                            }

                            echo '</td>';
                            echo '</tr>';
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


