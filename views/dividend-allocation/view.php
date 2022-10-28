<?php

use yii\bootstrap4\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use borales\extensions\phoneInput\PhoneInput;
use kartik\select2\Select2;


$absoluteUrl = \yii\helpers\Url::home(true);
$this->title = 'Dividend Allocation Details'

?>
        
<h3 class="card-title"><?= $this->title ?></h3>
<?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>
    <div class="card-body">
            <?= $form->field($model,'Member_No')->hiddenInput()->label(false) ?>
            <div class="row">


                <div class=" row col-md-12">
                        <div class="col-md-6">
                        <?= $form->field($model, 'Net_Amount')->textInput(['readonly' => 'true', 'value'=>number_format($model->Net_Amount)]) ?>

                                <?= $form->field($model, 'Allocation_Type')->dropDownList([
                                    'Deposits'=>'Add to My Deposits',
                                    'Share_Capital'=>'Top Up My Share Capital',
                                    'Loan_Payment'=>'Pay Loan',
                                    'MPESA_Payment'=>'Send To Mpesa',
                                    'Bank_Payment'=>'Send To Bank',
                                    ],['prompt' => '-- Select Option --','disabled'=>true ]) 
                                ?>

                                <?= $form->field($model, 'MPESA_No')->textInput(['readonly'=>true, 'disabled'=>true])  ?>


                                <?= $form->field($model, 'Allocation_Account')->widget(Select2::classname(), [
                                    'data' => [],
                                    'options' => ['placeholder' => $model->Allocation_Account, 'disabled'=>true],
                                    'pluginOptions' => [
                                        'allowClear' => true
                                    ],
                                    ]);
                                ?>

                                                                
                        

                                <span id="number-response"></span>
                                <br>
                        
                        </div>
                        <div class="col-md-6" id="payment-guideline">

                                <?= $form->field($model, 'Bank_Code')->widget(Select2::classname(), [
                                    'data' => [],
                                    'options' => ['placeholder' => $model->Bank_Name, 'disabled'=>true],
                                    'pluginOptions' => [
                                        'allowClear' => true
                                    ],
                                    ]);
                                ?>

                                 <?= $form->field($model, 'Branch_Code')->widget(Select2::classname(), [
                                    'data' => [],
                                    'options' => ['placeholder' =>$model->Branch_Name, 'disabled'=>true],
                                    'pluginOptions' => [
                                        'allowClear' => true
                                    ],
                                    ]);
                                ?>


                               <?= $form->field($model, 'Account_No')->textInput(['readonly'=>true, 'disabled'=>true]) 
                                ?>



                                <span id="number-response"></span>
                                <br>
                        
                        </div>                    
                </div>

            </div>
            <?= \yii\helpers\Html::a('Go Back',Url::to(['index']),['class' => 'create btn btn-info btn-md mr-2 ']) ?>



    </div>        
<?php ActiveForm::end(); ?>
  
<input type="hidden" name="url" value="<?= $absoluteUrl ?>">

<?php

    $script = <<<JS
    $(function(){
        $('#payment-guideline').hide();

            
            const Source = $('#dividendallocationheader-allocation_type').val()
            var url = $('input[name=url]').val()+'loan-repayment/get-member-accounts';
            var LoansUrl = $('input[name=url]').val()+'dividend-allocation/get-member-loans';
            var BanksUrl = $('input[name=url]').val()+'dividend-allocation/get-banks';


            if(Source == 'MPESA_Payment' || Source == 'Bank_Payment'){
                $('.field-dividendallocationheader-allocation_account').hide();
                if(Source != 'MPESA_Payment'){ //Bank
                    $('.field-dividendallocationheader-mpesa_no').hide();
                    $('#payment-guideline').show();
                    $('#dividendallocationheader-bank_code').empty();
                    $('#dividendallocationheader-branch_code').empty();

                    $.get(BanksUrl, function(response) {
                    $('#dividendallocationheader-bank_code').empty();

                    $('#dividendallocationheader-bank_code').append($('<option id="itemId"></option>').attr('value', '').text('--  --')); //append Here
                        $.each(response, function (key, entry) {
                            $('#dividendallocationheader-bank_code').append($('<option id="itemId'+ entry.Code+'"></option>').attr('value', entry.Code).text(entry.Name)); //append Here
                        })
                    });
                }else{
                    $('.field-dividendallocationheader-mpesa_no').show();
                    $('#payment-guideline').hide()
                }

            }else{
                $('.field-dividendallocationheader-mpesa_no').hide();
                $('.field-dividendallocationheader-allocation_account').hide();
                $('#payment-guideline').hide();

                if(Source == 'Loan_Payment'){
                    $('.field-dividendallocationheader-allocation_account').show();
                    $.get(LoansUrl, function(response) {
                    $('#dividendallocationheader-allocation_account').empty();

                    $('#dividendallocationheader-allocation_account').append($('<option id="itemId"></option>').attr('value', '').text('--  --')); //append Here
                        $.each(response, function (key, entry) {
                            $('#dividendallocationheader-allocation_account').append($('<option id="itemId'+ entry.Code+'"></option>').attr('value', entry.Product_Description).text(entry.Name)); //append Here
                        })
                    });

                return false;
                }else{
                    $('.field-dividendallocationheader-allocation_account').hide();
                }

                // $('.field-dividendallocationheader-allocation_account').hide();
                
            }    
     

           
  

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


        $('#dividendallocationheader-allocation_type').change(function(e){
            const Source = e.target.value;
            var url = $('input[name=url]').val()+'loan-repayment/get-member-accounts';
            var LoansUrl = $('input[name=url]').val()+'dividend-allocation/get-member-loans';
            var BanksUrl = $('input[name=url]').val()+'dividend-allocation/get-banks';


            if(Source == 'MPESA_Payment' || Source == 'Bank_Payment'){
                $('.field-dividendallocationheader-allocation_account').hide();
                if(Source != 'MPESA_Payment'){ //Bank
                    $('.field-dividendallocationheader-mpesa_no').hide();
                    $('#payment-guideline').show();
                    $('#dividendallocationheader-bank_code').empty();
                    $('#dividendallocationheader-branch_code').empty();

                    $.get(BanksUrl, function(response) {
                    $('#dividendallocationheader-bank_code').empty();

                    $('#dividendallocationheader-bank_code').append($('<option id="itemId"></option>').attr('value', '').text('--  --')); //append Here
                        $.each(response, function (key, entry) {
                            $('#dividendallocationheader-bank_code').append($('<option id="itemId'+ entry.Code+'"></option>').attr('value', entry.Code).text(entry.Name)); //append Here
                        })
                    });

                    

                }else{
                    $('.field-dividendallocationheader-mpesa_no').show();
                    $('#payment-guideline').hide()


                    

                }

            }else{
                $('.field-dividendallocationheader-mpesa_no').hide();
                $('.field-dividendallocationheader-allocation_account').hide();
                $('#payment-guideline').hide();

                if(Source == 'Loan_Payment'){
                    $('.field-dividendallocationheader-allocation_account').show();
                    $.get(LoansUrl, function(response) {
                    $('#dividendallocationheader-allocation_account').empty();

                    $('#dividendallocationheader-allocation_account').append($('<option id="itemId"></option>').attr('value', '').text('-- Select Loan --')); //append Here
                        $.each(response, function (key, entry) {
                            $('#dividendallocationheader-allocation_account').append($('<option id="itemId'+ entry.Code+'"></option>').attr('value', entry.Product_Description).text(entry.Name)); //append Here
                        })
                    });

                return false;
                }else{
                    $('.field-dividendallocationheader-allocation_account').hide();
                }

                // $('.field-dividendallocationheader-allocation_account').hide();
                
            }    
        });

        $('#dividendallocationheader-bank_code').change(function(e){
            const SelectedBank = e.target.value;
            if(SelectedBank.length){ 
                var BankBranchesUrl = $('input[name=url]').val()+'dividend-allocation/get-bank-branches?Bank_Code='+SelectedBank;
                $.get(BankBranchesUrl, function(response) {
                    $('#dividendallocationheader-branch_code').empty();
                    $('#dividendallocationheader-branch_code').append($('<option id="itemId"></option>').attr('value', '').text('-- Select Branch of The Bank --')); //append Here
                        $.each(response, function (key, entry) {
                            $('#dividendallocationheader-branch_code').append($('<option id="itemId'+ entry.Code+'"></option>').attr('value', entry.Code).text(entry.Name)); //append Here
                        })
                    });
            }     
        });

  
        

    });
        
JS;

$this->registerJs($script);
