<?php
/**
 * Created by PhpStorm.
 * User: ks
 * Date: 24/6/2561
 * Time: 1:54 น.
 */
namespace backend\assets;

use yii\web\AssetBundle;

class AdminPress extends AssetBundle
{
    public $sourcePath = '@backend/assets/adminpress';
    public $css = [
		// 'plugins/bootstrap/css/bootstrap.min.css',
        'css/style.css',
        'css/colors/green.css',
		'plugins/toast-master/css/jquery.toast.css',
        'plugins/calendar/dist/fullcalendar.css',
        // 'css/icons/themify-icons/themify-icons.css',
    ];

    public $js = [
        // 'plugins/jquery/jquery.min.js',
        'plugins/bootstrap/js/popper.min.js',
        'plugins/bootstrap/js/bootstrap.min.js',
        'plugins/sticky-kit-master/dist/sticky-kit.min.js',
        'js/jquery.slimscroll.js',
        'js/waves.js',
        'js/sidebarmenu.js',
        'js/custom.min.js',
        'js/morris-data.js',
        'plugins/raphael/raphael-min.js',
       
		'plugins/toast-master/js/jquery.toast.js',
        'plugins/calendar/jquery-ui.min.js',
        'plugins/calendar/dist/fullcalendar.min.js',
        'plugins/calendar/dist/cal-init.js',
    ];

   //  public $publishOptions = [
   //      "only" => [
   //          "dist/js/*",
   //          "dist/css/*",
			// "dist/img/*",
   //          //"plugins/bootstrap/js/*",
			// "plugins/fontawesome-free/css/*",
			// "plugins/fontawesome-free/webfonts/*",
			// "plugins/toastr/*",
   //      ],

   //  ];

    public $depends = [
        'yii\web\YiiAsset',
		'djabiev\yii\assets\AutosizeTextareaAsset',
        //'yii\jui\JuiAsset',
        'yii\bootstrap4\BootstrapAsset',
        //'backend\assets\FontAwesomeAsset'
    ];
}