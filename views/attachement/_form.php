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
                    <h3 class="card-title"> File Uploads</h3>

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
                                    <?php if($model->isNewRecord == true): ?>
                                        <?= $form->field($model, 'File_Name')->dropDownList(ArrayHelper::map($RequiredDocuments, 'Code', 'Name'),['prompt' => '-- Select Option--']) ?>
                                        <?php else: ?>
                                            <?= $form->field($model, 'File_Name')->textInput(['readonly'=>true]);?>
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
