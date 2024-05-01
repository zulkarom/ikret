<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\helpers\Url;
/* @var $this yii\web\View */
/* @var $model backend\models\moduleKategori */

$this->title = $model->kategori_name;
$this->params['breadcrumbs'][] = ['label' => 'Module Program', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>

<div class="card">
<div class="card-body">
<div class="module-kategori-view">

<style>
table.detail-view th {
    width:15%;
}
</style>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
           // 'id',
            'kategori_name',
        ],
    ]) ?>
    
        <p>
        <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>
</div>
</div>
</div>
<br/>

<div class="card">
        <div class="card-header"><b>Module</b></div>
        <div class="card-body">
            <div class="table-responsive"> <table class="table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>NAME</th>
                        <th></th>
                    </tr>
                    <?php 
                        $i = 1;
                        if($model->modules){
                            foreach ($model->modules as $module) {
                                
                                echo'<tr>
                                <td>'.$i.'</td>
                                <td>'.$module->module_name.'</td>
                                <td><a href="' . Url::to(['/module-kategori/delete', 'id' => $module->id, 'pid' => $model->id]) . '" ><span class="fa fa-trash fa-xs"></span></a></td>
                                </tr>';
                                $i++;
                                
                            }
                        }
                    ?>               
                </thead>
            </table></div>
                    
            <br/>

                <p>
                    <?php echo Html::button('<span class="fa fa-plus"></span> Add module', ['value' => Url::to(['/module/create','pid' => $model->id]), 'class' => 'btn btn-success btn-sm', 'id' => 'modalBttnmodule']);
                    
                        

                    $this->registerJs('
                    $(function(){
                      $("#modalBttnmodule").click(function(){
                          $("#createmodule").modal("show")
                            .find("#formCreatemodule")
                            .load($(this).attr("value"));
                      });
                    });
                    ');
                
                
                    ?>
                </p>
        </div>
    </div>
