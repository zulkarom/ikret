<?php

use yii\grid\GridView;
use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\UserRole $role */
/** @var app\models\ProgramSub|null $programSub */
/** @var app\models\JuryApplicationSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$program = $role->program;
$sub_str = $programSub ? ' / ' . $programSub->sub_abbr : '';

$this->title = 'Jury Applications (' . $program->program_abbr . $sub_str . ')';
$this->params['breadcrumbs'][] = [
    'label' => $program->program_abbr . $sub_str,
    'url' => ['/program-registration/manager-dashboard', 'id' => $program->id, 'sub' => $programSub ? $programSub->id : null]
];
$this->params['breadcrumbs'][] = $this->title;

?>

<div class="pagetitle">
    <h1><?=$this->title?></h1>
</div>

</div><!-- End Page Title -->

<section class="section">
    <div class="card">
        <div class="card-body pt-4">

            <div class="table-responsive">
                <?= GridView::widget([
                    'dataProvider' => $dataProvider,
                    'pager' => [
                        'class' => 'yii\\bootstrap5\\LinkPager',
                    ],
                    'columns' => [
                        ['class' => 'yii\\grid\\SerialColumn'],
                        [
                            'label' => 'Name',
                            'attribute' => 'fullname',
                            'value' => function($model){
                                return $model->juryProfile ? $model->juryProfile->fullname : null;
                            }
                        ],
                        [
                            'label' => 'Email',
                            'attribute' => 'email',
                            'value' => function($model){
                                if($model->juryProfile && $model->juryProfile->user){
                                    return $model->juryProfile->user->email;
                                }
                                return null;
                            }
                        ],
                        [
                            'label' => 'Category',
                            'value' => function($model){
                                return $model->juryProfile ? $model->juryProfile->category : null;
                            }
                        ],
                        [
                            'label' => 'Session',
                            'value' => function($model){
                                return $model->judgingSession ? $model->judgingSession->session_name : null;
                            }
                        ],
                        [
                            'label' => 'Status',
                            'value' => function($model){
                                return $model->statusText;
                            }
                        ],
                        [
                            'label' => 'Applied At',
                            'value' => function($model){
                                return $model->created_at ? date('Y-m-d H:i', $model->created_at) : null;
                            }
                        ],
                    ],
                ]); ?>
            </div>

        </div>
    </div>
</section>
