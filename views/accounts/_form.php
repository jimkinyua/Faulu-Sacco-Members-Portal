<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\depdrop\DepDrop;
use yii\helpers\Url;
?>
        
                    <h3 class="card-title">Internal Deduction Details </h3>
              
                    <div class="card-body">
                        <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>
                        <?= $form->field($model,'Key')->hiddenInput()->label(false) ?>
                        <?= $form->field($model,'Loan_No')->hiddenInput()->label(false) ?>

                        <div class="row">
                            <div class=" row col-md-12">

                                <div class="col-md-6">

                                    <?= $form->field($model, 'Deduction_Type')->dropDownList([
                                    '0' => 'Loan',
                                    '1' => 'Share_Capital_Boost',
                                    '2' => 'NWD_Deposit',
                                    ],['prompt' => '-- Select Option --', 'id'=>'Deduction_Type-ID']) ?>
                                    
                                    <?= $form->field($model, 'Account_Type')->widget(DepDrop::classname(), [
                                        'options'=>['id'=>'Account_Type_ID'],
                                        'pluginOptions'=>[
                                            'depends'=>['Deduction_Type-ID'],
                                            'placeholder'=>'Select...',
                                            'url'=>Url::to(['/internal-deductions/account-types'])]
                                    ]); ?> 
                                    
                                    <?= $form->field($model, 'Account_No')->widget(DepDrop::classname(), [
                                        'pluginOptions'=>[
                                            'depends'=>['Account_Type_ID', 'Deduction_Type-ID'],
                                            'placeholder'=>'Select...',
                                            'url'=>Url::to(['/internal-deductions/account-nos'])
                                        ]]); 
                                    ?>
                                    
                                </div>

                                <div class="col-md-6">
                                    <?= $form->field($model, 'Description')->textInput(['type'=>'text']) ?>
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

    // $(function(){
        
    //     $('#Deduction_Type-ID').change(function(e){
    //         const Deduction_Type = e.target.value;
    //         const No = $('#loanappsecurities-application_no').val();
    //         const Key = $('#loanappsecurities-key').val();
    //         var dropdown = $('#loaninternaldeductions-account_no');

    //         if(Deduction_Type == 'Share_Capital_Boost'){
    //             $('#loaninternaldeductions-account_no').replaceWith('<input type="text" id="loaninternaldeductions-account_no" readonly="true" value="Not Applicable" class="form-control" name="LoanInternalDeductions[Account_No]" aria-required="true">');
    //         }else{
    //             $('#loaninternaldeductions-account_no').replaceWith('<select id="loaninternaldeductions-account_no" class="form-control" name="LoanInternalDeductions[Account_No]" data-krajee-depdrop="depdrop_313eb25c" aria-required="true" aria-invalid="true"></select>');
    //         }
           
    
    //     });
    // });
        
JS;

$this->registerJs($script);

