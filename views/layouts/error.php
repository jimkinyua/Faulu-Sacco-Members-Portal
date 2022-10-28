<?php

/* @var $this \yii\web\View */
/* @var $content string */

use app\assets\ErrorAsset;
use app\widgets\Alert;
use yii\bootstrap4\Breadcrumbs;
use yii\bootstrap4\Html;
use yii\bootstrap4\Nav;
use yii\bootstrap4\NavBar;
use yii\helpers\Url;



ErrorAsset::register($this);

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

        <body>
            <?php $this->beginBody() ?>
                <main>
                    <div class="preloader bg-soft flex-column justify-content-center align-items-center">
                        <img class="loader-element" src="<?= $webroot ?>/html&css/assets/img/brand/preloader-spinner.gif" height="50" alt="Mhasibu logo">
                    </div>

                    <section class="vh-100 d-flex align-items-center justify-content-center">
                        <div class="container">
                            <div class="row">
                                <div class="col-12 col-lg-6 order-2 order-lg-1 text-center text-lg-left">
                                    <h1 class="mt-5">Something has gone <span class="text-primary">seriously</span> wrong</h1>
                                    <?= $content ?>
                                    <p class="lead my-4">It's always time for a coffee break. We should be back by the time you finish your coffee.</p>
                                    <a class="btn btn-primary animate-hover" href="/"><i class="fas fa-chevron-left mr-3 pl-2 animate-left-3"></i>Go
                                        back home</a>
                                </div>
                                <div class="col-12 col-lg-6 order-1 order-lg-2 text-center d-flex align-items-center justify-content-center">
                                    <img class="img-fluid w-75" src="<?= $webroot ?>/html&css/assets/img/illustrations/500.svg" alt="500 Server Error">
                                </div>
                            </div>
                        </div>
                    </section>

                </main>
            <?php $this->endBody() ?>
        </body>
    </html>
<?php $this->endPage() ?>
