<?php
namespace app\assets;
use yii\web\AssetBundle;

class ErrorAsset extends AssetBundle{

    public $basePath = '@webroot';
    public $baseUrl = '@web';

    public $css = [
        'html&css/vendor/@fortawesome/fontawesome-free/css/all.min.css',
        'html&css/vendor/prismjs/themes/prism.css',
        'html&css/vendor/jqvmap/dist/jqvmap.min.css',
        'html&css/css/rocket.css',
        'css/helpblock.css',

    ];

    public $js = [
        //  Core 
        // 'html&css/vendor/jquery/dist/jquery.min.js',
        'html&css/vendor/popper.js/dist/umd/popper.min.js',
        'html&css/vendor/bootstrap/dist/js/bootstrap.min.js',
        'html&css/vendor/headroom.js/dist/headroom.min.js',
        
        //  Vendor JS 
        'html&css/vendor/countup.js/dist/countUp.min.js',
        'html&css/vendor/jquery-countdown/dist/jquery.countdown.min.js',
        'html&css/vendor/smooth-scroll/dist/smooth-scroll.polyfills.min.js',
        'html&css/vendor/prismjs/prism.js',
        
        //  Chartist 
        'html&css/vendor/chartist/dist/chartist.min.js',
        'html&css/vendor/chartist-plugin-tooltips/dist/chartist-plugin-tooltip.min.js',
        
        //  Vector Maps 
        'html&css/vendor/jqvmap/dist/jquery.vmap.min.js',
        'html&css/vendor/jqvmap/dist/maps/jquery.vmap.world.js',
        '//cdn.datatables.net/plug-ins/1.12.0/api/sum().js',

        //  Rocket JS 
        'html&css/assets/js/rocket.js',
        'CustomJS\preloader.js',


    ];

    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap4\BootstrapAsset',
    ];
    
}
