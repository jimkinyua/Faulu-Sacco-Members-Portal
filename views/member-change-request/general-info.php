<?php

use yii\helpers\Html;
use kartik\date\DatePicker;
use yii\bootstrap4\ActiveForm;
use borales\extensions\phoneInput\PhoneInput;

// echo '<pre>';
// print_r($model);
// exit;
$this->title = 'Change Request';

?>

<!-- <h3 class="card-title">Guarantor/Security Details</h3> -->

<div class="card-body">

    <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data', 'options' => ['autocomplete' => 'off']]]); ?>
    <?= $form->field($model, 'Key')->hiddenInput()->label(false) ?>
    <?= $form->field($model, 'Document_No')->hiddenInput()->label(false) ?>

    <h3> General Details </h3>
    <div class="row">
        <div class=" row col-md-12">
            <div class=" row col-md-12">

                <div class="col-md-6">
                    <?= $form->field($model, '_x0026_E_Mail_Address')->textInput([]) ?>

                    <?= $form->field($model, 'Marital_Status')->dropDownList([
                        'Single' => 'Single',
                        'Married' => 'Married',
                        'Separated' => 'Separated',
                        'Divorced' => 'Divorced',
                        'Widowed' => 'Widowed',
                        'Divorced' => 'Divorced',
                        'Withheld' => 'Withheld'
                    ], ['prompt' => 'Select Status'])

                    ?>


                    <?= $form->field($model, 'Type_of_Residence')->dropDownList([
                        'Rented' => 'Rented',
                        'Owned' => 'Owned',
                    ], ['prompt' => 'Select Type of Residence'])

                    ?>
                    <?= $form->field($model, 'Mobile_Transacting_No')->widget(PhoneInput::className(), [
                        'jsOptions' => [
                            'onlyCountries' => ['ke'],
                        ]
                    ]) ?>





                </div>

                <div class="col-md-6">
                    <?= $form->field($model, 'Town_of_Residence')->textInput() ?>
                    <?= $form->field($model, 'Estate_of_Residence')->textInput(['placeholder' => 'Estate_of_Residence',]) ?>
                    <?= $form->field($model, 'Date_of_Birth')->textInput(['placeholder' => 'E-mail Address', 'type' => 'date']) ?>
                    <?= $form->field($model, 'Mobile_Phone_No')->widget(PhoneInput::className(), [
                        'jsOptions' => [
                            'preferredCountries' => ['ke'],
                        ]
                    ]) ?>




                </div>


            </div>


            <div class="col-xl-6 col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Passport Photo</h4>
                    </div>
                    <div class="card-content">
                        <div class="col-xl-3 col-md-3 col-6">
                            <div class="border-lighten-9">
                                <div class="text-center">
                                    <div class="card-body">
                                        <img src="data:image/png;base64,<?= $image ?>" class="rounded-circle" alt="No Image Found" style="height:150px;width:150px;">
                                    </div>
                                    <div class="card-body">
                                        <?= $form->field($model, 'PassPortPhoto')->fileInput() ?>

                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-6 col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Signature</h4>
                    </div>
                    <div class="card-content">
                        <div class="col-xl-3 col-md-3 col-6">
                            <div class="border-lighten-9">

                                <div class="text-center">
                                    <div class="card-body">
                                        <img src="data:image/png;base64,<?= $signature ?>" class="rounded-circle" alt="No Signature Found" style="height:150px;width:150px;">
                                    </div>
                                    <div class="card-body">
                                        <?= $form->field($model, 'Signature')->fileInput() ?>
                                    </div>
                                </div>

                            </div>
                        </div>


                    </div>
                </div>
            </div>

        </div>
    </div>



    <div class="row">
        <div class="form-group">
            <?= Html::submitButton('Submit For Approval', ['class' => 'btn btn-success']) ?>
        </div>
    </div>



</div>

</div>

<?php ActiveForm::end(); ?>
</div>
</div>
<?php

$script = <<<JS

    $(function(){
        
        $('#loanappsecurities-type').change(function(e){
            const SecurityType = e.target.value;
            const No = $('#loanappsecurities-application_no').val();
            const Key = $('#loanappsecurities-key').val();
            var dropdown = $('#loanappsecurities-code');

            if(SecurityType == 'Guarantor'){
                var url = $('input[name=url]').val()+'guarantors/get-members';
            }else{
                var url = $('input[name=url]').val()+'guarantors/get-loan-securities';
            }
            $.get(url, {'SecurityType': SecurityType}, function(response) {
                dropdown.empty();
                dropdown.append($('<option id="itemId"></option>').attr('value', '').text('-- Select Option --')); //append Here
                $.each(response, function (key, entry) {
                    dropdown.append($('<option id="itemId'+ entry.Code+'"></option>').attr('value', entry.Code).text(entry.Description)); //append Here
                })
            })
    
        });
    });
        
JS;

$this->registerJs($script);
