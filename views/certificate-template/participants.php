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
$programList = $programList ?? [];
$programSubList = $programSubList ?? [];

$this->title = 'All Participants';
$this->params['breadcrumbs'][] = ['label' => 'Certificate Config', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="pagetitle">
    <h1><?= Html::encode($this->title) ?></h1>
</div>

<?php
    $publishedParticipation = CertificateTemplate::isPublished(1);
    $publishedAchievement = CertificateTemplate::isPublished(4);
    $publishedExcellence = CertificateTemplate::isPublished(5);
?>

<div class="alert <?= ($publishedParticipation && $publishedAchievement && $publishedExcellence) ? 'alert-success' : 'alert-warning' ?>" role="alert">
    Participation template: <b><?= $publishedParticipation ? 'PUBLISHED' : 'NOT PUBLISHED' ?></b><br>
    Achievement template: <b><?= $publishedAchievement ? 'PUBLISHED' : 'NOT PUBLISHED' ?></b><br>
    Excellence template: <b><?= $publishedExcellence ? 'PUBLISHED' : 'NOT PUBLISHED' ?></b><br>
    Note: Admin can view these pages anytime. Non-admin users can only view/download certificates when certificates are released and the template is published.
</div>

</div><!-- End Page Title -->

<section class="section dashboard">
    <div class="card">
        <div class="card-body pt-4">
            <div class="mb-3">
                <?php $form = ActiveForm::begin([
                    'method' => 'get',
                    'action' => ['participants'],
                    'options' => ['class' => 'row g-2 align-items-end'],
                ]); ?>

                <div class="col-12 col-md-4">
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

                <div class="col-12 col-md-2">
                    <?= Html::submitButton('Filter', ['class' => 'btn btn-primary w-100']) ?>
                    <div class="mt-1">
                        <?= Html::a('Reset', ['participants'], ['class' => 'btn btn-outline-secondary w-100']) ?>
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
                            'label' => 'Participant',
                            'format' => 'html',
                            'value' => function($model){
                                return $model->shortFieldsHtml;
                            },
                        ],
                        [
                            'label' => 'Members',
                            'format' => 'raw',
                            'value' => function($model){
                                return $model->memberStr;
                            },
                        ],
                        [
                            'label' => 'Certificates',
                            'format' => 'raw',
                            'value' => function($model){
                                $links = [];
                                $links[] = Html::a('Participation', ['/program/cert-participation', 'reg' => $model->id], ['class' => 'btn btn-primary btn-sm mb-1', 'target' => '_blank']);

                                if($model->achievements){
                                    foreach($model->achievements as $achievement){
                                        $label = $achievement->achieve ? 'Achievement: ' . $achievement->achieve->name : 'Achievement';
                                        $links[] = Html::a(Html::encode($label), ['/program/cert-achievement', 'reg' => $model->id, 'achieve' => $achievement->achieve_id], ['class' => 'btn btn-outline-primary btn-sm mb-1', 'target' => '_blank']);
                                    }
                                }

                                return implode(' ', $links);
                            },
                        ],
                    ],
                ]) ?>
            </div>
        </div>
    </div>
</section>
