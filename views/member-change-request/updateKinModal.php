<?php

use yii\helpers\Html;
use borales\extensions\phoneInput\PhoneInput;
use yii\bootstrap4\ActiveForm;
use yii\helpers\Url;
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
                                <?= $form->field($model, 'KIN_ID')->textInput() ?>

                            </div>

                            <div class="col-md-4">



                                <?= $form->field($model, 'Date_of_Birth')->textInput(['type' => 'date']) ?>

                                <?= $form->field($model, 'Kin_Type')->dropDownList([
                                    'Child' => 'Child',
                                    'Spouse' => 'Spouse',
                                    'Parent' => 'Parent',
                                    'Nephew' => 'Nephew',
                                    'Niece' => 'Niece',
                                    'Uncle' => 'Uncle',
                                    'Aunt' => 'Aunt',
                                    'Cousin' => 'Cousin',
                                    'Other' => 'Other',
                                ], ['prompt' => 'Select Relationship']) ?>

                            </div>

                            <div class="col-md-4">
                                <?= $form->field($model, 'Phone_No')->widget(PhoneInput::className(), [
                                    'jsOptions' => [
                                        'preferredCountries' => ['ke'],
                                    ]
                                ]) ?>
                                <?= $form->field($model, 'Allocation')->textInput(['']) ?>

                            </div>

                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
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