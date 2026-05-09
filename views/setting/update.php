<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\Setting $model */

$this->title = 'Settings';

$this->registerCss(<<<CSS
.setting-update .form-group {
    margin-bottom: 18px;
}

.setting-update .form-group .help-block {
    margin-top: 6px;
}

.setting-update .form-group .checkbox {
    margin-top: 4px;
    margin-bottom: 0;
}
CSS);

?>

<div class="setting-update">

    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Home Page Settings</h5>

                    <?php $form = ActiveForm::begin([
                        'action' => Url::to(['/storage/index']),
                    ]); ?>

                    <?= Html::hiddenInput('storage_action', 'setting-update') ?>
                    <?= Html::hiddenInput('id', $model->id) ?>

                    <?= $form->field($model, 'show_icreate_list_event')->checkbox() ?>

                    <?= $form->field($model, 'programme_book_url')->textInput(['type' => 'url', 'placeholder' => 'https://...']) ?>

                    <?= $form->field($model, 'program_description')->textarea(['rows' => 6]) ?>

                    <hr />

                    <h5 class="card-title">System Dates</h5>

                    <?= $form->field($model, 'date_start')->textInput(['type' => 'date']) ?>
                    <?= $form->field($model, 'date_end')->textInput(['type' => 'date']) ?>
                    <?= $form->field($model, 'allow_cert_from')->textInput(['type' => 'date'])
                        ->hint('Participant certificates, awards, jury certificates, mentor certificates, committee certificates, and certificate menu links will be released from this date.') ?>
                    <?= $form->field($model, 'allow_edit_reg_until')->textInput(['type' => 'date']) ?>

                    <div class="form-group">
                        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
                    </div>

                    <?php ActiveForm::end(); ?>
                </div>
            </div>
        </div>
    </div>

</div>
