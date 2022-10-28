<?php
use yii\helpers\Html;
$this->title = 'Next of Kins';
$this->params['breadcrumbs'][] = ['label' => 'Recruitment ', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => 'Create Applicant Profile', 'url' => ['applicantprofile/create']];
?>
<div class="leave-document-create">

    <div class="row">
        <div class="col-md-12">


                <?php

                if(Yii::$app->session->hasFlash('success')){
                    print ' <div class="alert alert-success alert-dismissable">
                             <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                             <h5><i class="icon fas fa-check"></i> Success!</h5>';
                    echo Yii::$app->session->getFlash('success');
                    print '</div>';
                }else if(Yii::$app->session->hasFlash('error')){
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

    <!--<h1><?= Html::encode($this->title) ?></h1>-->

    <?= $this->render('_form', [
        'model' => $model,
        'ExternalRecoveries' => $ExternalRecoveries,
        // 'religion' => $religion

    ]) ?>

</div>
