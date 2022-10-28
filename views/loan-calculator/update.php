<?php

use yii\helpers\Html;

// echo '<pre>';
// print_r($rePaymentLines);
// exit;

/* @var $this yii\web\View */
/* @var $model frontend\models\AgendaDocument */

// $this->title = 'Update Applicant Personal info.: ' . $model->No;
// $this->params['breadcrumbs'][] = ['label' => 'Recruitment ', 'url' => ['index']];
// $this->params['breadcrumbs'][] = ['label' => 'Update Applicant Profile', 'url' => ['applicantprofile/update','No' => $model->No ]];
?>
<div class="agenda-document-update">

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



    <?= $this->render('_form', [
        'model' => $model,
        'loanProducts' => $loanProducts,
        'rePaymentLines' => $rePaymentLines,

    ]) ?>

</div>