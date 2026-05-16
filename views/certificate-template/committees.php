<?php

use app\models\CertificateTemplate;
use app\models\Committee;
use yii\grid\GridView;
use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var yii\data\ActiveDataProvider $dataProvider */
/** @var app\models\CommitteeCertificateSearch $searchModel */

$this->title = 'All Committees';
$this->params['breadcrumbs'][] = ['label' => 'Certificate Config', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="pagetitle">
    <h1><?= Html::encode($this->title) ?></h1>
</div>

<?php $published = CertificateTemplate::isPublished(2); ?>
<div class="alert <?= $published ? 'alert-success' : 'alert-warning' ?>" role="alert">
    Committee certificate template: <b><?= $published ? 'PUBLISHED' : 'NOT PUBLISHED' ?></b><br>
    Note: Admin can view this page anytime. Non-admin users can only view/download certificates when certificates are released and the template is published.
</div>

</div><!-- End Page Title -->

<section class="section dashboard">
    <div class="card">
        <div class="card-body pt-4">
            <div class="table-responsive">
                <?= GridView::widget([
                    'dataProvider' => $dataProvider,
                    'filterModel' => $searchModel,
                    'pager' => [
                        'class' => 'yii\bootstrap5\LinkPager',
                    ],
                    'columns' => [
                        ['class' => 'yii\grid\SerialColumn'],
                        [
                            'label' => 'Name',
                            'value' => function($model){
                                return $model->user ? $model->user->fullname : '';
                            },
                            'attribute' => 'fullname',
                        ],
                        [
                            'label' => 'Committee',
                            'format' => 'raw',
                            'value' => function($model){
                                $text = $model->committee ? Html::encode($model->committee->com_name_en) : '';
                                if($model->committee && (int)$model->committee->is_jawatankuasa === 1){
                                    $text .= (int)$model->is_leader === 1 ? ' <b>- Leader</b>' : ' <b>- Member</b>';
                                }

                                return $text;
                            },
                            'attribute' => 'committee_id',
                            'filter' => Html::activeDropDownList($searchModel, 'committee_id', Committee::listCommittees(), [
                                'class' => 'form-control',
                                'prompt' => '',
                            ]),
                        ],
                        [
                            'label' => 'Role',
                            'value' => function($model){
                                if(!$model->committee){
                                    return '';
                                }
                                if((int)$model->committee->is_jawatankuasa !== 1){
                                    return 'No Role';
                                }
                                return (int)$model->is_leader === 1 ? 'Leader' : 'Member';
                            },
                            'attribute' => 'committee_role',
                            'filter' => Html::activeDropDownList($searchModel, 'committee_role', [
                                'none' => 'No Role',
                                'leader' => 'Leader',
                                'member' => 'Member',
                            ], [
                                'class' => 'form-control',
                                'prompt' => '',
                            ]),
                        ],
                        [
                            'label' => 'Certificate',
                            'format' => 'raw',
                            'value' => function($model){
                                return Html::a('Certificate', ['/committee/certificate', 'id' => $model->id], ['class' => 'btn btn-primary btn-sm', 'target' => '_blank']);
                            },
                        ],
                    ],
                ]) ?>
            </div>
        </div>
    </div>
</section>
