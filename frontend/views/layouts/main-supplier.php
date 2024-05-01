<?php

/* @var $this \yii\web\View */
/* @var $content string */

use common\widgets\Alert;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap4\Breadcrumbs;
use backend\assets\CryptoAsset;
use backend\assets\AppAsset;

AppAsset::register($this);
CryptoAsset::register($this);


$dirAssests = Yii::$app->assetManager->getPublishedUrl('@backend/assets/crypto');


?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="zxx">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <!-- <title>BitCrypto</title> -->
    <title><?= Html::encode($this->title) ?></title>

    <link rel="icon" href="<?= $dirAssests?>/img/favicon-hs.png" type="image/png">

    <?= Html::csrfMetaTags() ?>
    <?php $this->head() ?>

</head>

<body class="crm_body_bg">
<?php $this->beginBody() ?>


    <?=$this->render('menu-supplier', [    
        'dirAssests' => $dirAssests,
    ]);
    ?>

    <section class="main_content dashboard_part large_header_bg">
    <!-- menu  -->
        <?=$this->render('upper_menu_supplier', [    
            'dirAssests' => $dirAssests,
        ]);
        ?>
    <!--/ menu  -->
    
<div class="main_content_iner overly_inner ">
    <div class="container-fluid p-0">
        <div class="row">
            <div class="col-12">
                <div class="page_title_box d-flex align-items-center justify-content-between">
                    <div class="page_title_left">
                        <h3 class="f_s_30 f_w_700 dark_text"><?= Html::encode($this->title) ?></h3>
                        
                    </div>
                    <ol class="breadcrumb page_bradcam mb-0">
                        <?php echo
                             Breadcrumbs::widget(
                                 [
                                     'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
                                 ]
                             ) 
                        ?>
                    </ol>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <?= Alert::widget() ?>
                <?=$content?>
            </div>
        </div>
    </div>
</div>

<!-- footer part -->
    <?=$this->render('footer', [    
        'dirAssests' => $dirAssests,
    ]);
    ?>
<!-- end of footer part -->    
</section>

<!-- ### CHAT_MESSAGE_BOX   ### -->

    <?=$this->render('chat_message', [    
        'dirAssests' => $dirAssests,
    ]);
    ?>

<!--/### CHAT_MESSAGE_BOX  ### -->

<div id="back-top" style="display: none;">
    <a title="Go to Top" href="#">
        <i class="ti-angle-up"></i>
    </a>
</div>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>