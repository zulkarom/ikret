<?php

use app\models\CertificateTemplate;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var yii\data\ActiveDataProvider $dataProvider */

$name = $name ?? '';
$programId = $programId ?? null;
$programSubId = $programSubId ?? null;
$achievementId = $achievementId ?? null;
$programList = $programList ?? [];
$programSubList = $programSubList ?? [];
$achievementList = $achievementList ?? [];

$this->title = 'Achievement List';
$this->params['breadcrumbs'][] = ['label' => 'Certificate Config', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="pagetitle">
    <h1><?= Html::encode($this->title) ?></h1>
</div>

<?php $published = CertificateTemplate::isPublished(4); ?>
<div class="alert <?= $published ? 'alert-success' : 'alert-warning' ?>" role="alert">
    Achievement certificate template: <b><?= $published ? 'PUBLISHED' : 'NOT PUBLISHED' ?></b><br>
    Note: Admin can view this page anytime. Winner title is optional; if not assigned it will show as "-".
</div>

</div><!-- End Page Title -->

<section class="section dashboard">
    <div class="card">
        <div class="card-body pt-4">
            <div class="mb-3">
                <?php $form = ActiveForm::begin([
                    'method' => 'get',
                    'action' => ['achievement-list'],
                    'options' => ['class' => 'row g-2 align-items-end'],
                ]); ?>

                <div class="col-12 col-md-3">
                    <label class="form-label">Name</label>
                    <?= Html::textInput('name', $name, ['class' => 'form-control']) ?>
                </div>

                <div class="col-12 col-md-3">
                    <label class="form-label">Program</label>
                    <?= Html::dropDownList('program_id', $programId, $programList, ['class' => 'form-select', 'prompt' => '- All -']) ?>
                </div>

                <div class="col-12 col-md-3">
                    <label class="form-label">Sub Program</label>
                    <?= Html::dropDownList('program_sub', $programSubId, $programSubList, ['class' => 'form-select', 'prompt' => '- All -']) ?>
                </div>

                <div class="col-12 col-md-3">
                    <label class="form-label">Achievement</label>
                    <?= Html::dropDownList('achievement_id', $achievementId, $achievementList, ['class' => 'form-select', 'prompt' => '- All -']) ?>
                </div>

                <div class="col-12 col-md-2">
                    <?= Html::submitButton('Filter', ['class' => 'btn btn-primary w-100']) ?>
                    <div class="mt-1">
                        <?= Html::a('Reset', ['achievement-list'], ['class' => 'btn btn-outline-secondary w-100']) ?>
                    </div>
                </div>

                <?php ActiveForm::end(); ?>
            </div>

            <div class="table-responsive">
                <?= GridView::widget([
                    'dataProvider' => $dataProvider,
                    'pager' => [
                        'class' => 'yii\bootstrap5\LinkPager',
                    ],
                    'columns' => [
                        ['class' => 'yii\grid\SerialColumn'],
                        [
                            'label' => 'Program',
                            'format' => 'raw',
                            'value' => function($model){
                                return $model->registration ? $model->registration->programNameLong : '-';
                            },
                        ],
                        [
                            'label' => 'Participant',
                            'format' => 'html',
                            'value' => function($model){
                                return ($model->registration && $model->registration->user) ? $model->registration->user->fullname : '-';
                            },
                        ],
                        [
                            'label' => 'Group / Project',
                            'format' => 'raw',
                            'value' => function($model){
                                if(!$model->registration){
                                    return '-';
                                }
                                $group = trim((string)$model->registration->group_name);
                                $project = trim((string)$model->registration->project_name);
                                if($group !== '' && $project !== ''){
                                    return Html::encode($group) . '<br>' . Html::encode($project);
                                }
                                if($group !== ''){
                                    return Html::encode($group);
                                }
                                if($project !== ''){
                                    return Html::encode($project);
                                }
                                return '-';
                            },
                        ],
                        [
                            'label' => 'Achievement',
                            'format' => 'raw',
                            'value' => function($model){
                                return $model->achieve ? Html::encode($model->achieve->name) : '-';
                            },
                        ],
                        [
                            'label' => 'Winner Title',
                            'format' => 'raw',
                            'value' => function($model){
                                return $model->winnerTitle ? Html::encode($model->winnerTitle->title_name) : '-';
                            },
                        ],
                        [
                            'label' => 'Certificate',
                            'format' => 'raw',
                            'value' => function($model){
                                if(!$model->registration){
                                    return '-';
                                }
                                return Html::a('Achievement', ['/program/cert-achievement', 'reg' => $model->registration->id], [
                                    'class' => 'btn btn-outline-primary btn-sm',
                                    'target' => '_blank',
                                ]);
                            },
                        ],
                    ],
                ]) ?>
            </div>
        </div>
    </div>
</section>
