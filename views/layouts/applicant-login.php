<?php

/* @var $this \yii\web\View */
/* @var $content string */

use app\assets\LoginAsset;
use app\widgets\Alert;
use yii\bootstrap4\Breadcrumbs;
use yii\bootstrap4\Html;
use yii\bootstrap4\Nav;
use yii\bootstrap4\NavBar;
use yii\helpers\Url;



LoginAsset::register($this);

$webroot = Yii::getAlias(@$webroot);
$absoluteUrl = \yii\helpers\Url::home(true);

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
    <meta name="msapplication-TileColor" content="#ffffff">
    <meta name="theme-color" content="#ffffff">
    <?php $this->registerCsrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>

<body class="d-flex flex-column h-100">
    <?php $this->beginBody() ?>
    <!-- Section -->


    <main>
        <div class="preloader bg-soft flex-column justify-content-center align-items-center">
            <img class="loader-element" src="<?= $webroot ?>/html&css/assets/img/brand/preloader-spinner.gif" height="50" alt="Mhasibu logo">
        </div>

        <section class="vh-100 d-flex align-items-center section-image overlay-soft-dark" data-background="../assets/img/saas-form-image.jpg">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-12 d-flex align-items-center justify-content-center">
                        <div class="signin-inner mt-3 mt-lg-0 bg-white shadow-soft border rounded border-light p-4 p-lg-5 w-100 fmxw-500">
                            <div class="text-center text-md-center mb-4 mt-md-0">
                                <h1 class="mb-0 h3"> Member Applicant Login </h1>
                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <?php
                                    if (Yii::$app->session->hasFlash('success')) {
                                        print ' <div class="alert alert-success alert-dismissable">
                                                                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                                                                            <h5><i class="icon fas fa-check"></i> </h5>';
                                        echo Yii::$app->session->getFlash('success');
                                        print '</div>';
                                    } else if (Yii::$app->session->hasFlash('error')) {
                                        print ' <div class="alert alert-danger alert-dismissable">
                                                                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                                                                            <h5><i class="icon fas fa-check"></i> </h5>
                                                                                    ';
                                        echo Yii::$app->session->getFlash('error');
                                        print '</div>';
                                    }
                                    ?>
                                </div>
                            </div>

                            <?= $content ?>
                            <div class="d-block d-sm-flex justify-content-center align-items-center mt-4">
                                <span class="font-weight-normal">
                                    <a href="<?= Url::to('site/login', $schema = true) ?>" class="font-weight-bold">Member Login</a>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>



        <footer class="footer mt-auto py-3 text-muted">
            <div class="container">
                <p class="float-left">&copy; Faulu Sacco <?= date('Y') ?></p>
                <p class="float-right"><?= Yii::powered() ?></p>
            </div>
        </footer>
    </main>

    <?php $this->endBody() ?>
</body>

</html>
<?php $this->endPage() ?>