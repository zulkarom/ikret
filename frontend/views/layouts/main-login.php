<?php
use yii\helpers\Url;
\backend\assets\WebAsset::register($this);
$web = Yii::$app->assetManager->getPublishedUrl('@backend/assets/web');
?>



<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="en-gb" class="no-js">
  <head>

        <?php $this->registerCsrfMetaTags() ?>
 <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <meta name="description" content="" />
        <meta name="author" content="" />
        <title>HubSoE | Hub for Social Entrepreneur</title>
        <link rel="icon" href="<?=$web?>/images/favhs.png" type="image/png">
        <!-- Bootstrap icons-->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css" rel="stylesheet" />
        <!-- Google fonts-->
        <link rel="preconnect" href="https://fonts.gstatic.com" />
        <link href="https://fonts.googleapis.com/css2?family=Newsreader:ital,wght@0,600;1,600&amp;display=swap" rel="stylesheet" />
        <link href="https://fonts.googleapis.com/css2?family=Mulish:ital,wght@0,300;0,500;0,600;0,700;1,300;1,500;1,600;1,700&amp;display=swap" rel="stylesheet" />
        <link href="https://fonts.googleapis.com/css2?family=Kanit:ital,wght@0,400;1,400&amp;display=swap" rel="stylesheet" />
        <!-- Core theme CSS (includes Bootstrap)-->
        <link href="<?=$web?>/css/styles.css" rel="stylesheet" />
		<style>
		
		.device[data-device=iPhoneX][data-orientation=portrait][data-color=black]::after {
		  content: "";
		  background-image: url("images/img/portrait_black.png");
		}
		
		</style>
   </head>

   <body  data-spy="scroll" data-target="#main-menu">
    <?php $this->beginBody() ?>

   
     <body id="page-top">
        <!-- Navigation-->
        <nav class="navbar navbar-expand-lg navbar-light fixed-top shadow-sm" id="mainNav">
            <div class="container px-5">
                <a class="navbar-brand fw-bold" href="<?php echo Url::to(['../../'])?>">HubSoE</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation">
                    Menu
                    <i class="bi-list"></i>
                </button>
                <div class="collapse navbar-collapse" id="navbarResponsive">
                    <ul class="navbar-nav ms-auto me-4 my-3 my-lg-0">
                    
                    <li class="nav-item"><a class="nav-link me-lg-3" href="<?=Url::to(['../../'])?>">Utama</a></li>
                    
                        <li class="nav-item"><a class="nav-link me-lg-3" href="<?=Url::to(['/'])?>">Log Masuk</a></li>
                    </ul>
                    <a href="<?=Url::to(['/user-register/register'])?>" class="btn btn-primary rounded-pill px-3 mb-2 mb-lg-0">
                        <span class="d-flex align-items-center">
                     
                            <span class="small">Pendaftaran</span>
                        </span>
                    </a>
                </div>
            </div>
        </nav>
        
        <!-- Mashead header-->
        
       <?=$content?>


        <!-- Footer-->
        <footer class="bg-black text-center py-5">
            <div class="container px-5">
                <div class="text-white-50 small">
                    <div class="mb-2">&copy; HubSoE 2022. All Rights Reserved.</div>
                    <a href="#!">Privacy</a>
                    <span class="mx-1">&middot;</span>
                    <a href="#!">Terms</a>
                    <span class="mx-1">&middot;</span>
                    <a href="#!">FAQ</a>
                </div>
            </div>
        </footer>
        <!-- Feedback Modal-->
        
        <!-- Bootstrap core JS-->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
        <!-- Core theme JS-->
        <script src="<?=$web?>/js/scripts.js"></script>
        <script src="https://cdn.startbootstrap.com/sb-forms-latest.js"></script>




   </body>
</html>

<?php $this->endPage() ?>