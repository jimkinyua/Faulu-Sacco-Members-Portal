<?php

/* @var $this \yii\web\View */
/* @var $content string */

use app\assets\GuestAsset;
use app\widgets\Alert;
use yii\bootstrap4\Breadcrumbs;
use yii\bootstrap4\Html;
use yii\bootstrap4\Nav;
use yii\bootstrap4\NavBar;
use yii\helpers\Url;

GuestAsset::register($this);

$webroot = Yii::getAlias(@$webroot);
$absoluteUrl = \yii\helpers\Url::home(true);

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

    <div class="preloader bg-white flex-column justify-content-center align-items-center">
        <img class="loader-element" src="<?= $webroot ?>/html&css/assets/img/brand/preloader-spinner.gif" height="50" alt="Preloader">
    </div>

    <!-- Hero -->
    <section class="vh-100 d-flex align-items-center section-image">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-12 d-flex align-items-center justify-content-center">
                    <div class="signin-inner mt-3 mt-lg-0 bg-white shadow-soft border rounded border-light p-4 p-lg-5 w-100 fmxw-500">
                        <img style="margin-left: 4.5em;" src="<?= $webroot ?>/html&css/assets/img/SaccoLogo.png" class="navbar-brand-dark rotate-logo" alt="Faulu Sacco">
                        <?= $content ?>

                    </div>
                </div>
            </div>
        </div>
    </section>

    <?php $this->endBody() ?>
</body>

</html>
<?php $this->endPage() ?>