<?php

use yii\bootstrap4\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;

$absoluteUrl = \yii\helpers\Url::home(true);
$this->title = 'Loan Calculator';

?>
<!--THE STEPS THING--->


<!--END THE STEPS THING--->
<h3 class="card-title"></h3>




<div class="card-body border-info">

    <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>
    <?= $form->field($model, 'Key')->hiddenInput()->label(false) ?>
    <?= $form->field($model, 'Document_No')->hiddenInput()->label(false) ?>

    <div class="row">
        <div class="col-lg-6">
            <div class="card border-info">
                <div class="card-header">
                    <a class="heading-elements-toggle"><i class="la la-ellipsis-v font-medium-3"></i></a>
                </div>
                <div class="card-content collpase show">
                    <div class="card-body">
                        <div class="form-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <?= $form->field($model, 'Loan_Product')->dropDownList(Arrayhelper::map($loanProducts, 'Code', 'Name'), ['prompt' => 'Select Loan Type']) ?>
                                        <?= $form->field($model, 'Principal_Amount')->textInput(['type' => 'text', 'value' => number_format($model->Principal_Amount)]) ?>
                                        <?= $form->field($model, 'Interest_Rate')->textInput(['readonly' => true, 'disabled' => true, 'value' => number_format($model->Interest_Rate)]) ?>
                                        <?= $form->field($model, 'Installments_Months')->textInput(['type' => 'text', 'value' => number_format($model->Installments_Months)]) ?>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <?= $form->field($model, 'Current_Deposits')->textInput([
                                            'readonly' => true,
                                            'disabled' => true,
                                            'style' => 'color: red;',
                                            'value' => number_format($model->Current_Deposits)
                                        ])
                                        ?>
                                        <?= $form->field($model, 'Current_DepositsX4')->textInput([
                                            'readonly' => true,
                                            'disabled' => true,
                                            'style' => 'color: red;',
                                            'value' => number_format($model->Current_DepositsX4)
                                        ])
                                        ?>

                                        <?= $form->field($model, 'Ouststanding_Loans')->textInput([
                                            'readonly' => true,
                                            'disabled' => true,
                                            'style' => 'color: red;',
                                            'value' => number_format($model->Ouststanding_Loans)
                                        ])
                                        ?>

                                        <?= $form->field($model, 'Deposit_Appraisal')->textInput([
                                            'readonly' => true,
                                            'disabled' => true,
                                            'style' => 'color: red;',
                                            'value' => number_format($model->Deposit_Appraisal)
                                        ])
                                        ?>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6">
            <div class="card border-danger">
                <div class="card-header">
                    <a class="heading-elements-toggle"><i class="la la-ellipsis-v font-medium-3"></i></a>
                </div>
                <div class="card-content collpase show">
                    <div class="card-body">
                        <div class="form-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <?= $form->field($model, 'Basic_Pay')->textInput(['type' => 'text', 'value' => number_format($model->Principal_Amount)]) ?>
                                        <?= $form->field($model, 'Other_Allowances')->textInput(['type' => 'text', 'value' => number_format($model->Other_Allowances)]) ?>
                                        <?= $form->field($model, 'Overtime_Allowances')->textInput(['type' => 'text', 'value' => number_format($model->Overtime_Allowances)]) ?>
                                        <?= $form->field($model, 'Sacco_Dividend')->textInput(['type' => 'text', 'value' => number_format($model->Sacco_Dividend)]) ?>
                                        <?= $form->field($model, 'Total_Deductions')->textInput(['type' => 'text', 'value' => number_format($model->Total_Deductions)]) ?>

                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <?= $form->field($model, 'Cleared_Effects')->textInput(['type' => 'text', 'value' => number_format($model->Cleared_Effects)]) ?>


                                        <?= $form->field($model, 'Adjusted_Net_Income')->textInput([
                                            'readonly' => true,
                                            'disabled' => true,
                                            'style' => 'color: red;',
                                            'value' => number_format($model->Adjusted_Net_Income)
                                        ])
                                        ?>
                                        <?= $form->field($model, 'OneThird_Basic')->textInput([
                                            'readonly' => true,
                                            'disabled' => true,
                                            'style' => 'color: red;',
                                            'value' => number_format($model->OneThird_Basic)
                                        ])
                                        ?>

                                        <?= $form->field($model, 'Amount_Available')->textInput([
                                            'readonly' => true,
                                            'disabled' => true,
                                            'style' => 'color: red;',
                                            'value' => number_format($model->Amount_Available)
                                        ])
                                        ?>

                                        <?= Html::submitButton('Calculate', ['class' => 'btn btn-lg btn-primary submitButton',]) ?>


                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <hr>


    <H2 style="text-align: center;"> Payment Schedule </H2>

    <div class="col-md-10">
        <div class="panel panel-primary">
            <div class="panel-body">
                <table class="table table-condensed table-responsive">
                    <thead>
                        <tr>
                            <th class="small" style="font-size: 10px"># </th>
                            <th class="small" style="font-size: 10px">Due Date </th>
                            <th class="small" style="font-size: 10px">Principle Amount</th>
                            <th class="small" style="font-size: 10px">Monthly Interest</th>
                            <th class="small" style="font-size: 10px">Monthly Repayment</th>
                            <th class="small" style="font-size: 10px">Loan Balance</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php if (is_array($rePaymentLines)) : ?>
                            <?php foreach ($rePaymentLines as $key => $repaymentLine) : ?>
                                <tr>
                                    <td class="small" style="font-size:11px"><?= $key ?></td>
                                    <td class="small" style="font-size:11px"><?= $repaymentLine->Expected_Date ?></td>
                                    <td class="small" style="font-size:11px"><?= number_format($repaymentLine->Principal_Amount) ?></td>
                                    <td class="small" style="font-size:11px"><?= number_format($repaymentLine->Interest_Amount) ?></td>
                                    <td class="small" style="font-size:11px"><?= number_format($repaymentLine->Installment_Amount) ?></td>
                                    <td class="small" style="font-size:11px"><?= number_format($repaymentLine->Running_Balance) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>



                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

</div>


<?php ActiveForm::end(); ?>
</div>
</div>
<input type="hidden" name="url" value="<?= $absoluteUrl ?>">

<?php

$script = <<<JS
    $(function(){
           
        function numberWithCommas(x) {
            x=String(x).toString();
            var afterPoint = '';
            if(x.indexOf('.') > 0)
                afterPoint = x.substring(x.indexOf('.'),x.length);
            x = Math.floor(x);
            x=x.toString();
            var lastThree = x.substring(x.length-3);
            var otherNumbers = x.substring(0,x.length-3);
            if(otherNumbers != '')
                lastThree = ',' + lastThree;
            return otherNumbers.replace(/\B(?=(\d{2})+(?!\d))/g, ",") + lastThree + afterPoint;
        }

        function round(value, decimals) {
            return Number(Math.round(value +'e'+ decimals) +'e-'+ decimals).toFixed(decimals);
        }

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


        $('#loancalculatorheader-loan_product').change(function(e){
            const LoanProduct = e.target.value;
            let DocumentNo  = $('#loancalculatorheader-document_no').val();
            if(LoanProduct.length){
                
                const url = $('input[name=url]').val()+'loan-calculator/set-loan-product';
                $.post(url,{'LoanProduct': LoanProduct,'LoanNo': DocumentNo}).done(function(msg){
                    //populate empty form fields with new data
                    
                    // $('#loanapplicationheader_balance').val(msg.Leave_balance);  
                    $('#loancalculatorheader-key').val(msg.Key);
                          console.log(typeof msg);
                        // console.table(msg);
                        if((typeof msg) === 'string') { // A string is an error
                            const parent = document.querySelector('.field-loancalculatorheader-loan_product');
                            const helpbBlock = parent.children[2];
                            helpbBlock.innerText = msg;
                            disableSubmit();
                            
                        }else{ // An object represents correct details
                            const parent = document.querySelector('.field-loancalculatorheader-loan_product');
                            const helpbBlock = parent.children[2];
                            helpbBlock.innerText = '';
                            $('#loancalculatorheader-key').val(msg.Key);
                            $('#loancalculatorheader-monthly_principle').val(msg.Monthly_Principle);
                            $('#loancalculatorheader-total_interest').val(msg.Total_Interest);
                            $('#loancalculatorheader-monthly_interest').val(msg.Monthly_Interest);
                            $('#loancalculatorheader-monthly_installment').val(msg.Monthly_Installment); 
                            $('#loancalculatorheader-max_installments').val(msg.Max_Installments); 
                            $('#loancalculatorheader-interest_rate').val(msg.Interest_Rate); 
                            $('#loancalculatorheader-repayment_installments').val(msg.Max_Installments); 

                            if (msg.SheduleDetails.length === 0) { 
                                return false; //We''ll deal with this later. It may never happen
                            }else{
                                $('.PaymentDetails').closest('tr').remove();
                                msg.SheduleDetails.forEach(function (item, index) {
                                    var html = '<tr>'+
                                            '<th scope="row">'+index+'</th>'+
                                            '<td>'+item.Description+'</td>'+
                                            '<td>'+item.Document_Date+'</td>'+
                                            '<td>'+ numberWithCommas(parseInt(item.Amount)) +'</td>'+
                                        '</tr>';
                                       
                                    $('.PaymentDetails').after(html);
                                });
                            }                    
                        }
                        
                    },'json');
                
            }     
        });

        $('#loancalculatorheader-principle_amount').change(function(e){
            const Applied_Amount = e.target.value;
            const No = $('#loancalculatorheader-calculator_no').val();
            const Installments = $('#loancalculatorheader-repayment_installments').val();                      

            if(No.length){
                
                const url = $('input[name=url]').val()+'loan-calculator/set-loan-applied-amount';
                $.post(url,{'Applied_Amount': Applied_Amount,'LoanNo': No, 'Installments':Installments}).done(function(msg){
                    //populate empty form fields with new data
                        console.log(typeof msg);
                        console.table(msg);
                        if((typeof msg) === 'string') { // A string is an error
                            const parent = document.querySelector('.field-loancalculatorheader-principle_amount');
                            const helpbBlock = parent.children[2];
                            helpbBlock.innerText = msg;
                            disableSubmit();
                            
                        }else{ // An object represents correct details
                            const parent = document.querySelector('.field-loancalculatorheader-principle_amount');
                            const helpbBlock = parent.children[2];
                            helpbBlock.innerText = ''; 
                            $('#loancalculatorheader-key').val(msg.Key);
                            $('#loancalculatorheader-monthly_principle').val(msg.Monthly_Principle);
                            $('#loancalculatorheader-total_interest').val(msg.Total_Interest);
                            $('#loancalculatorheader-monthly_interest').val(msg.Monthly_Interest);
                            $('#loancalculatorheader-monthly_installment').val(msg.Monthly_Installment); 
                            $('#loancalculatorheader-max_installments').val(msg.Max_Installments); 
                            $('#loancalculatorheader-interest_rate').val(msg.Interest_Rate);

                            if (msg.SheduleDetails.length === 0) { 
                                return false; //We''ll deal with this later. It may never happen
                            }else{
                                $('.PaymentDetails').closest('tr').remove();
                                msg.SheduleDetails.forEach(function (item, index) {
                                    var html = '<tr>'+
                                            '<th scope="row">'+index+'</th>'+
                                            '<td>'+item.Description+'</td>'+
                                            '<td>'+item.Document_Date+'</td>'+
                                            '<td>'+ numberWithCommas(parseInt(item.Amount)) +'</td>'+
                                        '</tr>';
                                       
                                    $('.PaymentDetails').after(html);
                                });
                            }  
 
                            
                        }
                        
                    },'json');
                
            }     
        });

        $('#loancalculatorheader-repayment_installments').change(function(e){
            const Repayment_Period_M = e.target.value;
            const No = $('#loancalculatorheader-calculator_no').val();
            const maxInstallments = $('#loancalculatorheader-max_installments').val();
            const Priciple = $('#loancalculatorheader-monthly_principle').val();

            // if(Repayment_Period_M > maxInstallments){
            //     alert('You Cannot Exceed Maximum Repayment Period');
            //     return false;
            // }
            if(No.length){
                
                const url = $('input[name=url]').val()+'loan-calculator/set-loan-repayment-period';
                $.post(url,{'Repayment_Period_M': Repayment_Period_M,'LoanNo': No, 'Priciple':Priciple}).done(function(msg){
                    //populate empty form fields with new data
                        console.log(typeof msg);
                        console.table(msg);
                        if((typeof msg) === 'string') { // A string is an error
                            const parent = document.querySelector('.field-loancalculatorheader-repayment_installments');
                            const helpbBlock = parent.children[2];
                            helpbBlock.innerText = msg;
                            disableSubmit();
                            
                        }else{ // An object represents correct details
                            const parent = document.querySelector('.field-loancalculatorheader-repayment_installments');
                            const helpbBlock = parent.children[2];
                            helpbBlock.innerText = ''; 
                            $('#loancalculatorheader-key').val(msg.Key);
                            $('#loancalculatorheader-monthly_principle').val(msg.Monthly_Principle);
                            $('#loancalculatorheader-total_interest').val(msg.Total_Interest);
                            $('#loancalculatorheader-monthly_interest').val(msg.Monthly_Interest);
                            $('#loancalculatorheader-monthly_installment').val(msg.Monthly_Installment);
                            $('#loancalculatorheader-max_installments').val(msg.Max_Installments);
                            $('#loancalculatorheader-interest_rate').val(msg.Interest_Rate); 

                            if (msg.SheduleDetails.length === 0) { 
                                return false; //We''ll deal with this later. It may never happen
                            }else{
                                $('.PaymentDetails').closest('tr').remove();
                                msg.SheduleDetails.forEach(function (item, index) {
                                    var html = '<tr>'+
                                            '<th scope="row">'+index+'</th>'+
                                            '<td>'+item.Description+'</td>'+
                                            '<td>'+item.Document_Date+'</td>'+
                                            '<td>'+ numberWithCommas(parseInt(item.Amount)) +'</td>'+
                                        '</tr>';
                                       
                                    $('.PaymentDetails').after(html);
                                });
                            } 
    
                            
                            
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
