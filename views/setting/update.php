<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\Setting $model */

$this->title = 'Settings';

?>

<div class="setting-update">

    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Home Page Settings</h5>

                    <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>

                    <?= $form->field($model, 'banner_file')->fileInput() ?>
                    <?php if (!empty($model->banner_image)) { ?>
                        <div class="mb-3">
                            <?= Html::img(Yii::getAlias('@web') . '/' . ltrim($model->banner_image, '/'), ['style' => 'max-width:100%; max-height:180px; object-fit:cover;']) ?>
                        </div>
                    <?php } ?>

                    <?= $form->field($model, 'show_icreate_list_event')->checkbox() ?>

                    <?= $form->field($model, 'programme_book_url')->textInput(['type' => 'url', 'placeholder' => 'https://...']) ?>

                    <?= $form->field($model, 'program_description')->textarea(['rows' => 6]) ?>

                    <?= $form->field($model, 'programme_book_qr_file')->fileInput() ?>
                    <?php if (!empty($model->programme_book_qr)) { ?>
                        <div class="mb-3">
                            <?= Html::img(Yii::getAlias('@web') . '/' . ltrim($model->programme_book_qr, '/'), ['style' => 'max-width:140px; width:100%;']) ?>
                        </div>
                    <?php } ?>

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
