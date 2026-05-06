<?php

use kartik\export\ExportMenu;
use kartik\form\ActiveForm;
//use yii\bootstrap5\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
use app\models\JuryAssign;

/** @var yii\web\View $this */
/** @var app\models\ProgramRegistrationSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */
$program = $role->program;
$sub_str = $programSub? ' / ' . $programSub->sub_abbr  : '';
$this->title = 'Registration ('.$program->program_abbr . $sub_str.')';
$this->params['breadcrumbs'][] = [
    'label' => $program->program_abbr . $sub_str,
    'url' => ['/program-registration/manager-dashboard', 'id' => $program->id, 'sub' => $programSub ? $programSub->id : null]
];
$this->params['breadcrumbs'][] = $this->title;

$this->registerCss(<<<CSS
.jury-summary-grid {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 0.75rem;
    margin-bottom: 1rem;
}

.jury-summary-card {
    display: block;
    padding: 1rem;
    border: 1px solid #e5e7eb;
    border-radius: 0.9rem;
    background: #fff;
    color: inherit;
    text-decoration: none;
    box-shadow: 0 0.35rem 1rem rgba(15, 23, 42, 0.06);
    transition: transform 0.15s ease, box-shadow 0.15s ease, border-color 0.15s ease;
}

.jury-summary-card:hover {
    color: inherit;
    text-decoration: none;
    transform: translateY(-1px);
    box-shadow: 0 0.5rem 1.25rem rgba(15, 23, 42, 0.1);
}

.jury-summary-card.active {
    border-color: #0d6efd;
    box-shadow: 0 0.5rem 1.25rem rgba(13, 110, 253, 0.16);
}

.jury-summary-label {
    color: #6c757d;
    font-size: 0.85rem;
    font-weight: 700;
    letter-spacing: 0.04em;
    text-transform: uppercase;
}

.jury-summary-count {
    margin-top: 0.25rem;
    font-size: 2rem;
    font-weight: 800;
    line-height: 1;
}

@media (max-width: 575.98px) {
    .jury-summary-grid {
        grid-template-columns: 1fr;
    }
}
CSS);
?>
  <div class="pagetitle">
<h1><?=$this->title?></h1>
</div>

    </div><!-- End Page Title -->

    <section class="section dashboard">
        <?php
        $statusList = JuryAssign::getStatusArray();
        $statusColors = JuryAssign::getStatusColor();
        $selectedStatus = $searchModel->jury_status;
        ?>
        <div class="jury-summary-grid">
            <?php foreach($statusList as $status => $label): ?>
                <?php
                $summaryUrlParams = Yii::$app->request->queryParams;
                unset($summaryUrlParams['page'], $summaryUrlParams['per-page']);
                $summaryUrlParams[0] = 'manager';
                $summaryUrlParams['id'] = $program->id;
                if($programSub){
                    $summaryUrlParams['sub'] = $programSub->id;
                }
                $summaryUrlParams[$searchModel->formName()]['jury_status'] = $status;
                $isActive = (string)$selectedStatus !== '' && (int)$selectedStatus === (int)$status;
                ?>
                <?= Html::a(
                    '<div class="jury-summary-label">' . Html::encode($label) . '</div>' .
                    '<div class="jury-summary-count text-' . Html::encode($statusColors[$status] ?? 'secondary') . '">' . Html::encode($juryStatusSummary[$status] ?? 0) . '</div>',
                    Url::to($summaryUrlParams),
                    ['class' => 'jury-summary-card' . ($isActive ? ' active' : '')]
                ) ?>
            <?php endforeach; ?>
        </div>

        <?php if((string)$selectedStatus !== ''): ?>
            <div class="mb-3">
                <?= Html::a('Clear Status Filter', ['manager', 'id' => $program->id, 'sub' => $programSub ? $programSub->id : null], ['class' => 'btn btn-outline-secondary btn-sm']) ?>
            </div>
        <?php endif; ?>

        <div class="form-group"><?=Html::button('Filter Form',['id' => 'btn-filter-form','class' => 'btn btn-info'])?> 
        <?=Html::button('Jury Assignment Form',['id' => 'btn-jury-form', 'class' => 'btn btn-primary'])?> 
    </div> 

        <?php
        $this->registerJs('
        $("#dwl-exl").click(function(){
            $("#w0-xls")[0].click();
        });
        ');
        ?>

        <?php
        $this->registerJs('
            $("#btn-jury-form").click(function(){
                $("#con-jury-form").slideDown();
                $("#con-filter-form").slideUp();
            });
            $("#hide-jury-form").click(function(){
                $("#con-jury-form").slideUp();
              
            });

            $("#btn-filter-form").click(function(){
                $("#con-filter-form").slideDown();
                $("#con-jury-form").slideUp();
            });
            $("#hide-filter-form").click(function(){
                $("#con-filter-form").slideUp();
            });
        ');
        
        ?>
    
    <br />

    <div class="card" style="display:none" id="con-filter-form">
    <div class="card-header">Filter Form</div>
    <div class="card-body pt-4">
    <?= $this->render('_search', [
        'model' => $searchModel,
        'programSub' => $programSub,
        'action' => 'manager'
    ]) ?>
</div></div>

    <?php 
    $fstyle = 'style="display:none"';
    $session = Yii::$app->session;
    if ($session->has('keep-open') && $session->get('keep-open') == 1){
        $fstyle = '';
    }
    $form = ActiveForm::begin(['id' => 'jury-assign-form']); ?>
    <div class="card" <?=$fstyle?> id="con-jury-form">
    <div class="card-header">Jury Assignment Form</div>
    <div class="card-body pt-4">
    <?= $this->render('_form_jury', [
        'model' => $model,
        'form' => $form,
        'program' => $program,
        'programSub' => $programSub,
    ]) ?>
</div></div>




    <div class="card">
            <div class="card-body pt-4">
            <div class="table-responsive">

    <?php
    $colums[] = ['class' => 'yii\grid\CheckboxColumn'];
    $colums[] = ['class' => 'yii\grid\SerialColumn'];

    /* $colums[] = [
        'label' =>'Date Time',
        'attribute' => 'dateSearch',
        'value' => function($model){
            return $model->submitted_at;
        }
    ]; */
    

    if(true){ //$program->id == 1
        $colums[] = [
            'label' =>'Participants',
            'attribute' => 'fullnameSearch',
            'format' => 'html',
            'value' => function($model){
                $html = '';
                if($model->flag == 1){
                    $html .= '<i class="bi bi-flag-fill" style="color:blue"></i> ';
                }
                $text = $model->participantText;
                if(!empty($model->group_name)){
                    $text .= ' (' . $model->group_name . ')';
                }
                $html .= Html::encode($text);
                return $html;
            }
        ];

        $colums[] = [
            'label' =>'Assigned Juries',
            'format' => 'raw',
            'value' => function($model){
                $juries = $model->juries;
                $html = '';
                if($juries){
                    $html .= '<ul>';
                    foreach($juries as $jury){
                        $html .= $jury->infoHtml(true);
                    }
                    $html .= '<ul>';
                }
                return $html;
            }
        ];
    }

    $colums[] = ['class' => 'yii\grid\ActionColumn',
'template' => '{view} {flag}',
//'visible' => false,
'buttons'=>[
    'view'=>function ($url, $model) {
        $url = ['manager-view', 'id' => $model->id];
        if($model->programSub){
            $url = ['manager-view', 'id' => $model->id, 'sub' => $model->programSub->id];
        }
        return Html::a('<span class="bi bi-eye"></span> View',$url,['class'=>'btn btn-primary btn-sm']);
    },
    'flag'=>function ($url, $model) {
        if($model->flag == 0){
            $url = ['manager-flag', 'id' => $model->id];
            if($model->programSub){
                $url = ['manager-flag', 'id' => $model->id, 'flag' => 1, 'sub' => $model->programSub->id];
            }
            return Html::a('<span class="bi bi-flag"></span> Flag', $url,['class'=>'btn btn-warning btn-sm']);
        }else if($model->flag == 1){
            $url = ['manager-flag', 'id' => $model->id];
            if($model->programSub){
                $url = ['manager-flag', 'id' => $model->id,'flag' => 0, 'sub' => $model->programSub->id];
            }
            return Html::a('<span class="bi bi-flag"></span> Unflag', $url,['class'=>'btn btn-outline-warning btn-sm']);
        }
    },
],

];
    
    echo GridView::widget([
        'dataProvider' => $dataProvider,
                'pager' => [
            'class' => 'yii\bootstrap5\LinkPager',
        ],
                'pager' => [
            'class' => 'yii\bootstrap5\LinkPager',
        ],
        //'filterModel' => $searchModel,
        'columns' => $colums,
    ]); ?>

    <div class="mt-3">
        <?= Html::button('Delete Selected Judging Input', [
            'id' => 'btn-reset-judging-input',
            'class' => 'btn btn-outline-warning',
        ]) ?>
    </div>

</div>
            </div>
        </div>


        <?php ActiveForm::end(); ?>
        <?= Html::beginForm(['manager', 'id' => $program->id, 'sub' => $programSub ? $programSub->id : null], 'post', ['id' => 'reset-judging-input-form', 'style' => 'display:none']) ?>
            <?= Html::hiddenInput('action', 'reset_judging_input') ?>
        <?= Html::endForm() ?>
        <?php
        $this->registerJs(<<<JS
$("#btn-reset-judging-input").on("click", function(){
    var selected = $("input[name='selection[]']:checked").map(function(){
        return $(this).val();
    }).get();

    if(selected.length === 0){
        alert("Please select at least one participant first.");
        return false;
    }

    if(!confirm("Delete judging input for selected participants and revert their jury assignments to ASSIGNED?")){
        return false;
    }

    var form = $("#reset-judging-input-form");
    form.find("input[name='selection[]']").remove();
    selected.forEach(function(id){
        $("<input>", {
            type: "hidden",
            name: "selection[]",
            value: id
        }).appendTo(form);
    });
    form[0].submit();
});
JS);
        ?>
        <i>* if you want to select multiple participants, but they are in different pages, flag them first to be on top of the list first.</i>
    </section>
