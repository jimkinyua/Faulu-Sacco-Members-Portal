<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
?>
    
                    <?php if($model->Accepted === true): ?>
                        <h3 class="card-title">Guarantor Acceptance </h3>
                        <?php else: ?>
                            <h3 class="card-title">Guarantor Rejectance </h3>
                    <?php endif; ?>  

              
                    <div class="card-body">
                        <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>
                        <?= $form->field($model,'Key')->hiddenInput()->label(false) ?>
                        <?= $form->field($model,'Loan_No')->hiddenInput()->label(false) ?>
                        <?= $form->field($model,'PhoneNo')->hiddenInput()->label(false) ?>
                        <?= $form->field($model,'Member_Name')->hiddenInput()->label(false) ?>

                        <div class="row">
                            <div class=" row col-md-12">

                                <div class="col-md-12">
                                    <?php if($model->Accepted === true): ?>
                                        <?= $form->field($model, 'Loan_Principal')->textInput(['type'=>'number', 'maxlength' => true]) ?>
                                     <?php else: ?>
                                        <?= $form->field($model, 'Rejection_Reason')->textarea(['rows'=>'7', 'cols' => '4', 'minlength' => true]) ?>
                                    <?php endif; ?>   
                                </div>
                              
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group">
                                <?= Html::submitButton('Submit', ['class' => 'btn btn-success']) ?>
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

