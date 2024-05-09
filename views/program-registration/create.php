<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\ProgramRegistration $model */

$this->title = 'Create Program Registration';
$this->params['breadcrumbs'][] = ['label' => 'Program Registrations', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="program-registration-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
