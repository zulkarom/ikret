<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\Setting $model */

$this->title = 'Settings Media';

$this->registerCss(<<<CSS
.setting-media .form-group {
    margin-bottom: 18px;
}

.setting-media .current-image {
    margin-bottom: 12px;
}

.setting-media .current-banner {
    max-width: 100%;
    max-height: 180px;
    object-fit: cover;
}

.setting-media .current-qr {
    max-width: 140px;
    width: 100%;
}
CSS);

?>

<div class="setting-media">

    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Home Page Media</h5>

                    <?php $form = ActiveForm::begin([
                        'action' => Url::to(['/storage/index']),
                        'options' => ['enctype' => 'multipart/form-data'],
                    ]); ?>

                    <?= Html::hiddenInput('storage_action', 'setting-media-update') ?>
                    <?= Html::hiddenInput('id', $model->id) ?>

                    <?php if (!empty($model->banner_image)) { ?>
                        <div class="current-image">
                            <?= Html::img(Yii::getAlias('@web') . '/' . ltrim($model->banner_image, '/'), ['class' => 'current-banner']) ?>
                        </div>
                    <?php } ?>
                    <?= $form->field($model, 'banner_file')->fileInput() ?>

                    <?php if (!empty($model->programme_book_qr)) { ?>
                        <div class="current-image">
                            <?= Html::img(Yii::getAlias('@web') . '/' . ltrim($model->programme_book_qr, '/'), ['class' => 'current-qr']) ?>
                        </div>
                    <?php } ?>
                    <?= $form->field($model, 'programme_book_qr_file')->fileInput() ?>

                    <div class="form-group">
                        <?= Html::submitButton('Save Media', ['class' => 'btn btn-success']) ?>
                    </div>

                    <?php ActiveForm::end(); ?>
                </div>
            </div>
        </div>
    </div>

</div>
