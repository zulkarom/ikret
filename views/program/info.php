<?php

use yii\bootstrap5\ActiveForm;
use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var yii\db\ActiveRecord $model */
/** @var app\models\Program|null $program */
/** @var app\models\ProgramSub|null $programSub */

$this->title = 'Update Program Info';

$programSub = $programSub ?? null;
$program = $program ?? ($programSub ? $programSub->program : null);

$breadcrumbUrl = ['/program-registration/manager-dashboard', 'id' => $program ? $program->id : null, 'sub' => $programSub ? $programSub->id : null];
if($program && (int)$program->has_sub === 1 && !$programSub){
    $breadcrumbUrl = ['/program-registration/manager-parent', 'id' => $program->id];
}

$this->params['breadcrumbs'][] = [
    'label' => ($program ? $program->program_abbr : '') . ($programSub ? ' / ' . $programSub->sub_name : ''),
    'url' => $breadcrumbUrl
];
$this->params['breadcrumbs'][] = $this->title;
?>


<div class="pagetitle">
<h1><?=$this->title?></h1></div>

    </div><!-- End Page Title -->

    <section class="section dashboard">

    <div class="card">
            <div class="card-body pt-4">

    <?php if($programSub){ ?>
        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'sub_name')->textInput(['maxlength' => true]) ?>
        <?= $form->field($model, 'sub_abbr')->textInput(['maxlength' => true]) ?>
        <?= $form->field($model, 'advisor')->textInput(['maxlength' => true]) ?>

        <?php
            $subTable = Yii::$app->db->schema->getTableSchema(app\models\ProgramSub::tableName());
            $hasActive = $subTable && $subTable->getColumn('is_active');
        ?>
        <?php if($hasActive){ ?>
            <?= $form->field($model, 'is_active')->checkbox() ?>
        <?php } ?>

        <br>
        <div class="form-group">
            <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
        </div>

        <?php ActiveForm::end(); ?>
    <?php }else{ ?>
        <?= $this->render('_form', [
            'model' => $model,
        ]) ?>
    <?php } ?>

</div>
            </div>
        </div>



    </section>


