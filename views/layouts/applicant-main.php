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
// echo '<pre>';
// print_r(Yii::$app->applicant->identity->getMemberData());
// exit;
?>

<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>" class="h-100">

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=yes">
    <meta name="title" content="Faulu SACCO Members Portal">
    <meta name="author" content="Iansoft Technologies">
    <meta name="description" content="Faulu Members Portal for Self Service Portal">
    <meta name="keywords" content="SACCO, Faulu Members, Faulu Login, Faulu Kenya, Faulu Kenya" />

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

<body>
    <?php $this->beginBody() ?>

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
                    <img class="loader-element" src="<?= $webroot ?>/html&css/assets/img/brand/preloader-spinner.gif" height="50" alt="Faulu logo">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                    <!--<button type="button" class="btn btn-primary">Save changes</button>-->
                </div>

            </div>
        </div>
    </div>



    <nav class="navbar navbar-dark navbar-theme-primary col-12 d-md-none">
        <a class="navbar-brand mr-lg-5" href="../../index.html">
            <img class="navbar-brand-dark" src="<?= $webroot ?>/html&css/assets/img/.svg" alt="Pixel logo" /> <img class="navbar-brand-light" src="<?= $webroot ?>/html&css/assets/img/brand/dark.svg" alt="Pixel Logo Dark" />
        </a>
        <div class="d-flex align-items-center">
            <button class="navbar-toggler d-md-none collapsed" type="button" data-toggle="collapse" data-target="#sidebarMenu" aria-controls="sidebarMenu" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
        </div>
    </nav>

    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <?= $content ?>

            </div>
        </div>
    </div>
    <?php $this->endBody() ?>

</body>
<script>
    $(function() {

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