<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
$this->title = 'Cheque Book Request Details';
$absoluteUrl = \yii\helpers\Url::home(true);

?>
        
            <h3 class="card-title">Cheque Book Request Details</h3>
            <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>
                <div class="card-body">
                    <?= $form->field($model,'Key')->hiddenInput()->label(false) ?>
                    

                    <div class="row">
                        <div class=" row col-md-12">
                         
                                    <div class="col-md-6">
                                        <?= $form->field($model, 'Cheque_Book_Type')->dropDownList(ArrayHelper::map($ChequeBookTypes, 'Code','Name'),['prompt' => '--Select Option--']) ?>

                                    </div>

                                    <div class="col-md-6">
                                        <?= $form->field($model, 'No_of_Leafs')->textInput() ?>
                                    </div>


                        </div>
                    </div>
                    

                        <div class="row">
                            <div class="form-group">
                                <?= Html::submitButton('Submit ', ['class' => 'btn btn-success', 'id'=>'SubmitButton']) ?>
                            </div>

                        </div>

                </div>

            <?php ActiveForm::end(); ?>
            <input type="hidden" name="url" value="<?= $absoluteUrl ?>">

            <?php

                $script = <<<JS
                    $(function(){

                    var typeVal = $('#fixeddepositcard-source_type').val();
                    $('.field-fixeddepositcard-account_no').show();
                    $('.field-fixeddepositcard-source_of_funds').show()

                    if(typeVal == 'Bank'){
                        $('#fixeddepositcard-account_no').replaceWith('<select id="fixeddepositcard-account_no" class="form-control" name="FixedDepositCard[Account_No]" aria-required="true"></select>');
                        var url = '/fixed-deposit/get-banks';
                        
                        $('label[for="fixeddepositcard-account_no"]').text('Select Bank');

                        $.get(url, function(response) {
                            $('#fixeddepositcard-account_no').empty();
                            $('#fixeddepositcard-account_no').append($('<option id="itemId"></option>').attr('value', '').text('-- Select Option --')); //append Here
                        $.each(response, function (key, entry) {
                            $('#fixeddepositcard-account_no').append($('<option id="itemId'+ entry.Code+'"></option>').attr('value', entry.Code).text(entry.Name)); //append Here
                        })});

                    }else{
                        var url = '/fixed-deposit/get-vendors';
                        $('.field-fixeddepositcard-account_no').hide();
                        // $('.field-fixeddepositcard-source_of_funds').hide()
                        $('#fixeddepositcard-account_no').replaceWith('<input type="text" id="fixeddepositcard-account_no" class="form-control" name="FixedDepositCard[Account_No]" aria-required="true">');
                        $('label[for="fixeddepositcard-account_no"]').text('Provide Bank Receipt No');

                        // $('.field-fixeddepositcard-account_no').hide();                
                    }  



                        $('#fixeddepositcard-fd_type').change(function(e){
                            const FixedProduct = e.target.value;
                            const No = $('#fixeddepositcard-fd_no').val();
                            if(FixedProduct.length){
                                const url = $('input[name=url]').val()+'fixed-deposit/set-fixed-product';
                                $.post(url,{'Product': FixedProduct,'DocNum': No}).done(function(msg){
                                    //populate empty form fields with new data
                                        if((typeof msg) === 'string') { // A string is an error
                                            const parent = document.querySelector('.field-fixeddepositcard-fd_type');
                                            const helpbBlock = parent.children[2];
                                            helpbBlock.innerText = msg;
                                            disableSubmit();
                                        }else{ // An object represents correct details
                                            const parent = document.querySelector('.field-fixeddepositcard-fd_type');
                                            const helpbBlock = parent.children[2];
                                            helpbBlock.innerText = '';
                                            $('#fixeddepositcard-key').val(msg.Key);
                                            $('#fixeddepositcard-interest_rate').val(msg.Interest_Rate);
                                            // $('#fixeddepositcard-fixed_period_m').val(msg.Total_Interest_Repayment);
                                            $('#fixeddepositcard-fixed_amount').val(msg.Total_Loan_Repayment);
                                            $('#fixeddepositcard-expected_interest').val(msg.Expected_Interest);
                                            $('#fixeddepositcard-charges').val(msg.Charges);
                                            $('#fixeddepositcard-expected_interest_net').val(msg.Expected_Interest_Net);
                                            // enableSubmit();
                                            
                                        }
                                        
                                    },'json');
                                
                            }     
                        });

                        $('#fixeddepositcard-fixed_period_m').change(function(e){
                            const Months = e.target.value;
                            const No = $('#fixeddepositcard-fd_no').val();
                            if(Months.length){
                                const url = $('input[name=url]').val()+'fixed-deposit/set-fixed-period';
                                $.post(url,{'Months': Months,'DocNum': No}).done(function(msg){
                                    //populate empty form fields with new data
                                        if((typeof msg) === 'string') { // A string is an error
                                            const parent = document.querySelector('.field-fixeddepositcard-fd_type');
                                            const helpbBlock = parent.children[2];
                                            helpbBlock.innerText = msg;
                                            disableSubmit();
                                        }else{ // An object represents correct details
                                            const parent = document.querySelector('.field-fixeddepositcard-fixed_period_m');
                                            const helpbBlock = parent.children[2];
                                            helpbBlock.innerText = '';
                                            $('#fixeddepositcard-key').val(msg.Key);
                                            $('#fixeddepositcard-interest_rate').val(msg.Interest_Rate);
                                            // $('#fixeddepositcard-fixed_period_m').val(msg.Total_Interest_Repayment);
                                            // $('#fixeddepositcard-fixed_amount').val(msg.Total_Loan_Repayment);
                                            $('#fixeddepositcard-expected_interest').val(msg.Expected_Interest);
                                            $('#fixeddepositcard-charges').val(msg.Charges);
                                            $('#fixeddepositcard-expected_interest_net').val(msg.Expected_Interest_Net);
                                            // enableSubmit();
                                            
                                        }
                                        
                                    },'json');
                                
                            }     
                        });

                        $('#fixeddepositcard-fixed_amount').change(function(e){
                            const AmountToFix = e.target.value;
                            const No = $('#fixeddepositcard-fd_no').val();
                            if(AmountToFix.length){
                                const url = $('input[name=url]').val()+'fixed-deposit/set-fixed-amount';
                                $.post(url,{'AmountToFix': AmountToFix,'DocNum': No}).done(function(msg){
                                    //populate empty form fields with new data
                                        if((typeof msg) === 'string') { // A string is an error
                                            const parent = document.querySelector('.field-fixeddepositcard-fixed_amount');
                                            const helpbBlock = parent.children[2];
                                            helpbBlock.innerText = msg;
                                            disableSubmit();
                                        }else{ // An object represents correct details
                                            const parent = document.querySelector('.field-fixeddepositcard-fixed_amount');
                                            const helpbBlock = parent.children[2];
                                            helpbBlock.innerText = '';
                                            $('#fixeddepositcard-key').val(msg.Key);
                                            $('#fixeddepositcard-interest_rate').val(msg.Interest_Rate);
                                            // $('#fixeddepositcard-fixed_period_m').val(msg.Total_Interest_Repayment);
                                            // $('#fixeddepositcard-fixed_amount').val(msg.Total_Loan_Repayment);
                                            $('#fixeddepositcard-expected_interest').val(msg.Expected_Interest);
                                            $('#fixeddepositcard-charges').val(msg.Charges);
                                            $('#fixeddepositcard-expected_interest_net').val(msg.Expected_Interest_Net);
                                            // enableSubmit();
                                            
                                        }
                                        
                                    },'json');
                                
                            }     
                        });

                        
                        $('#fixeddepositcard-maturity_action').change(function(e){
                            const MaturityAction = e.target.value;
                            const No = $('#fixeddepositcard-fd_no').val();
                            if(MaturityAction.length){
                                const url = $('input[name=url]').val()+'fixed-deposit/set-maturity-action';
                                $.post(url,{'MaturityAction': MaturityAction,'DocNum': No}).done(function(msg){
                                    //populate empty form fields with new data
                                        if((typeof msg) === 'string') { // A string is an error
                                            const parent = document.querySelector('.field-fixeddepositcard-maturity_action');
                                            const helpbBlock = parent.children[2];
                                            helpbBlock.innerText = msg;
                                            disableSubmit();
                                        }else{ // An object represents correct details
                                            const parent = document.querySelector('.field-fixeddepositcard-maturity_action');
                                            const helpbBlock = parent.children[2];
                                            helpbBlock.innerText = '';
                                            $('#fixeddepositcard-key').val(msg.Key);
                                            $('#fixeddepositcard-interest_rate').val(msg.Interest_Rate);
                                            $('#fixeddepositcard-expected_interest').val(msg.Expected_Interest);
                                            $('#fixeddepositcard-charges').val(msg.Charges);
                                            $('#fixeddepositcard-expected_interest_net').val(msg.Expected_Interest_Net);
                                            // enableSubmit();
                                            
                                        }
                                        
                                    },'json');
                                
                            }     
                        });

                        
                        $('#fixeddepositcard-fixed_date').change(function(e){
                            const FixedDate = e.target.value;
                            const No = $('#fixeddepositcard-fd_no').val();
                            if(FixedDate.length){
                                const url = $('input[name=url]').val()+'fixed-deposit/set-fixed-date';
                                $.post(url,{'FixedDate': FixedDate,'DocNum': No}).done(function(msg){
                                    //populate empty form fields with new data
                                        if((typeof msg) === 'string') { // A string is an error
                                            const parent = document.querySelector('.field-fixeddepositcard-maturity_action');
                                            const helpbBlock = parent.children[2];
                                            helpbBlock.innerText = msg;
                                            disableSubmit();
                                        }else{ // An object represents correct details
                                            const parent = document.querySelector('.field-fixeddepositcard-maturity_action');
                                            const helpbBlock = parent.children[2];
                                            helpbBlock.innerText = '';
                                            $('#fixeddepositcard-key').val(msg.Key);
                                            $('#fixeddepositcard-interest_rate').val(msg.Interest_Rate);
                                            $('#fixeddepositcard-expected_interest').val(msg.Expected_Interest);
                                            $('#fixeddepositcard-charges').val(msg.Charges);
                                            $('#fixeddepositcard-expected_interest_net').val(msg.Expected_Interest_Net);
                                            // enableSubmit();
                                            
                                        }
                                        
                                    },'json');
                                
                            }     
                        });

                        $('#fixeddepositcard-source_type').change(function(e){
                            const Type = e.target.value;
                            $('.field-fixeddepositcard-account_no').show();
                            $('.field-fixeddepositcard-source_of_funds').show()

                            if(Type == 'Bank'){
                                $('#fixeddepositcard-account_no').replaceWith('<select id="fixeddepositcard-account_no" class="form-control" name="FixedDepositCard[Account_No]" aria-required="true"></select>');
                                var url = '/fixed-deposit/get-banks';
                                
                                $('label[for="fixeddepositcard-account_no"]').text('Select Bank');

                                $.get(url, function(response) {
                                    $('#fixeddepositcard-account_no').empty();
                                    $('#fixeddepositcard-account_no').append($('<option id="itemId"></option>').attr('value', '').text('-- Select Option --')); //append Here
                                $.each(response, function (key, entry) {
                                    $('#fixeddepositcard-account_no').append($('<option id="itemId'+ entry.Code+'"></option>').attr('value', entry.Code).text(entry.Name)); //append Here
                                })});

                            }else{
                                var url = '/fixed-deposit/get-vendors';
                                $('.field-fixeddepositcard-account_no').hide();
                                $('.field-fixeddepositcard-source_of_funds').hide()
                                $('#fixeddepositcard-account_no').replaceWith('<input type="text" id="fixeddepositcard-account_no" class="form-control" name="FixedDepositCard[Account_No]" aria-required="true">');
                                $('label[for="fixeddepositcard-account_no"]').text('Provide Bank Receipt No');

                                // $('.field-fixeddepositcard-account_no').hide();                
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

