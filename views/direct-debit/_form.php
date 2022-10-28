<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
// use \kartik\FileInput;
use kartik\file\FileInput;


?>   
                    <h3 class="card-title">Bank Details </h3>
              
                    <div class="card-body">
                        <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>
                        <?= $form->field($model,'Key')->hiddenInput()->label(false) ?>
                        <?= $form->field($model,'Loan_No')->hiddenInput()->label(false) ?>

                        <div class="row">
                            <div class=" row col-md-12">

                                <div class="col-md-6">
                                    <?= $form->field($model, 'Bank_Code')->textInput(['type'=>'text']) ?>
                                    <?= $form->field($model, 'Amount')->textInput(['type'=>'number', 'minlength'=>true]) ?>             
                                </div>

                                <div class="col-md-6">
                                    <?= $form->field($model, 'Branch_Code')->textInput(['type'=>'text']) ?>
                                    <?= $form->field($model, 'Account_No')->textInput(['type'=>'number', 'minlength'=>true]) ?>
                                    <?= $form->field($model, 'docFile')->fileInput() ?>
                                    <?php if($model->isNewRecord == false): ?>
                                    <iframe src="data:application/pdf;base64,<?= $content; ?>" ></iframe>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class=" row col-12">
                                <div class="col-12">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group">
                                <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
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
                dropdown.replaceWith('<input type="text" id="loanappsecurities-code" class="form-control" name="LoanAppSecurities[Code]" aria-required="true">');
            }else{
                var url = '/guarantors/get-loan-securities';
                dropdown.replaceWith('<select id="loanappsecurities-code" class="form-control" name="LoanAppSecurities[Code]" aria-required="true"></select>');
                $.get(url, {'SecurityType': SecurityType}, function(response) {
                    $('#loanappsecurities-code').empty();
                    $('#loanappsecurities-code').append($('<option id="itemId"></option>').attr('value', '').text('-- Select Option --')); //append Here
                $.each(response, function (key, entry) {
                    $('#loanappsecurities-code').append($('<option id="itemId'+ entry.Code+'"></option>').attr('value', entry.Code).text(entry.Description)); //append Here
                })
            })
            }
           
    
        });
    });
        
JS;

$this->registerJs($script);

