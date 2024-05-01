<?php

namespace website\assets;

use yii\web\AssetBundle;

class NiceAsset extends AssetBundle
{
    public $sourcePath = '@frontend/assets/nice';
    public $css = [
        'vendor/bootstrap/css/bootstrap.min.css',
        'vendor/bootstrap-icons/bootstrap-icons.css',
        'vendor/boxicons/css/boxicons.min.css',
        'vendor/quill/quill.snow.css',
        'vendor/quill/quill.bubble.css',
        'vendor/remixicon/remixicon.css',
        'vendor/simple-datatables/style.css',
        'css/style.css',

    ];

    public $js = [
		// 'js/jquery.js',
        'vendor/apexcharts/apexcharts.min.js',
        'vendor/bootstrap/js/bootstrap.bundle.min.js',
        'vendor/chart.js/chart.umd.js',
        'vendor/echarts/echarts.min.js',
        'vendor/quill/quill.js',
        'vendor/simple-datatables/simple-datatables.js',
        'vendor/tinymce/tinymce.min.js',
        'vendor/php-email-form/validate.js',
        'js/main.js',
    ];


    public $depends = [
        'yii\web\YiiAsset',
       // 'yii\bootstrap4\BootstrapAsset',
    ];
}