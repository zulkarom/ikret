<?php
use common\widgets\Alert;
use yii\helpers\Html;
use backend\assets\LoginAsset;
use backend\assets\AppAsset;
use kartik\widgets\ActiveForm;

LoginAsset::register($this);

?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title><?= Html::encode($this->title) ?></title>

    <?= Html::csrfMetaTags() ?>
    <?php $this->head() ?>
</head>
<body>

<?php $this->beginBody() ?>
    

        <?= Alert::widget() ?>
        <?= $content ?>
    

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
