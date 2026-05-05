<?php

use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use app\models\JuryApplication;
use app\models\JuryProfile;
use yii\helpers\Url;

/** @var yii\web\View $this */
/** @var app\models\UserRole|null $role */
/** @var app\models\Program|null $program */
/** @var app\models\ProgramSub|null $programSub */
/** @var app\models\JuryApplicationSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$program = $program ?? ($role ? $role->program : null);
$programSub = $programSub ?? null;
$sub_str = $programSub ? ' / ' . $programSub->sub_abbr : '';

$categories = JuryProfile::find()
    ->select(['category'])
    ->distinct()
    ->where(['not', ['category' => null]])
    ->andWhere(['<>', 'category', ''])
    ->orderBy(['category' => SORT_ASC])
    ->column();
$categoryFilter = $categories ? array_combine($categories, $categories) : [];

$this->title = $program ? ('Jury Applications (' . $program->program_abbr . $sub_str . ')') : 'Jury Applications';
if($program){
    $this->params['breadcrumbs'][] = [
        'label' => $program->program_abbr . $sub_str,
        'url' => ['/program-registration/manager-dashboard', 'id' => $program->id, 'sub' => $programSub ? $programSub->id : null]
    ];
}
$this->params['breadcrumbs'][] = $this->title;

?>

<div class="pagetitle">
    <h1><?=$this->title?></h1>
</div>

</div><!-- End Page Title -->

<section class="section">
    <div class="card">
        <div class="card-body pt-4">

            <?= Html::beginForm(['/program-registration/jury-application-bulk-update'], 'post') ?>

            <div class="mb-3">
                <?= Html::submitButton('Approve Selected', ['class' => 'btn btn-success', 'name' => 'bulk_action', 'value' => 'approve']) ?>
                <?= Html::submitButton('Reject Selected', ['class' => 'btn btn-danger', 'name' => 'bulk_action', 'value' => 'reject', 'data' => ['confirm' => 'Reject selected applications?']]) ?>
                <?php if(!Yii::$app->user->isGuest && Yii::$app->user->identity->isAdmin): ?>
                    <?= Html::a('Import CSV', ['/program-registration/jury-application-import'], ['class' => 'btn btn-outline-primary']) ?>
                <?php endif; ?>
            </div>

            <div class="table-responsive">
                <?= GridView::widget([
                    'dataProvider' => $dataProvider,
                    'filterModel' => $searchModel,
                    'pager' => [
                        'class' => 'yii\\bootstrap5\\LinkPager',
                    ],
                    'columns' => [
                        ['class' => 'yii\\grid\\SerialColumn'],
                        [
                            'class' => 'yii\\grid\\CheckboxColumn',
                        ],
                        [
                            'label' => 'Program / Sub',
                            'attribute' => 'program_abbr',
                            'format' => 'raw',
                            'value' => function($model){
                                $name = $model->program ? $model->program->program_abbr : null;
                                if($model->programSub){
                                    $sub = $model->programSub->sub_abbr;
                                    if($name){
                                        $name = $name . ' / ' . $sub;
                                    }else{
                                        $name = $sub;
                                    }
                                }

                                if($model->judgingSession){
                                    $s = $model->judgingSession;
                                    $start = $s->datetime_start ? new \DateTime($s->datetime_start) : null;
                                    $end = $s->datetime_end ? new \DateTime($s->datetime_end) : null;

                                    $range = '';
                                    if($start && $end){
                                        if($start->format('Y-m-d') === $end->format('Y-m-d')){
                                            $range = $start->format('d M Y') . ' ' . $start->format('H:i') . ' - ' . $end->format('H:i');
                                        }else{
                                            $range = $start->format('d M Y H:i') . ' - ' . $end->format('d M Y H:i');
                                        }
                                    }elseif($start){
                                        $range = $start->format('d M Y H:i');
                                    }

                                    $sessionText = $s->session_name;
                                    if($range !== ''){
                                        $sessionText .= ' (' . $range . ')';
                                    }

                                    if($name){
                                        return $name . '<br />' . $sessionText;
                                    }
                                    return $sessionText;
                                }

                                return $name;
                            }
                        ],
                        [
                            'label' => 'Name / Email',
                            'attribute' => 'fullname',
                            'format' => 'raw',
                            'value' => function($model){
                                $name = $model->juryProfile ? $model->juryProfile->fullname : null;
                                $email = null;
                                if($model->juryProfile && $model->juryProfile->user){
                                    $email = $model->juryProfile->user->email;
                                }

                                if($name && $email){
                                    return $name . '<br />' . $email;
                                }
                                return $name ?: $email;
                            }
                        ],
                        [
                            'label' => 'Category',
                            'attribute' => 'category',
                            'filter' => $categoryFilter,
                            'filterInputOptions' => ['class' => 'form-select', 'prompt' => 'All'],
                            'value' => function($model){
                                return $model->juryProfile ? $model->juryProfile->category : null;
                            }
                        ],
                        [
                            'label' => 'Status',
                            'attribute' => 'status',
                            'format' => 'raw',
                            'filter' => JuryApplication::listStatus(),
                            'filterInputOptions' => ['class' => 'form-select', 'prompt' => 'All'],
                            'value' => function($model){
                                $text = $model->statusText;
                                $class = 'bg-secondary';
                                if((int)$model->status === 10){
                                    $class = 'bg-success';
                                }elseif((int)$model->status === 20){
                                    $class = 'bg-danger';
                                }
                                return Html::tag('span', $text, ['class' => 'badge ' . $class]);
                            }
                        ],
                        [
                            'label' => 'Applied At',
                            'attribute' => 'created_at',
                            'value' => function($model){
                                return $model->created_at ? date('Y-m-d H:i', $model->created_at) : null;
                            }
                        ],
                        [
                            'class' => 'yii\grid\ActionColumn',
                            'template' => '{view}',
                            'buttons' => [
                                'view' => function($url, $model){
                                    return Html::a('View', ['/program-registration/jury-application-view', 'id' => $model->id], ['class' => 'btn btn-secondary btn-sm']);
                                },
                            ],
                        ],
                    ],
                ]); ?>
            </div>

            <?= Html::endForm() ?>

        </div>
    </div>
</section>
