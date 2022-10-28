<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model frontend\models\AgendaDocument */

// $this->title = 'Update Applicant Personal info.: ' . $model->No;
$this->params['breadcrumbs'][] = ['label' => 'Change Request', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->Document_No, 'url' => ['applicantprofile/update', 'No' => $model->Key]];
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



    <?= $this->render('update-employee', [
        'model' => $model,
        'RequiredAttachements' => $RequiredAttachements,
        'Employers' => $Employers,
    ]) ?>

</div>