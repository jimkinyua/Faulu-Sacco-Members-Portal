<?php

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
$absoluteUrl = \yii\helpers\Url::home(true);

?>
           
                    <h3 class="card-title"></h3>
                    <div class="card-body"> 
                        <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>
                            <?= $form->field($model,'Key')->hiddenInput()->label(false) ?>
                            <div class="row">
                                <div class=" row col-md-12">
                                        <div class="col-md-12">
                                            <?= $form->field($model, 'Loan_No')->dropDownList($MemberLoans,['prompt' => '-- Select Loan --']) ?>
                                        </div>   
                                </div>
                            </div>
                    
                            <div class="row">
                                <div class="form-group">
                                    <?= Html::submitButton('Save', ['class' => 'btn btn-success ', 'id'=>'SubmitButton']) ?>
                                </div>
                            </div>     

                        <?php ActiveForm::end(); ?>
                    </div>
<input type="hidden" name="url" value="<?= $absoluteUrl ?>">

<?php

    $script = <<<JS
    $(function(){
           
        $('.create').on('click',function(e){
            e.preventDefault();
            var url = $(this).attr('href');
            console.log(url)

            console.log('clicking...');
            $('.modal').modal('show')
                            .find('.modal-body')
                            .load(url); 
    
         });

         $('.update').on('click',function(e){
            e.preventDefault();
            var url = $(this).attr('href');
            console.log('clicking...');
            $('.modal').modal('show')
                            .find('.modal-body')
                            .load(url); 
    
         });
        
        /*Handle dismissal eveent of modal */
        $('.modal').on('hidden.bs.modal',function(){
            var reld = location.reload(true);
            setTimeout(reld,1000);
        });


        $('#loanapplicationheader-loan_product').change(function(e){
            const Leave_Code = e.target.value;
            const No = $('#loanapplicationheader-application_no').val();
            if(No.length){
                
                const url = $('input[name=url]').val()+'loan/set-loan-product';
                $.post(url,{'LoanProduct': Leave_Code,'LoanNo': No}).done(function(msg){
                    //populate empty form fields with new data
                    
                    // $('#loanapplicationheader_balance').val(msg.Leave_balance);  
                    $('#loanapplicationheader-key').val(msg.Key);
                          console.log(typeof msg);
                        console.table(msg);
                        if((typeof msg) === 'string') { // A string is an error
                            const parent = document.querySelector('.field-loanapplicationheader-loan_product');
                            const helpbBlock = parent.children[2];
                            helpbBlock.innerText = msg;
                            disableSubmit();
                            
                        }else{ // An object represents correct details
                            const parent = document.querySelector('.field-loanapplicationheader-loan_product');
                            const helpbBlock = parent.children[2];
                            helpbBlock.innerText = '';
                            $('#loanapplicationheader-key').val(msg.Key);
                            $('#loanapplicationheader-total_principle_repayment').val(msg.Total_Principle_Repayment);
                            $('#loanapplicationheader-total_interest_repayment').val(msg.Total_Interest_Repayment);
                            $('#loanapplicationheader-total_loan_repayment').val(msg.Total_Loan_Repayment);
                            $('#loanapplicationheader-monthly_installment').val(msg.Monthly_Installment);
                            // enableSubmit();
                            
                        }
                        
                    },'json');
                
            }     
        });

        $('#loanapplicationheader-applied_amount').change(function(e){
            const Applied_Amount = e.target.value;
            const No = $('#loanapplicationheader-application_no').val();
            if(No.length){
                
                const url = $('input[name=url]').val()+'loan/set-loan-applied-amount';
                $.post(url,{'Applied_Amount': Applied_Amount,'LoanNo': No}).done(function(msg){
                    //populate empty form fields with new data
                        console.log(typeof msg);
                        console.table(msg);
                        if((typeof msg) === 'string') { // A string is an error
                            const parent = document.querySelector('.field-loanapplicationheader-applied_amount');
                            const helpbBlock = parent.children[2];
                            helpbBlock.innerText = msg;
                            disableSubmit();
                            
                        }else{ // An object represents correct details
                            const parent = document.querySelector('.field-loanapplicationheader-applied_amount');
                            const helpbBlock = parent.children[2];
                            helpbBlock.innerText = ''; 
                            $('#loanapplicationheader-key').val(msg.Key);
                            $('#loanapplicationheader-total_principle_repayment').val(msg.Total_Principle_Repayment);
                            $('#loanapplicationheader-total_interest_repayment').val(msg.Total_Interest_Repayment);
                            $('#loanapplicationheader-total_loan_repayment').val(msg.Total_Loan_Repayment);
                            $('#loanapplicationheader-monthly_installment').val(msg.Monthly_Installment);
                            // enableSubmit();
                            
                        }
                        
                    },'json');
                
            }     
        });

        $('#loanapplicationheader-repayment_period_m').change(function(e){
            const Repayment_Period_M = e.target.value;
            const No = $('#loanapplicationheader-application_no').val();
            if(No.length){
                
                const url = $('input[name=url]').val()+'loan/set-loan-repayment-period';
                $.post(url,{'Repayment_Period_M': Repayment_Period_M,'LoanNo': No}).done(function(msg){
                    //populate empty form fields with new data
                        console.log(typeof msg);
                        console.table(msg);
                        if((typeof msg) === 'string') { // A string is an error
                            const parent = document.querySelector('.field-loanapplicationheader-repayment_period_m');
                            const helpbBlock = parent.children[2];
                            helpbBlock.innerText = msg;
                            disableSubmit();
                            
                        }else{ // An object represents correct details
                            const parent = document.querySelector('.field-loanapplicationheader-repayment_period_m');
                            const helpbBlock = parent.children[2];
                            helpbBlock.innerText = ''; 
                            $('#loanapplicationheader-key').val(msg.Key);
                            $('#loanapplicationheader-total_principle_repayment').val(msg.Total_Principle_Repayment);
                            $('#loanapplicationheader-total_interest_repayment').val(msg.Total_Interest_Repayment);
                            $('#loanapplicationheader-total_loan_repayment').val(msg.Total_Loan_Repayment);
                            $('#loanapplicationheader-monthly_installment').val(msg.Monthly_Installment);
                            enableSubmit();
                            
                        }
                        
                    },'json');
                
            }     
        });
        

    });

    function disableSubmit(){
             document.getElementById('SubmitButton').setAttribute("disabled", "true");
        }
        
        function enableSubmit(){
            document.getElementById('SubmitButton').removeAttribute("disabled");
        
        }
        
JS;

$this->registerJs($script);
