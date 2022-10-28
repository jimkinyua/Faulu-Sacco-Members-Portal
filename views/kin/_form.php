<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use borales\extensions\phoneInput\PhoneInput;
use yii\helpers\Url;


$attachments = $model->getKinAttachments();

//$this->title = 'AAS - Employee Profile'
// echo '<pre>';
// print_r($attachments);
// exit;

?>
<!-- <h3 class="card-title">Kin Details</h3> -->

<div class="row match-height">
    <div class="col-md-12 col-sm-12">
        <div class="card border-danger bg-transparent">
            <div class="card-content">
                <div class="card-body pt-3">
                    <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>
                    <?= $form->field($model, 'Key')->hiddenInput()->label(false) ?>
                    <?= $form->field($model, 'isNewRecord')->hiddenInput()->label(false) ?>

                    <div class="row">
                        <div class=" row col-md-12">

                            <div class="col-md-4">
                                <?= $form->field($model, 'Name')->textInput() ?>
                                <?= $form->field($model, 'ID_No')->textInput() ?>

                            </div>

                            <div class="col-md-4">



                                <?= $form->field($model, 'Date_of_Birth')->textInput(['type' => 'date']) ?>

                                <?= $form->field($model, 'Relationship')->dropDownList($RelationshipTypes, ['prompt' => 'Select Relationship']) ?>

                            </div>

                            <div class="col-md-4">
                                <?= $form->field($model, 'Telephone')->widget(PhoneInput::className(), [
                                    'jsOptions' => [
                                        'preferredCountries' => ['ke'],
                                    ]
                                ]) ?>
                                <?= $form->field($model, 'Allocation')->textInput(['']) ?>
                                <?= $form->field($model, 'Beneficiary')->checkbox() ?>


                            </div>

                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="btn-group" role="group" aria-label="Basic example">
                                <?= Html::submitButton('Save Kin Details', ['class' => 'btn btn-info']) ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>














<?php ActiveForm::end(); ?>
</div>
</div>