<?php

use yii\helpers\Html;

/** @var string $url */
/** @var array $data */

?>

<?= Html::beginForm($url, 'post', ['id' => 'post-redirect-form']) ?>
<?= Html::hiddenInput(Yii::$app->request->csrfParam, Yii::$app->request->getCsrfToken()) ?>
<?php foreach ($data as $name => $value): ?>
    <?= Html::hiddenInput($name, $value) ?>
<?php endforeach; ?>
<?= Html::endForm() ?>

<script>
(function () {
    var form = document.getElementById('post-redirect-form');
    if (form) {
        form.submit();
    }
})();
</script>
