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

<body class="">
    <?php $this->beginBody() ?>
    <!-- Section -->

    <main>
        <div class="preloader bg-soft flex-column justify-content-center align-items-center">
            <img class="loader-element" src="<?= $webroot ?>/html&css/assets/img/brand/preloader-spinner.gif" height="50" alt="Faulu logo">
        </div>

        <section class="vh-100 bg-soft d-flex align-items-center">
            <div class="container">
                <div class="row justify-content-center ">
                    <div class="col-12 d-flex align-items-center justify-content-center">
                        <div class="signin-inner mt-3 mt-lg-1 bg-white shadow-soft border border-light rounded p-lg-5 w-100 fmxw-800">
                            <img style="margin-left: 12em;" src="<?= $webroot ?>/html&css/assets/img/SaccoLogo.png" class="navbar-brand-dark rotate-logo" alt="Faulu Sacco">

                            <?= $content ?>

                            <div class="d-block d-sm-flex justify-content-center align-items-center mt-4">
                                <span class="font-weight-normal">
                                    Already have an account?
                                    <a href="<?= Url::to('site/login', $schema = true) ?>" class="font-weight-bold">Login here</a>
                                </span>
                            </div>

                        </div>
                    </div>
                </div>
            </div>

        </section>


    </main>

    <?php $this->endBody() ?>
</body>

</html>
<?php $this->endPage() ?>