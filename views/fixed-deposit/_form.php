<?php

use yii\bootstrap4\Html;
use yii\bootstrap4\ActiveForm;
use yii\helpers\ArrayHelper;


$this->title = 'Fixed Deposit';
$absoluteUrl = \yii\helpers\Url::home(true);

?>

<h3 class="card-title">Fixed Deposit Details</h3>
<?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data', 'id' => 'FixedDepositForm']]); ?>
<div class="card-body">
    <?= $form->field($model, 'Key')->hiddenInput()->label(false) ?>
    <?= $form->field($model, 'FD_No')->hiddenInput()->label(false) ?>



    <div class="row">
        <div class=" row col-md-12">



            <div class="col-md-4">
                <?= $form->field($model, 'FD_Type')->dropDownList(ArrayHelper::map($FDTypes, 'Code', 'Name'), ['prompt' => '--Select Fixed Deposit Product--',]) ?>
                <?= $form->field($model, 'Amount')->textInput() ?>

            </div>

            <div class="col-md-4">
                <?= $form->field($model, 'Member_Name')->textInput(['readonly' => true]) ?>
                <!-- <?= $form->field($model, 'Start_Date')->textInput(['type' => 'date', 'minDate' => 0, 'min' => 'current_date']) ?> -->
            </div>

            <div class="col-md-4">
                <?= $form->field($model, 'Period')->dropDownList([
                    '3' => '3 Months',
                    '6' => '6 Months',
                    '12' => '12 Months'
                ], ['prompt' => '--Select Period--',]) ?>

                <?= $form->field($model, 'Marturity_Instructions')->dropDownList([
                    'Liquidate' => 'Liquidate',
                    'Roll_Over_Net' => 'Roll The Over The Net Amount',
                    'Roll_Over_Principle' => 'Roll Over The Principle',

                ], ['prompt' => '--Select Option--']) ?>
            </div>

        </div>
    </div>
    <h2> Source of Funds</h2>
    <div class="row">
        <div class=" row col-md-12">

            <div class="col-md-6">
                <?= $form->field($model, 'Source_Account')->dropDownList(ArrayHelper::map($Accounts, 'Code', 'Name'), ['prompt' => '--Select Option--']) ?>
            </div>
            <div class="col-md-6">
                <?= $form->field($model, 'Source_Balance')->textInput(['readonly' => true]) ?>
            </div>

        </div>

    </div>

    <div class="row">
        <div class="form-group">
            <?= Html::submitButton('Submit ', ['class' => 'btn btn-success', 'id' => 'SubmitButton']) ?>
        </div>

    </div>

</div>

<?php ActiveForm::end(); ?>
<input type="hidden" name="url" value="<?= $absoluteUrl ?>">

<?php

$script = <<<JS
                    $(function(){

                        $('#FixedDepositForm').on('beforeSubmit', function () {
                  
                        var yiiform = $(this);
                        $.ajax({
                                type: yiiform.attr('method'),
                                url: yiiform.attr('action'),
                                data: yiiform.serializeArray(),
                            }
                        )
                            .done(function(data) {
                                if(data.success) {
                                    // data is saved
                                    $('.ErrorPage').text('');
                                    $('.ErrorPage').hide();
                                } else if (data.validation) {
                                    // server validation failed
                                    yiiform.yiiActiveForm('updateMessages', data.validation, true); // renders validation messages at appropriate places
                                  
                                }
                                
                                else if (data.error) {
                                    // server validation failed
                                    alert(data.error);                         

                                }

                                else {
                                    // incorrect server response
                                }
                            })
                            .fail(function () {
                                // request failed
                            })

                        return false; // prevent default form submission
                    })


                    var typeVal = $('#fixeddepositcard-funds_source').val();
   
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


                          $('#fixeddepositcard-source_account').change(function(e){
                            const SelectedAccount = e.target.value;
                            const No = $('#fixeddepositcard-fd_no').val();
                            if(SelectedAccount.length){
                                const url = $('input[name=url]').val()+'fixed-deposit/set-account';
                                $.post(url,{'SelectedAccount': SelectedAccount,'DocNum': No}).done(function(msg){
                                    //populate empty form fields with new data
                                        if((typeof msg) === 'string') { // A string is an error
                                            const parent = document.querySelector('.field-fixeddepositcard-source_account');
                                            const helpbBlock = parent.children[2];
                                            helpbBlock.innerText = msg;
                                            disableSubmit();
                                        }else{ // An object represents correct details
                                            const parent = document.querySelector('.field-fixeddepositcard-source_account');
                                            const helpbBlock = parent.children[2];
                                            helpbBlock.innerText = '';
                                            $('#fixeddepositcard-key').val(msg.Key);
                                            $('#fixeddepositcard-source_balance').val(msg.Source_Balance);
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
