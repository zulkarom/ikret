<?php

use app\models\Program;
use app\models\CertificateTemplate;
use yii\grid\GridView;
use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'All Juries';
$this->params['breadcrumbs'][] = ['label' => 'Certificate Config', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="pagetitle">
    <h1><?= Html::encode($this->title) ?></h1>
</div>

<?php $published = CertificateTemplate::isPublished(3); ?>
<div class="alert <?= $published ? 'alert-success' : 'alert-warning' ?>" role="alert">
    Jury certificate template: <b><?= $published ? 'PUBLISHED' : 'NOT PUBLISHED' ?></b><br>
    Note: Admin can view this page anytime. Jury certificate PDF is only available when the jury assignment is marked <b>COMPLETE</b>.
</div>

</div><!-- End Page Title -->

<section class="section dashboard">
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
                            'label' => 'Jury',
                            'value' => function($model){
                                return $model->user ? $model->user->fullname : '';
                            },
                        ],
                        [
                            'label' => 'Program',
                            'format' => 'html',
                            'value' => function($model){
                                return Program::programNameLong($model->program_id, $model->program_sub);
                            },
                        ],
                        [
                            'label' => 'Status',
                            'format' => 'raw',
                            'value' => function($model){
                                return (int)$model->status === 20
                                    ? '<span class="badge bg-success">COMPLETE</span>'
                                    : '<span class="badge bg-warning">IN PROGRESS</span>';
                            },
                        ],
                        [
                            'label' => 'Certificate',
                            'format' => 'raw',
                            'value' => function($model){
                                $url = [
                                    '/program-registration/jury-cert-pdf',
                                    'p' => $model->program_id,
                                    's' => $model->program_sub,
                                    'u' => $model->user_id,
                                ];

                                if((int)$model->status !== 20){
                                    $btn = Html::a('Certificate', $url, ['class' => 'btn btn-outline-primary btn-sm', 'target' => '_blank']);
                                    return $btn . '<div class="small text-muted">Not generated for the jury yet</div>';
                                }

                                return Html::a('Certificate', $url, ['class' => 'btn btn-primary btn-sm', 'target' => '_blank']);
                            },
                        ],
                    ],
                ]) ?>
            </div>
        </div>
    </div>
</section>
