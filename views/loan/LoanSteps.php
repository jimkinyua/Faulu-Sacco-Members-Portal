<?php

use yii\bootstrap4\ActiveForm;

$profileAction = (Yii::$app->user->identity->{'No_'}) ? 'update?No=' . Yii::$app->user->identity->{'No_'} : 'view-profile';

// echo '<pre>';
// print_r($model);
// exit;
?>

<style>
    /*custom font*/
    @import url(https://fonts.googleapis.com/css?family=Merriweather+Sans);

    * {
        margin: 0;
        padding: 0;
    }

    html,
    body {
        min-height: 100%;
    }

    .breadcrumbb {
        text-align: center;
        /* padding-top: 50px; */
        /* background: #689976;
        background: linear-gradient(#e90b0b, #EE7E7E); */
        font-family: 'Merriweather Sans', arial, verdana;
    }

    .breadcrumbb {
        /*centering*/
        display: inline-block;
        box-shadow: 0 0 15px 1px rgba(0, 0, 0, 0.35);
        overflow: hidden;
        border-radius: 5px;
        /*Lets add the numbers for each link using CSS counters. flag is the name of the counter. to be defined using counter-reset in the parent element of the links*/
        counter-reset: flag;
    }

    .help-block {
        color: red;
    }

    .required>label:after {
        content: " *";
        color: red;
        display: inline-block;
        padding-left: 0.2em;
    }

    .breadcrumbb a {
        text-decoration: none;
        outline: none;
        display: block;
        float: left;
        font-size: 12px;
        line-height: 36px;
        color: white;
        /*need more margin on the left of links to accomodate the numbers*/
        padding: 0 10px 0 60px;
        background: #666;
        /* background: linear-gradient(#666, #333); */
        position: relative;
    }

    /*since the first link does not have a triangle before it we can reduce the left padding to make it look consistent with other links*/
    .breadcrumbb a:first-child {
        padding-left: 46px;
        border-radius: 5px 0 0 5px;
        /*to match with the parent's radius*/
    }

    .breadcrumbb a:first-child:before {
        left: 14px;
    }

    .breadcrumbb a:last-child {
        border-radius: 0 5px 5px 0;
        /*this was to prevent glitches on hover*/
        padding-right: 20px;
    }

    /*hover/active styles*/
    .breadcrumbb a.active,
    .breadcrumbb a:hover {
        background: #333;
        background: linear-gradient(#333, #000);
    }

    .breadcrumbb a.active:after,
    .breadcrumbb a:hover:after {
        background: #333;
        background: linear-gradient(135deg, #333, #000);
    }

    /*adding the arrows for the breadcrumbbs using rotated pseudo elements*/
    .breadcrumbb a:after {
        content: '';
        position: absolute;
        top: 0;
        right: -18px;
        /*half of square's length*/
        /*same dimension as the line-height of .breadcrumb a */
        width: 36px;
        height: 36px;
        /*as you see the rotated square takes a larger height. which makes it tough to position it properly. So we are going to scale it down so that the diagonals become equal to the line-height of the link. We scale it to 70.7% because if square's: 
            length = 1; diagonal = (1^2 + 1^2)^0.5 = 1.414 (pythagoras theorem)
            if diagonal required = 1; length = 1/1.414 = 0.707*/
        transform: scale(0.707) rotate(45deg);
        /*we need to prevent the arrows from getting buried under the next link*/
        z-index: 1;
        /*background same as links but the gradient will be rotated to compensate with the transform applied*/
        background: #666;
        background: linear-gradient(135deg, #666, #333);
        /*stylish arrow design using box shadow*/
        box-shadow:
            2px -2px 0 2px rgba(0, 0, 0, 0.4),
            3px -3px 0 2px rgba(255, 255, 255, 0.1);
        /*
                5px - for rounded arrows and 
                50px - to prevent hover glitches on the border created using shadows*/
        border-radius: 0 5px 0 50px;
    }

    /*we dont need an arrow after the last link*/
    .breadcrumbb a:last-child:after {
        content: none;
    }

    /*we will use the :before element to show numbers*/
    .breadcrumbb a:before {
        content: counter(flag);
        counter-increment: flag;
        /*some styles now*/
        border-radius: 100%;
        width: 20px;
        height: 20px;
        line-height: 20px;
        margin: 8px 0;
        position: absolute;
        top: 0;
        left: 30px;
        background: #444;
        background: linear-gradient(#444, #222);
        font-weight: bold;
    }


    .flat a,
    .flat a:after {
        background: white;
        color: black;
        transition: all 0.5s;
    }

    .flat a:before {
        background: white;
        box-shadow: 0 0 0 1px #ccc;
    }

    .flat a:hover,
    .flat a.active,
    .flat a:hover:after,
    .flat a.active:after {
        background: #0672FC;
    }
</style>

<!-- another version - flat style with animated hover effect -->

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <div class="breadcrumbb flat">

                    <a href="<?= Yii::$app->recruitment->absoluteUrl() . 'loan/update?Key=' . $model->Key . '&DocumentNo=' . $model->Loan_No ?>" <?= Yii::$app->recruitment->currentaction('loan', 'update') ? 'class="active"' : ''  ?> id="0">Loan Product </a>
                    <a href="<?= Yii::$app->recruitment->absoluteUrl() . 'loan/schedule?Key=' . $model->Key ?>" <?= Yii::$app->recruitment->currentaction('loan', 'schedule') ? 'class="active"' : '' ?> id="1">View Schedule </a>
                    <a href="<?= Yii::$app->recruitment->absoluteUrl() . 'payslip-information/index?Key=' . $model->Key ?>" <?= Yii::$app->recruitment->currentaction('payslip-information', 'index') ? 'class="active"' : '' ?> id="2">Salary Details</a>
                    <!-- <a href="<?= Yii::$app->recruitment->absoluteUrl() . 'payment-details/update?Key=' . $model->Key . '&DocumentNo=' . $model->Loan_No ?>" <?= Yii::$app->recruitment->currentaction('payment-details', 'update') ? 'class="active"' : '' ?> id="3"> Payment Details </a> -->
                    <a href="<?= Yii::$app->recruitment->absoluteUrl() . 'internal-deductions/index?Key=' . $model->Key ?>" <?= Yii::$app->recruitment->currentaction('internal-deductions', 'index') ? 'class="active"' : '' ?> id="4">Loan Bridging</a>
                    <!-- <a href="<?= Yii::$app->recruitment->absoluteUrl() . 'documents/index?Key=' . $model->Key ?>" <?= Yii::$app->recruitment->currentaction('documents', 'index') ? 'class="active"' : '' ?> id="5"> Documents </a> -->
                    <a href="<?= Yii::$app->recruitment->absoluteUrl() . 'guarantors/index?Key=' . $model->Key ?>" <?= Yii::$app->recruitment->currentaction('guarantors', 'index') ? 'class="active"' : '' ?> id="6">Loan Guarantors</a>
                    <a href="<?=  Yii::$app->recruitment->absoluteUrl() .'sign-application/?Key='.$model->Key ?>" <?= Yii::$app->recruitment->currentaction('sign-application','index')?'class="active"': '' ?>>Documents</a>
                    <a href="<?= Yii::$app->recruitment->absoluteUrl() . 'loan/send-for-approval?Key=' . $model->Key ?>" <?= Yii::$app->recruitment->currentaction('loan', 'send-for-approval') ? 'class="active"' : '' ?> id="8">Confim </a>


                </div>
            </div>

            <?php
            print ' <div class="alert alert-danger alert-dismissable ErrorPage">
                                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                                <h5><i class="icon fas fa-check"></i> Error!</h5>';
            print '</div>';
            ?>
            <div class="col-md-12">
                <div class="card">

                    <div class="card-content collpase show">
                        <div class="card-body">
                            <div class="card-text">
                            </div>
                            <div class="form-body"