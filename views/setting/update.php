<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\Setting $model */

$this->title = 'Settings';

?>

<div class="setting-update">

    <?php $form = ActiveForm::begin(); ?>

    <?php foreach ($model->attributes as $attribute => $value): ?>
        <?php if ($attribute === 'id') { continue; } ?>
        <?= $form->field($model, $attribute)->textInput() ?>
    <?php endforeach; ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
