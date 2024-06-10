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

$this->title = 'User Role Request';
$this->params['breadcrumbs'][] = $this->title;
?>
  <div class="pagetitle">
<h1><?=$this->title?></h1></div>

    </div><!-- End Page Title -->

    <section class="section dashboard">
    <?php  echo $this->render('_search', ['model' => $searchModel]); ?>



    <?php $form = ActiveForm::begin(); ?>
    <div class="card">
            <div class="card-body pt-4">
    <p>
    <?= Html::submitButton('APPROVE SELECTED', ['data-confirm' => 'Are you sure to approve the selected request?','class' => 'btn btn-success', 'name' => 'actiontype', 'value' => 'approve']) ?> 
    <?= Html::submitButton('REVOKE SELECTED', ['data-confirm' => 'Are you sure to revole the selected request?','class' => 'btn btn-warning', 'name' => 'actiontype', 'value' => 'revoke']) ?>
    </p>
            <div class="table-responsive">

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        //'filterModel' => $searchModel,
                'pager' => [
            'class' => 'yii\bootstrap5\LinkPager',
        ],
        'columns' => [
            ['class' => 'yii\grid\CheckboxColumn'],
            ['class' => 'yii\grid\SerialColumn'],

            [
                'label' =>'Name',
                'attribute' => 'fullname',
                'value' => function($model){
                    return $model->user->fullname;
                }
            ],
            [
                'label' =>'Role',
                'attribute' => 'role_name',
                'format' => 'html',
                'value' => function($r){
                    $str = $r->roleText;
                    if($r->role_name == 'manager'){
                        if($r->program){
                            $sub = '';
                            if($r->programSub){
                              $sub = '/' . $r->programSub->sub_name;
                            }
                            $str .=  '<br />('.$r->program->program_abbr. $sub . ')';
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
                'attribute' => 'status',
                'format' => 'html',
                'value' => function($model){
                    return $model->statusLabel;
                }
            ],
            [
                'label' =>'Date Time',
                'attribute' => 'request_at',
                'value' => function($model){
                    return $model->request_at;
                }
            ],
            ['class' => 'yii\grid\ActionColumn',
            'template' => '{view}',
            //'visible' => false,
            'buttons'=>[
                'view'=>function ($url, $model) {
                    return Html::a('<span class="bi bi-trash"></span>',['delete-role', 'id' => $model->id],['data-confirm' => 'Are you sure to delete this user role']);
                },
            ],
        
        ], 
  
        ],
    ]); ?>

</div>
            </div>
        </div>
        <?php ActiveForm::end(); ?>


    </section>
