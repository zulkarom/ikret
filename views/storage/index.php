<?php

use yii\helpers\Html;

/** @var array $post */

$this->title = 'Storage';

?>

<div class="storage-index">
    <h1><?= Html::encode($this->title) ?></h1>

    <?php foreach (Yii::$app->session->getAllFlashes() as $type => $message): ?>
        <div class="alert alert-<?= Html::encode($type) ?>">
            <?= Html::encode($message) ?>
        </div>
    <?php endforeach; ?>

    <div class="alert alert-info">
        Received POST:
        <pre><?= Html::encode(print_r($post, true)) ?></pre>
    </div>
</div>
