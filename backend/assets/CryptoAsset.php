<?php
/**
 * Created by PhpStorm.
 * User: ks
 * Date: 24/6/2561
 * Time: 1:54 น.
 */
namespace backend\assets;

use yii\web\AssetBundle;

class CryptoAsset extends AssetBundle
{
    public $sourcePath = '@backend/assets/crypto';
    public $css = [
		// 'css/bootstrap.min.css',
        'vendors/themefy_icon/themify-icons.css',
        'vendors/niceselect/css/nice-select.css',
        'vendors/owl_carousel/css/owl.carousel.css',
        'vendors/gijgo/gijgo.min.css',
        'vendors/font_awesome/css/all.min.css',
        'vendors/tagsinput/tagsinput.css',
        'vendors/vectormap-home/vectormap-2.0.2.css',
        'vendors/scroll/scrollable.css',
        'vendors/datatable/css/jquery.dataTables.min.css',
        'vendors/datatable/css/responsive.dataTables.min.css',
        'vendors/datatable/css/buttons.dataTables.min.css',
        'vendors/text_editor/summernote-bs4.css',
        'vendors/morris/morris.css',
        'vendors/material_icon/material-icons.css',
        'css/metisMenu.css',
        'css/style.css',
        'css/colors/default.css',
    ];

    public $js = [
        
        'js/main.js',
        //footer//
        // 'js/jquery-3.4.1.min.js',
        //popper js -->
        'js/popper.min.js',
        //bootstarp js -->
        'js/bootstrap.min.js',
        //sidebar menu//
        'js/metisMenu.js',
        //waypoints js -->
        'vendors/count_up/jquery.waypoints.min.js',
        //waypoints js -->
        'vendors/chartlist/Chart.min.js',
        //counterup js -->
        'vendors/count_up/jquery.counterup.min.js',

        //nice select -->
        'vendors/niceselect/js/jquery.nice-select.min.js',
        //owl carousel -->
        'vendors/owl_carousel/js/owl.carousel.min.js',

        //responsive table -->
        'vendors/datatable/js/jquery.dataTables.min.js',
        'vendors/datatable/js/dataTables.responsive.min.js',
        'vendors/datatable/js/dataTables.buttons.min.js',
        'vendors/datatable/js/buttons.flash.min.js',
        'vendors/datatable/js/jszip.min.js',
        'vendors/datatable/js/pdfmake.min.js',
        'vendors/datatable/js/vfs_fonts.js',
        'vendors/datatable/js/buttons.html5.min.js',
        'vendors/datatable/js/buttons.print.min.js',

        

        'js/chart.min.js',
        'vendors/chartjs/roundedBar.min.js',

        //progressbar js -->
        'vendors/progressbar/jquery.barfiller.js',
        //tag input -->
        'vendors/tagsinput/tagsinput.js',
        //text editor js -->
        'vendors/text_editor/summernote-bs4.js',
        'vendors/am_chart/amcharts.js',

        //scrollabe//
        'vendors/scroll/perfect-scrollbar.min.js',
        'vendors/scroll/scrollable-custom.js',

        //vector map//
        'vendors/vectormap-home/vectormap-2.0.2.min.js',
        'vendors/vectormap-home/vectormap-world-mill-en.js',

        //apex chrat//
        'vendors/apex_chart/apex-chart2.js',
        'vendors/apex_chart/apex_dashboard.js',

        //'vendors/echart/echarts.min.js', -->


        'vendors/chart_am/core.js',
        'vendors/chart_am/charts.js',
        'vendors/chart_am/animated.js',
        'vendors/chart_am/kelly.js',
        'vendors/chart_am/chart-custom.js',
        //custom js -->
        'js/dashboard_init.js',
        'js/custom.js',

    ];

    public $depends = [
        'yii\web\YiiAsset',
		'djabiev\yii\assets\AutosizeTextareaAsset',
        //'yii\jui\JuiAsset',
        'yii\bootstrap4\BootstrapAsset',
        //'backend\assets\FontAwesomeAsset'
    ];
}