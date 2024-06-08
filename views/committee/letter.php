<?php

use app\models\Program;
use app\models\ProgramRegistration;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\ProgramRegistrationSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Committee';
$this->params['breadcrumbs'][] = $this->title;
?>
  <div class="pagetitle">
<h1><?=$this->title?></h1></div>

    </div><!-- End Page Title -->

    <section class="section dashboard">
    <?php $form = ActiveForm::begin(); ?>
    <div class="card">
            <div class="card-body pt-4">
            <div class="table-responsive">

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
                'pager' => [
            'class' => 'yii\bootstrap5\LinkPager',
        ],
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            [
                'label' =>'Name',
                'value' => function($model){
                    return $model->user->fullname;
                }
            ],
            [
                'label' =>'Role',
                'format' => 'html',
                'value' => function($r){
                    $str = $r->roleText;
                    if($r->role_name == 'manager'){
                        if($r->program){
                            $str .= '<br />('.$r->program->program_abbr.')';
                        }
                      }
                      if($r->role_name == 'committee'){
                        if($r->committee){
                            $str .= '<br />('.$r->committee->com_name.')';
                          if($r->committee->is_jawatankuasa == 1){
                            if($r->is_leader == 1){
                                $str .= '<b> - Leader</b>';
                            }else{
                                $str .= '<b> - Member</b>';
                            }
                            
                          }
                        }
                      }

                    return $str;
                }
            ],
            [
                'label' =>'Status',
                'format' => 'html',
                'value' => function($model){
                    return $model->statusLabel;
                }
            ],
            [
                'label' =>'',
                'attribute' => 'dateSearch',
                'format' => 'raw',
                'value' => function($model){
                    return Html::a('Download',['letter-pdf','id' => $model->id],['class' => 'btn btn-info', 'target' => '_blank']);
                }
            ],
            /* ['class' => 'yii\grid\ActionColumn',
                'contentOptions' => ['style' => 'width: 13%'],
            'template' => '{view}',
            //'visible' => false,
            'buttons'=>[
                'view'=>function ($url, $model) {
                    return Html::a('<span class="bi bi-eye"></span> View',['view', 'id' => $model->id],['class'=>'btn btn-primary btn-sm']);
                },
            ],
        
        ], */
  
        ],
    ]); ?>

</div>
            </div>
        </div>
        <?php ActiveForm::end(); ?>


    </section>
