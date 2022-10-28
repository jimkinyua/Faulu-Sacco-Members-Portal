<?php
use yii\widgets\ActiveForm;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
// echo '<pre>';
// print_r($model);
// exit;

?>

<div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"> CRB Clearance Certificate</h3>

                </div>
                <div class="card-body">
                    

                        <?php $form = ActiveForm::begin(
                            [
                                 'id' => 'login-form',
                                'encodeErrorSummary' => true,
                                'errorSummaryCssClass' => 'help-block',
                            ],
                            ['options' => [
                            'enctype' => 'multipart/form-data']]
                        ) ?>

                            <?= $form->errorSummary($model) ?>

                            <div class="row">
                                <div class="col-md-6">
                                    <?= $form->field($model,'LoanNo')->hiddenInput()->label(false) ?>
                                    <?= $form->field($model,'MemberNo')->hiddenInput()->label(false) ?>

                                    <?php if($model->isNewRecord == true): ?>
                                        <?php else: ?>
                                            <?= $form->field($model, 'File_Name')->textInput(['readonly'=>true, 'value'=>'CRB Clearance Certificate']);?>
                                        <?php endif ?>
                                </div>
                                <div class="col-md-6">

                                    <?= $form->field($model, 'docFile')->fileInput() ?>

                                </div>

                            </div>

                            <div class="form-group">
                                <?= Html::submitButton('Upload', ['class' => 'btn btn-success']) ?>
                            </div>

                        <?php ActiveForm::end() ?>


                </div>
            </div>
