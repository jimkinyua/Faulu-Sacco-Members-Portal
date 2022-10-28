<?php

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
use borales\extensions\phoneInput\PhoneInput;

$absoluteUrl = \yii\helpers\Url::home(true);
// echo '<pre>';
// print_r($LoanData);
// exit;

?>
        
<h3 class="card-title">Loan Payment Details</h3>
<?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>
    <div class="card-body">
            <?= $form->field($model,'MemberNo')->hiddenInput()->label(false) ?>
            <div class="row">

                <div class=" row col-md-12">
                    <div class="col-md-6">
                        <?= $form->field($model, 'Loan_Product')->textInput(['readonly' => 'true', ]) ?>
                    </div>

                    <div class="col-md-6">
                        <?= $form->field($model, 'LoanAmount')->textInput(['readonly' => 'true', ]) ?>
                    </div>
                </div>

                <div class=" row col-md-12">

                <div class="col-md-4">
                        <?= $form->field($model, 'Source')->dropDownList([
                            'FOSA'=>'FOSA Account',
                            'MPESA'=>'MPESA',
                            ],['prompt' => '-- Select Source --', ]) 
                        ?>
                        <?= $form->field($model, 'PhoneNo')->widget(PhoneInput::className(), [
                            'jsOptions' => [
                                'allowExtensions' => false,
                                'onlyCountries' => ['ke'],
                        ]]) ?>

                           <?= $form->field($model, 'AccountNo')->dropDownList([
                            ],['prompt' => '-- Select Account --', ]) 
                        ?>
                        <?= $form->field($model, 'Amount')->textInput(['type'=>'number']) ?>
                                                        
                      
                           <?= Html::submitButton('Submit Payment', [
                            'class' => 'btn btn-success', 
                            'id'=>'SubmitButton',
                            'data-loading-text'=>"<i class='icon-spinner icon-spin icon-large'></i> @Localization.Uploading"
                            ]) ?>

                        <span id="number-response"></span>
                        <br>
                   
                    </div>

                    <div class="col-lg-8 col-sm-8 col-md-8" id="payment-guideline">
                            <u> <h3 align="center">Mpesa Payment Methods </h3> </u>
                            <br>
                            <div class="row">
                                <div class="col-md-12" id="mpesa-response"></div> <!--style="font: italic bold 12px/30px Georgia, serif;"-->
                                    <div class="col-md-12"><p ><strong style="color:red;">NB:</strong>There are two acceptable methods of payment i.e. the STK PUSH and Paybill. Please Feel free to use either of the two. Guidelines for each are provided below.</p></div>
                                    <br>
                                        <div class="col-md-6">
                                            <div class="panel panel-default">
                                            <div class="panel-body">
                                                    <div class="form-group">
                                                        <h4>STK-Push Payment Mpesa Guidelines</h4>
                                                        <!--<input type="hidden" name="_csrf-licensing" value="amYSgaq5r6uAlq7NmyEj4YWjfFkmeHh-1LN96TvuXeMHM2rq-d752MnenL_jGGDV6fQwPU8nJwis7AuPeIxv1Q==">--> 
                                                        <ul>
                                                            <li><strong>Unlock your phone</strong> and ensure it's on</li>
                                                            <li>Enter the amount you want to deposit (NO COMMAS) </li>
                                                            <li>Enter the phone number you want to use <i><strong>e.g 07xxxxxxxx</strong></i></li>
                                                            <li>Send payment request to the entered phone number by clicking the button below</li>
                                                            <li style="color:red;">If a request is <strong>not sent</strong> to your phone, and it brings an error <strong>'Operation cancelled 09'</strong>, please use the other method.</li>
                                                            <li>Enter your <strong>Mpesa Pin</strong> and press okay</li>
                                                            <li>You will receive an SMS confirming the transaction</li> 
                                                            <!-- <li>Click Save button below after receiving the confirmation sms</li>  -->
                                                        </ul> 

                                                    </div>
                                            </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="panel panel-default">
                                        <div class="panel-body" style="height: 46rem;">
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="panel panel-default">
                                                        <div class="panel-body">
                                                        <h4>M-PESA Paybill Payment Guidelines</h4>
                                                            <ul>
                                                                <li>Go to&nbsp;<strong>M-PESA</strong>&nbsp;Menu on your mobile phone</li>
                                                                <li>Select&nbsp;<strong>Pay Bill</strong></li>
                                                                <li>Enter 540700 <strong>&nbsp;</strong>as the Business Number</li>
                                                                <li>Enter <strong> <?= $model->RefrenceNo ?></strong>&nbsp;as&nbsp;<strong>ACCOUNT NUMBER</strong>&nbsp;option</li>
                                                                <li>Enter the amount you want to pay (NO COMMAS) </li>
                                                                <li>Enter your&nbsp;<strong>M-PESA PIN</strong></li>
                                                                <!-- <li>Then Click on Submit Payment </li> -->
                                                                <li>You will receive an SMS confirming the transaction</li>
                                                                <li>Once your Loan Repayment is Processed, you will receive a confrimation message from us.</li>

                                                                <!-- <li>Click <strong>Save&nbsp;</strong>button below after receiving the confirmation sms</li> -->
                                                            </ul>
                                                        </div>
                                                    </div>  
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                    </div>

                
                    
                </div>
            </div>
            <!-- <div class="row">
                <div class="form-group">
                    <?= Html::submitButton('Submit Payment', [
                    'class' => 'btn btn-success', 
                    'id'=>'SubmitButton',
                    'data-loading-text'=>"<i class='icon-spinner icon-spin icon-large'></i> @Localization.Uploading"
                    ]) ?>
                </div>
            </div>                        -->
    </div>        
<?php ActiveForm::end(); ?>
  
<input type="hidden" name="url" value="<?= $absoluteUrl ?>">

<?php

    $script = <<<JS
    $(function(){
        $('#payment-guideline').hide()
           
        $('form').on('submit',function(e){
            console.log('clicking...');
            // document.getElementById('SubmitButton').setAttribute("disabled", "true");    
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


        $('#portalloanrepayment-source').change(function(e){
            const Source = e.target.value;
            var url = $('input[name=url]').val()+'loan-repayment/get-member-accounts';
            if(Source == 'FOSA'){
                $('#payment-guideline').hide()
                $('.field-portalloanrepayment-phoneno').hide();
                $('.field-portalloanrepayment-accountno').show();
                $('#portalloanrepayment-phoneno').val('N/A');
                $('#portalloanrepayment-phoneno').prop('disabled', true); 
                $.get(url, function(response) {
                $('#portalloanrepayment-accountno').empty();
                $('#portalloanrepayment-accountno').append($('<option id="itemId"></option>').attr('value', '').text('-- Select Account --')); //append Here
                    $.each(response, function (key, entry) {
                        $('#portalloanrepayment-accountno').append($('<option id="itemId'+ entry.No+'"></option>').attr('value', entry.No).text(entry.Name)); //append Here
                    })
                });
            } else{
                $('#payment-guideline').show();
                $('.field-portalloanrepayment-phoneno').show();

                $('#portalloanrepayment-accountno').empty();
                
                $('.field-portalloanrepayment-accountno').hide();


                $('#portalloanrepayment-phoneno').val('');
                $('#portalloanrepayment-phoneno').prop('disabled', false);
                $('#portalloanrepayment-accountno').empty();
                $('#portalloanrepayment-accountno').prop('disbaled', true);
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
                            const parent = document.querySelector('.form-group field-loanapplicationheader-applied_amount');
                            const helpbBlock = parent.children[2];
                            helpbBlock.innerText = msg;
                            disableSubmit();
                            
                        }else{ // An object represents correct details
                            // const parent = document.querySelector('.form-group field-loanapplicationheader-applied_amount');
                            // const helpbBlock = parent.children[2];
                            // helpbBlock.innerText = ''; 
                            $('#loanapplicationheader-key').val(msg.Key);
                            $('#loanapplicationheader-total_principle_repayment').val(msg.Total_Principle_Repayment);
                            $('#loanapplicationheader-total_interest_repayment').val(msg.Total_Interest_Repayment);
                            $('#loanapplicationheader-total_loan_repayment').val(msg.Total_Loan_Repayment);
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
                            const parent = document.querySelector('.form-group field-loanapplicationheader-applied_amount');
                            const helpbBlock = parent.children[2];
                            helpbBlock.innerText = msg;
                            disableSubmit();
                            
                        }else{ // An object represents correct details
                            // const parent = document.querySelector('.form-group field-loanapplicationheader-applied_amount');
                            // const helpbBlock = parent.children[2];
                            // helpbBlock.innerText = ''; 
                            $('#loanapplicationheader-key').val(msg.Key);
                            $('#loanapplicationheader-total_principle_repayment').val(msg.Total_Principle_Repayment);
                            $('#loanapplicationheader-total_interest_repayment').val(msg.Total_Interest_Repayment);
                            $('#loanapplicationheader-total_loan_repayment').val(msg.Total_Loan_Repayment);
                            // enableSubmit();
                            
                        }
                        
                    },'json');
                
            }     
        });
        

    });
        
JS;

$this->registerJs($script);
