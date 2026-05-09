<?php

use yii\bootstrap5\ActiveForm;
use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\JuryManualCreateForm $model */

$this->title = 'Create Jury Manually';
$this->params['breadcrumbs'][] = ['label' => 'Jury Profiles', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="pagetitle">
    <h1><?= Html::encode($this->title) ?></h1>
</div>

</div><!-- End Page Title -->

<section class="section">
    <div class="card">
        <div class="card-body pt-4">
            <?php $form = ActiveForm::begin(); ?>

            <div class="row">
                <div class="col-md-6">
                    <?= $form->field($model, 'fullname')->textInput(['maxlength' => true]) ?>
                </div>
                <div class="col-md-6">
                    <?= $form->field($model, 'email')->textInput(['maxlength' => true]) ?>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4">
                    <?= $form->field($model, 'category')->dropDownList([
                        'Academic' => 'Academic',
                        'Industry' => 'Industry',
                    ], ['prompt' => 'Choose Category']) ?>
                </div>
                <div class="col-md-4">
                    <?= $form->field($model, 'phone')->textInput(['maxlength' => true]) ?>
                </div>
                <div class="col-md-4">
                    <?= $form->field($model, 'password')->passwordInput(['maxlength' => true])->hint('Leave blank to use email as the initial password.') ?>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <?= $form->field($model, 'institution')->textInput(['maxlength' => true]) ?>
                </div>
                <div class="col-md-6">
                    <?= $form->field($model, 'designation')->textInput(['maxlength' => true]) ?>
                </div>
            </div>

            <?= $form->field($model, 'address')->textarea(['rows' => 3]) ?>

            <div class="form-group">
                <?= Html::submitButton('Create Jury', ['class' => 'btn btn-success']) ?>
                <?= Html::a('Cancel', ['index'], ['class' => 'btn btn-outline-secondary']) ?>
            </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>
</section>
