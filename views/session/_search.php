<?php

use app\models\Program;
use app\models\ProgramSub;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\SessionSearch $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="session-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => ['class' => 'row g-2 align-items-end mb-0'],
    ]); ?>

    <?php
    $programList = Program::listPrograms();
    $subQuery = ProgramSub::find()->alias('ps')->joinWith(['program p']);
    $subTable = Yii::$app->db->schema->getTableSchema(ProgramSub::tableName());
    if($subTable && $subTable->getColumn('is_active')){
        $subQuery->andWhere(['ps.is_active' => 1]);
    }
    if($model->program_id){
        $subQuery->andWhere(['ps.program_id' => (int)$model->program_id]);
    }
    $programSubList = ArrayHelper::map(
        $subQuery->orderBy(['p.id' => SORT_ASC, 'ps.id' => SORT_ASC])->all(),
        'id',
        function(ProgramSub $sub){
            $programName = $sub->program ? ($sub->program->program_abbr ?: $sub->program->program_name) : '-';
            return $programName . ' - ' . $sub->subProgramText;
        }
    );
    ?>

    <div class="col-md-3">
        <?= $form->field($model, 'session_name')->textInput(['placeholder' => 'Session name'])->label(false) ?>
    </div>

    <div class="col-md-3">
        <?= $form->field($model, 'program_id')->dropDownList($programList, [
            'prompt' => '- All Programs -',
            'onchange' => '$("select#sessionsearch-program_sub").val(""); this.form.submit();',
        ])->label(false) ?>
    </div>

    <div class="col-md-3">
        <?= $form->field($model, 'program_sub')->dropDownList($programSubList, ['prompt' => '- All Sub Programs -'])->label(false) ?>
    </div>

    <div class="col-md-3 d-flex gap-2">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Reset', ['index'], ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
