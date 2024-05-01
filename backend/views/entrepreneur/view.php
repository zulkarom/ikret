<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\helpers\Url;
use voime\GoogleMaps\Map;
use yii\grid\GridView;
/* @var $this yii\web\View */
/* @var $model backend\models\Entrepreneur */

$this->title = 'View Beneficiary';
$this->params['breadcrumbs'][] = ['label' => 'Beneficiaries', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$model->u_latitude = $model->latitude;
$model->u_longitude = $model->longitude;

?>

<div class="row">
    <div class="col-md-6">
        <div class="entrepreneur-view">
        
        <div class="card">
        <div class="card-header">Profile Information</div>
        <div class="card-body">
        <div class="table-responsive">
            <?= DetailView::widget([
                'model' => $model,
                'attributes' => [
                    [
                        'format' => 'raw',
                        'label' => 'Profile Picture',
                        'value' => function($model){
                            if($model->profile_file){

                                return '<img src="'.Url::to(['/entrepreneur/profile-image', 'id' => $model->id]).'" width="150" >';
                            }
                        }
                    ],
                    [
                        'label' => 'Name',
                        'value' => function($model){
                            return $model->user->fullname;
                        }
                    ],
                    [
                        'label' => 'NRIC',
                        'value' => function($model){
                        return $model->user->nric;
                        }
                        ],
                    'phone',
                    'biz_name',
                    'biz_info',
                    [
                     'label' => 'Date Register',
                     'value' => function($model){
                        if($model->user){
                            return date('d M Y', $model->user->created_at);
                        }
                     }
                    ],
                    [
                        'label' => 'Email',
                        'value' => function($model){
                            return $model->user->email;
                        }
                    ],
                    [
                        'label' => 'Age',
                        'value' => function($model){
                            return $model->age;
                        }
                    ],
                    [
                     'label' => 'Address',
                     'value' => function($model){
                        if($model->address){
                            return $model->fullAddress;
                        }
                     }
                    ],  
                   'note',   
                    [
                        'label' => 'Location',
                        'format' => 'raw',
                        'value' => function($model){
                            return $model->location.
                            '<br/>'.Map::widget([
                                'apiKey'=> 'AIzaSyCdaIFmGh8LWEfbXln7BkPnMfB1RDd9Rj4',
                                'width' => '400px',
                                'height' => '300px',
                                'center' => [$model->u_latitude, $model->u_longitude],
                                'markers' => [
                                    ['position' => [$model->u_latitude, $model->u_longitude],
                                    'content' => '<strong><b>'.$model->biz_name.'<br/>'.$model->location.'</b></strong>'],
                                ]
                                
                            ]);
                        }
                    ],
                    
                ],
            ]) ?>

         
        </div>
        </div>
        </div>
    </div>
    </div>
    <div class="col-md-6">
        <div class="card">
        <div class="card-header">Sectors</div>
        <div class="card-body">
            <?= GridView::widget([
                'dataProvider' => $dataProviderSector,
                'layout' => "{items}\n{pager}",
                //'filterModel' => $searchModel,
                'columns' => [
                    ['class' => 'yii\grid\SerialColumn'],
                    'sectorName',
                    'descriptionx:ntext',
                ],
            ]); ?>  

          </div>
        </div>
        <br/>
        <div class="card">
        <div class="card-header">Competencies</div>
        <div class="card-body">
            <?= GridView::widget([
                'dataProvider' => $dataProviderCompetency,
                'layout' => "{items}\n{pager}",
                //'filterModel' => $searchModel,
                'columns' => [
                    ['class' => 'yii\grid\SerialColumn'],
                    [
                     'label' => \Yii::t('app', 'Competency'),
                     'value' => function($model){
                        return $model->category->category_name;
                     }
                    ],
                    'description:ntext',
                ],
            ]); ?>


          </div>
        </div>

        <br/>
        <div class="card">
        <div class="card-header">Social Impact</div>
        <div class="card-body">
            <?= GridView::widget([
                'dataProvider' => $dataProviderSocial,
                'layout' => "{items}\n{pager}",
                //'filterModel' => $searchModel,
                'columns' => [
                    ['class' => 'yii\grid\SerialColumn'],
                    'socialImpactName',
                    'description:ntext',
                ],
            ]); ?>


          </div>
        </div>

        <br/>
        <div class="card">
        <div class="card-header">Economics</div>
        <div class="card-body">
            <?= GridView::widget([
                'dataProvider' => $dataProviderEconomic,
                'layout' => "{items}\n{pager}",
                //'filterModel' => $searchModel,
                'columns' => [
                    ['class' => 'yii\grid\SerialColumn'],
                    'economicName',
                    'description:ntext',
                ],
            ]); ?>

          </div>
        </div>

        <br/>
        <div class="card">
        <div class="card-header">Agencies</div>
        <div class="card-body">
            <?= GridView::widget([
                'dataProvider' => $dataProviderAgency,
                'layout' => "{items}\n{pager}",
                //'filterModel' => $searchModel,
                'columns' => [
                    ['class' => 'yii\grid\SerialColumn'],
                    'nama_agensi',
                    [
                     'label' => \Yii::t('app', 'Date Accept'),
                     'value' => function($model){
                        return date('d F Y', strtotime($model->tarikh_terima));;
                     }
                    ],
                ],
            ]); ?>


          </div>
        </div>

        <br/>
        <div class="card">
        <div class="card-header">Programs</div>
        <div class="card-body">
            <?= GridView::widget([
                'dataProvider' => $dataProviderProgram,
                'layout' => "{items}\n{pager}",
                //'filterModel' => $searchModel,
                'columns' => [
                    ['class' => 'yii\grid\SerialColumn'],
                    'prog_name',
                    [
                     'label' => 'Program Anjuran',
                     'value' => function($model){
                        return $model->anjuranText;
                     }
                    ],
                    [
                     'label' => 'Date Program',
                     'value' => function($model){
                        return date('d M Y', strtotime($model->prog_date));
                     }
                    ],
                ],
            ]); ?>

            
          </div>
        </div>
    </div>
</div>
