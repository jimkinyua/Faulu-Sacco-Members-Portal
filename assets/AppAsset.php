<?php

/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\assets;

use yii\web\AssetBundle;

/**
 * Main application asset bundle.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class AppAsset extends AssetBundle
{
  public $basePath = '@webroot';
  public $baseUrl = '@web';
  public $css = [
    // <!-- BEGIN: Vendor CSS-->
    'app-assets/vendors/css/vendors.min.css',
    'app-assets/vendors/css/ui/jquery-ui.min.css',
    'app-assets/css-rtl/plugins/ui/jqueryui.css',
    'app-assets/vendors/css/material-vendors.min.css',
    'app-assets/vendors/css/weather-icons/climacons.min.css',
    'app-assets/fonts/meteocons/style.css',
    'app-assets/vendors/css/charts/morris.css',
    'app-assets/vendors/css/charts/chartist.css',
    'app-assets/vendors/css/charts/chartist-plugin-tooltip.css',

    'app-assets/vendors/css/tables/datatable/datatables.min.css',
    'app-assets/vendors/css/tables/extensions/responsive.dataTables.min.css',
    'app-assets/vendors/css/tables/extensions/colReorder.dataTables.min.css',
    'app-assets/vendors/css/tables/extensions/buttons.dataTables.min.css',
    'app-assets/vendors/css/tables/datatable/buttons.bootstrap4.min.css',
    'app-assets/vendors/css/tables/extensions/fixedHeader.dataTables.min.css',

    // <!-- END: Vendor CSS-->

    // <!-- BEGIN: Theme CSS-->
    'app-assets/css/material.css',
    'app-assets/css/components.css',
    'app-assets/css/bootstrap-extended.css',
    'app-assets/css/material-extended.css',
    'app-assets/css/material-colors.css',
    'app-assets/css-rtl/colors.css',

    // <!-- END: Theme CSS-->

    // <!-- BEGIN: Page CSS-->
    'app-assets/css/core/menu/menu-types/vertical-menu.css',
    'app-assets/css/core/colors/palette-gradient.css',
    'app-assets/vendors/css/charts/jquery-jvectormap-2.0.3.css',
    'app-assets/vendors/css/charts/morris.css',
    'app-assets/fonts/simple-line-icons/style.css',
    'app-assets/css/core/colors/palette-gradient.css',
    // <!-- END: Page CSS-->

    'app-assets/css/plugins/images/cropper/cropper.css',



  ];
  public $js = [
    // <!-- BEGIN: Vendor JS-->
    'app-assets/vendors/js/vendors.min.js',
    // <!-- BEGIN Vendor JS-->

    // <!-- BEGIN: Page Vendor JS-->
    'app-assets/vendors/js/charts/chart.min.js',
    'app-assets/vendors/js/charts/raphael-min.js',
    'app-assets/vendors/js/charts/morris.min.js',
    'app-assets/vendors/js/charts/jvector/jquery-jvectormap-2.0.3.min.js',
    'app-assets/vendors/js/charts/jvector/jquery-jvectormap-world-mill.js',
    'app-assets/data/jvector/visitor-data.js',
    'app-assets/js/core/libraries/jquery_ui/jquery-ui.min.js',
    'app-assets/js/scripts/ui/jquery-ui/navigations.js',



    //Datatables 
    'app-assets/vendors/js/tables/datatable/datatables.min.js',
    'app-assets/vendors/js/tables/datatable/dataTables.responsive.min.js',
    'app-assets/vendors/js/tables/buttons.colVis.min.js',
    'app-assets/vendors/js/tables/datatable/dataTables.colReorder.min.js',
    'app-assets/vendors/js/tables/datatable/dataTables.buttons.min.js',
    'app-assets/vendors/js/tables/datatable/buttons.bootstrap4.min.js',
    'app-assets/vendors/js/tables/datatable/dataTables.fixedHeader.min.js',

    // <!-- END: Page Vendor JS-->

    // <!-- BEGIN: Theme JS-->
    'app-assets/js/core/app-menu.js',
    'app-assets/js/core/app.js',
    // <!-- END: Theme JS-->

    // <!-- BEGIN: Page JS-->
    'app-assets/js/scripts/pages/dashboard-sales.js',
    'app-assets/js/scripts/tables/datatables-extensions/datatable-responsive.js',

    // <!-- END: Page JS-->

    'app-assets/js/scripts/extensions/image-cropper.js',
    '//cdn.jsdelivr.net/npm/sweetalert2@11',


  ];
  public $depends = [
    'yii\web\JqueryAsset',
    // 'yii\web\YiiAsset',
    // 'yii\bootstrap4\BootstrapAsset',
  ];
}
