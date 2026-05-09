<?php

use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;

/** @var yii\web\View $this */
/** @var yii\data\ActiveDataProvider $dataProvider */
/** @var bool $hasWinnerCountColumn */

$this->title = 'Achievement Config';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="pagetitle">
    <h1><?= Html::encode($this->title) ?></h1>
</div>

</div><!-- End Page Title -->

<section class="section dashboard">
    <div class="mb-3">
        <?= Html::a('<i class="bi bi-upload"></i> Import CSV', ['achievement-import'], ['class' => 'btn btn-primary']) ?>
    </div>
    <div class="card">
        <div class="card-body pt-4">
            <?= Html::beginForm(Url::to(['certificate-template/achievement-config']), 'post') ?>
            <?= Html::hiddenInput(Yii::$app->request->csrfParam, Yii::$app->request->getCsrfToken()) ?>
            <div class="table-responsive">
                <?= GridView::widget([
                    'dataProvider' => $dataProvider,
                    'pager' => [
                        'class' => 'yii\bootstrap5\LinkPager',
                    ],
                    'columns' => [
                        [
                            'class' => 'yii\grid\CheckboxColumn',
                            'checkboxOptions' => function($model){
                                return ['value' => $model->id];
                            },
                        ],
                        ['class' => 'yii\grid\SerialColumn'],
                        [
                            'label' => 'Achievement Name',
                            'value' => 'name',
                        ],
                        [
                            'label' => 'Program / Sub',
                            'value' => function($model){
                                $program = '';
                                if($model->program){
                                    $program = $model->program->program_abbr ?: $model->program->program_name;
                                }
                                if($model->programSub){
                                    return $program . ' / ' . $model->programSub->sub_name;
                                }

                                return $program;
                            },
                        ],
                        [
                            'label' => 'Number of Winner',
                            'visible' => $hasWinnerCountColumn,
                            'value' => function($model){
                                return $model->winner_count === null || $model->winner_count === '' ? '' : $model->winner_count;
                            },
                        ],
                    ],
                ]) ?>
            </div>
            <div class="mt-3">
                <?= Html::submitButton('Delete Selected', [
                    'class' => 'btn btn-outline-danger',
                    'data-confirm' => 'Delete selected achievements? Achievements already used will be skipped.',
                ]) ?>
            </div>
            <?= Html::endForm() ?>
        </div>
    </div>
</section>
