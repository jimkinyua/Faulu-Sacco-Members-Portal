<?php

use yii\bootstrap4\ActiveForm;
use yii\bootstrap4\Html;

// echo '<pre>';
// print_r($loanData);
// exit;
?>

<div class="card-body">
    <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>
    <?= $form->field($model, 'Key')->hiddenInput()->label(false) ?>
    <?= $form->field($model, 'Loan_No')->hiddenInput()->label(false) ?>
    <?= $form->field($model, 'PhoneNo')->hiddenInput()->label(false) ?>
    <?= $form->field($model, 'Member_Name')->hiddenInput()->label(false) ?>
</div>

<div class="row">
    <div class=" row col-md-12">
        <div class="col-md-12">
            <?= $form->field($model, 'AcceptTerms')->checkbox() ?>
            <?= $form->field($model, 'Requested_Amount')->textInput(['readonly' => true,  'value' => number_format($model->Requested_Amount)]) ?>
            <?= $form->field($model, 'Amount_Accepted')->textInput(['type' => 'number', 'maxlength' => true]) ?>
        </div>
    </div>
</div>

<div class="row">
    <div class="form-group">
        <?= Html::submitButton('Accept', ['class' => 'btn btn-success']) ?>
    </div>
</div>