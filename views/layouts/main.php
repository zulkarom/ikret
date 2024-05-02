<?php

use app\assets\NiceAsset;
use app\models\Alert;
use yii\helpers\Html;

NiceAsset::register($this);
$dirAssests = Yii::$app->assetManager->getPublishedUrl('@app/assets/nice');
$web = Yii::getAlias('@web');
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title><?= Html::encode($this->title) ?></title>
  <meta content="" name="description">
  <meta content="" name="keywords">

  <!-- Favicons -->
  <link href="assets/img/favicon.png" rel="icon">
  <link href="assets/img/apple-touch-icon.png" rel="apple-touch-icon">

  <!-- Google Fonts -->
  <link href="https://fonts.gstatic.com" rel="preconnect">
  <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Nunito:300,300i,400,400i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet">

  <?= Html::csrfMetaTags() ?>
    <?php $this->head() ?>
</head>

<body>
<?php $this->beginBody() ?>
<?=$this->render('header', [    
        'dirAssests' => $dirAssests,
        'web' => $web
    ]);
    ?>

<?=$this->render('menu', [    
        'dirAssests' => $dirAssests,
        'web' => $web
    ]);
    ?>

  <main id="main" class="main">
  <?= Alert::widget() ?>
    <?=$content?>

  

  </main><!-- End #main -->

  <!-- ======= Footer ======= -->
  <footer id="footer" class="footer">
    <div class="copyright">
      &copy; Copyright <strong><span>iCreate 2024</span></strong>. All Rights Reserved
    </div>
    <div class="credits">
      <!-- All the links in the footer should remain intact. -->
      <!-- You can delete the links only if you purchased the pro version. -->
      <!-- Licensing information: https://bootstrapmade.com/license/ -->
      <!-- Purchase the pro version with working PHP/AJAX contact form: https://bootstrapmade.com/nice-admin-bootstrap-admin-html-template/ -->
      Template by <a href="https://bootstrapmade.com/" target="_blank">BootstrapMade</a> 
      System by <a href="https://skyhint.com/" target="_blank">Skyhint Design</a>
    </div>
  </footer><!-- End Footer -->

  <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

  <?php $this->endBody() ?>

</body>

</html>
<?php $this->endPage() ?>