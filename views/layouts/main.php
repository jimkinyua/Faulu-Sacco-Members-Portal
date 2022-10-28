<?php

/* @var $this \yii\web\View */
/* @var $content string */

use app\assets\AppAsset;
use app\widgets\Alert;
use yii\bootstrap4\Breadcrumbs;
use yii\bootstrap4\Html;
use yii\bootstrap4\Nav;
use yii\bootstrap4\NavBar;
use yii\helpers\Url;

AppAsset::register($this);
$webroot = Yii::getAlias(@$webroot);
$absoluteUrl = \yii\helpers\Url::home(true);
$identity = Yii::$app->user->identity;
// echo '<pre>';
// print_r($identity);
// exit;
?>

<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>" class="h-100">

<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=yes">
    <meta name="title" content="Faulu SACCO Members Portal">
    <meta name="author" content="Iansoft Technologies">
    <meta name="description" content="Faulu Members Portal for Self Service Portal">
    <meta name="keywords" content="SACCO, Faulu Members, Faulu Login, Faulu Kenya, Faulu Kenya" />
    <!-- <link rel="canonical" href="https://themes.getbootstrap.com/product/rocket/"> -->

    <!-- Favicon -->
    <link rel="apple-touch-icon" sizes="120x120" href="<?= $webroot ?> /html&css/assets/img/SaccoLogo.png">
    <link rel="icon" type="image/png" sizes="32x32" href=" <?= $webroot ?> /html&css/assets/img/SaccoLogo.png">
    <link rel="icon" type="image/png" sizes="16x16" href="<?= $webroot ?> /html&css/assets/img/SaccoLogo.png">
    <link rel="manifest" href="<?= $webroot ?> /html&css/assets/img/favicon/site.webmanifest">
    <link rel="mask-icon" href="<?= $webroot ?> /html&css/assets/img/favicon/safari-pinned-tab.svg" color="#ffffff">
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i%7CQuicksand:300,400,500,700" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="../../../app-assets/fonts/material-icons/material-icons.css">
    <meta name="msapplication-TileColor" content="#ffffff">
    <meta name="theme-color" content="#ffffff">

    <?php $this->registerCsrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>

<body class="vertical-layout vertical-menu 2-columns   fixed-navbar" data-open="click" data-menu="vertical-menu" data-col="2-columns">
    <!-- Global Modal Here -->
    <div class="modal fade bs-example-modal-lg bs-modal-lg" tabindex="-1" role="dialog" aria-hidden="true" id="modal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content" id="modalContent">

                <div class="modal-header" id="modalHeader">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span>
                    </button>
                    <h4 class="modal-title" id="myModalLabel" style="position: absolute"></h4>
                </div>

                <div class="modal-body">
                    <img class="loader-element ButtonPreloader" src="<?= $webroot ?>/html&css/assets/img/brand/preloader-spinner.gif" height="50" alt="Faulu logo">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                    <!--<button type="button" class="btn btn-primary">Save changes</button>-->
                </div>

            </div>
        </div>
    </div>


    <!-- BEGIN: Header-->
    <nav class="header-navbar navbar-expand-md navbar navbar-with-menu navbar-without-dd-arrow fixed-top navbar-semi-light bg-info navbar-shadow">
        <div class="navbar-wrapper">
            <div class="navbar-header">
                <ul class="nav navbar-nav flex-row">
                    <li class="nav-item mobile-menu d-md-none mr-auto"><a class="nav-link nav-menu-main menu-toggle hidden-xs" href="#"><i class="ft-menu font-large-5"></i></a></li>
                    <li class="nav-item"><a class="navbar-brand" href="/">
                            <img class="brand-logo" alt="modern admin logo" src="../../../app-assets/images/logo/logo.png" style="
                            width: 9.5em; 
                            position:fixed;
                            margin-top:.5em;
                            height: 4em;
                            ">
                            <!-- <h3 class="brand-text">Faulu SACCO</h3> -->
                        </a></li>
                    <li class="nav-item d-md-none"><a class="nav-link open-navbar-container" data-toggle="collapse" data-target="#navbar-mobile"><i class="la la-ellipsis-v"></i></a></li>
                </ul>
            </div>
            <div class="navbar-container content">
                <div class="collapse navbar-collapse" id="navbar-mobile">
                    <ul class="nav navbar-nav mr-auto float-left">
                        <li class="nav-item d-none d-md-block"><a class="nav-link nav-menu-main menu-toggle hidden-xs" href="#"><i class="ft-menu"></i></a></li>
                        <li class="nav-item d-none d-lg-block"><a class="nav-link nav-link-expand" href="#"><i class="ficon ft-maximize"></i></a></li>
                    </ul>
                    <ul class="nav navbar-nav float-right">
                        <li class="dropdown dropdown-user nav-item"><a class="dropdown-toggle nav-link dropdown-user-link" href="#" data-toggle="dropdown"><span class="mr-1 user-name text-bold-700"> <?= $identity->{'Search Name'}   ?></span><span class="avatar avatar-online"><img style="height:inherit;width:150px;" src=" data:image/png;base64, " alt="no image found"><i></i></span></a>
                            <div class="dropdown-menu dropdown-menu-right">
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="<?= Url::to(['/member-change-request/edit-profile']) ?>"><i class="ft-edit"></i> Edit My Details </a>
                                <a class="dropdown-item" href="<?= Url::to(['/site/change-password']) ?>"><i class="ft-edit"></i> Change Password </a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="<?= Url::to(['/site/logout']) ?>"><i class="ft-power"></i> Logout</a>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>
    <!-- END: Header-->


    <!-- BEGIN: Main Menu-->

    <div class="main-menu menu-fixed menu-dark menu-accordion text-white     menu-shadow " data-scroll-to-active="true">
        <div class="main-menu-content ">
            <ul class="navigation navigation-main" id="main-menu-navigation" data-menu="menu-navigation">
                <li class=" nav-item"><a href="/"><i class="la la-home"></i><span class="menu-title" data-i18n="Dashboard">Dashboard</span><span class="badge badge badge-info badge-pill float-right mr-2"></span></a>
                </li>


                <li class="nav-item"><a href="#"><i class="la la-clipboard"></i><span class="menu-title" data-i18n="Menu levels">Reports </span></a>
                    <ul class="menu-content">
                        <li><a class="menu-item" href="<?= Url::to('site/statement/', $schema = true) ?>"><i></i><span data-i18n="Second level">Full Statement</span></a>
                        </li>
                        <li><a class="menu-item" href="<?= Url::to('site/guaranteed-loans-statements/', $schema = true) ?>"><i></i><span data-i18n="Second level">Guaranteed Loans Statement</span></a>
                        </li>
                        <li><a class="menu-item" href="<?= Url::to('/site/guarantor-statement/', $schema = true) ?>"><i></i><span data-i18n="Second level">Guarantor Statement</span></a>
                        </li>
                    </ul>
                </li>

                <li class=" navigation-header"><span>General </span><i class="la la-ellipsis-h" data-toggle="tooltip" data-placement="right" data-original-title="General"></i>
                </li>


                <li class="nav-item"><a href="#"><i class="la la-money"></i><span class="menu-title" data-i18n="Menu levels">Loan Management</span></a>
                    <ul class="menu-content">
                        <li><a class="menu-item" href="<?= Url::to('loan', $schema = true) ?>"><i></i><span data-i18n="Second level">Loan Applications</span></a>
                        </li>

                        <li><a class="menu-item" href="<?= Url::to(['loan/pending-approval'], $schema = true) ?>"><i></i><span data-i18n="Second level"> Submitted Loans </span></a>
                        </li>
                        <li><a class="menu-item" href="<?= Url::to(['loan/approved'], $schema = true) ?>"><i></i><span data-i18n="Second level">Disbursed Loans </span></a>
                        </li>
                    </ul>
                </li>



                <li><a class="menu-item" href="#"><i class="la la-group"></i><span data-i18n="Second level child">Guarantor Management</span></a>
                    <ul class="menu-content">
                        <li><a class="menu-item" href="<?= Url::to('guarantors/guarantorship-requests/', $schema = true) ?>"><i class="la la-key"></i>
                                <span data-i18n="Third level">Guarantorship Requests</span></a>
                        </li>
                    </ul>
                </li>

             

                <!-- <li><a class="menu-item" href="#"><i class="la la-book"></i><span data-i18n="Second level child">Cheque Book Mgt</span></a>
                <ul class="menu-content">
                    <li><a class="menu-item" href="<?= Url::to('cheque-book-request/', $schema = true) ?>"><i class="la la-pen"></i>
                            <span data-i18n="Third level"> Applications</span></a>
                    </li>
                </ul>
            </li> -->

                <!-- <li><a class="menu-item" href="#"><i class="la la-edit"></i><span data-i18n="Second level child"> Change Request </span></a>
                    <ul class="menu-content">
                        <li><a class="menu-item" href="<?= Url::to('member-change-request/', $schema = true) ?>"><i class="la la-pen"></i>
                                <span data-i18n="Third level"> Applications</span></a>
                        </li>
                    </ul>
                </li> -->

            

                <!-- <li><a class="menu-item" href="#"><i class="la la-book"></i><span data-i18n="Second level child">Membership Management</span></a>
                    <ul class="menu-content">
                        <li><a class="menu-item" href="<?= Url::to('member-withdrawal/create', $schema = true) ?>"><i class="la la-door-open"></i>
                                <span data-i18n="Third level"> Exit Sacco</span></a>
                        </li>

                        <li><a class="menu-item" href="<?= Url::to('inter-account/create', $schema = true) ?>"><i class="la la-share"></i>
                            <span data-i18n="Third level"> Transfer Shares </span></a>
                    </li>

                    </ul>
                </li> -->

                <li class=" navigation-header"><span>Announcements</span><i class="la la-ellipsis-h" data-toggle="tooltip" data-placement="right" data-original-title="Support"></i>
                </li>
                <li class=" nav-item"><a href="<?= Url::to('downloads/', $schema = true) ?>" target="_blank"><i class="la la-download"></i><span class="menu-title" data-i18n="Raise Support">Downloads</span></a>
                </li>






            </ul>
            </li>






            </ul>
        </div>
    </div>

    <!-- END: Main Menu-->

    <!-- END: Main Menu-->

    <!-- BEGIN: Content-->
    <div class="app-content content">
        <div class="content-overlay"></div>
        <div class="content-wrapper">
            <div class="content-header row">
            </div>
            <div class="content-body">
                <div class="row">
                    <div class="col-md-12">
                        <?php

                        if (Yii::$app->session->hasFlash('success')) {
                            print ' <div class="alert alert-success alert-dismissable">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                            <h5><i class="icon fas fa-check"></i> Success!</h5>';
                            echo Yii::$app->session->getFlash('success');
                            print '</div>';
                        } else if (Yii::$app->session->hasFlash('error')) {
                            print ' <div class="alert alert-danger alert-dismissable">
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                            <h5><i class="icon fas fa-check"></i> Error!</h5>
                                    ';
                            echo Yii::$app->session->getFlash('error');
                            print '</div>';
                        }
                        ?>
                    </div>
                </div>

                <?= $content ?>
            </div>
        </div>
    </div>







    <?php $this->endBody() ?>

</body>
<div class="sidenav-overlay"></div>
<div class="drag-target"></div>

<!-- BEGIN: Footer-->
<!-- <footer class="footer footer-transparent footer-light navbar-shadow">
        <p class="clearfix blue-grey lighten-2 text-sm-center mb-0 px-2 container center-layout"><span class="float-md-left d-block d-md-inline-block">Copyright &copy; 2019 <a class="text-bold-800 grey darken-2" href="https://Faulusacco.com" target="_blank">Faulu SACCO</a></span><span class="float-md-right d-none d-lg-block">Powered By Iansoft Technologies<i class="ft-heart pink"></i><span id="scroll-top"></span></span></p>
    </footer> -->
<!-- END: Footer-->
<script>
    $(function() {
        console.log('hapa')
        var absolute = $('input[name=absolute]').val();
        //Add an experience
        $('.createWithdraw').on('click', function(e) {
            e.preventDefault();
            var url = $(this).attr('href');
            console.log('clicking...');
            $('.modal').modal('show')
                .find('.modal-body')
                .load(url);

        });

        /*Handle dismissal eveent of modal */
        $('.modal').on('hidden.bs.modal', function() {
            var reld = location.reload(true);
            setTimeout(reld, 1000);
        });
    });
</script>

</html>
<?php $this->endPage() ?>