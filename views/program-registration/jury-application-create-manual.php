<?php

use yii\bootstrap5\ActiveForm;
use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\JuryApplicationManualCreateForm $model */
/** @var array $programScopeOptions */
/** @var array $sessionOptions */
/** @var array $sessionScopeMap */

$this->title = 'Add Manual Jury Application';
$this->params['breadcrumbs'][] = ['label' => 'Jury Applications', 'url' => ['admin-jury-applications-all']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="pagetitle">
    <h1><?= Html::encode($this->title) ?></h1>
</div>

</div><!-- End Page Title -->

<section class="section">
    <div class="card">
        <div class="card-body pt-4">
            <div class="alert alert-info">
                This form creates or updates the user, activates the jury role, saves the jury profile, creates an approved jury application, and increases the matching jury limit when needed.
            </div>

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

            <div class="row">
                <div class="col-md-6">
                    <?= $form->field($model, 'program_scope')->dropDownList($programScopeOptions, [
                        'id' => 'manual-program-scope',
                        'prompt' => 'Choose Program / Category',
                    ]) ?>
                </div>
                <div class="col-md-6">
                    <?= $form->field($model, 'judging_session_id')->dropDownList($sessionOptions, [
                        'id' => 'manual-judging-session',
                        'prompt' => 'No Session',
                    ])->hint('Optional. If selected, the session must belong to the chosen program/category.') ?>
                </div>
            </div>

            <div class="form-group">
                <?= Html::submitButton('Create Application', ['class' => 'btn btn-success']) ?>
                <?= Html::a('Cancel', ['admin-jury-applications-all'], ['class' => 'btn btn-outline-secondary']) ?>
            </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>
</section>

<?php
$sessionScopeJson = json_encode($sessionScopeMap);
$this->registerJs(<<<JS
var manualSessionScopeMap = $sessionScopeJson || {};

function filterManualJudgingSessions(){
    var scope = $("#manual-program-scope").val();
    var sessionSelect = $("#manual-judging-session");
    var current = sessionSelect.val();

    sessionSelect.find("option").each(function(){
        var option = $(this);
        var value = option.attr("value");
        if(!value){
            option.show();
            return;
        }

        var scopes = manualSessionScopeMap[value] || [];
        var show = scope && scopes.indexOf(scope) !== -1;
        option.toggle(show);
        if(!show && current === value){
            sessionSelect.val("");
        }
    });
}

$("#manual-program-scope").on("change", filterManualJudgingSessions);
filterManualJudgingSessions();
JS);
?>
