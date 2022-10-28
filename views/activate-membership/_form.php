<?php

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
$absoluteUrl = \yii\helpers\Url::home(true);

?>
        
                    <h3 class="card-title">Withdrawal Details</h3>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="card">
                                        <div class="card-body">
                                            <?php if($model->Approval_Status == 'New'): ?>
                                                <?= \yii\helpers\Html::a('Submit',['send-for-approval','No'=> $model->Application_No],['class' => 'btn btn-success btn-md mr-2 ']) ?>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
              
                    <div class="card-body">
                        <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>
                        <?= $form->field($model,'Member_No')->hiddenInput(['value'=>Yii::$app->user->identity->{'No_'}])->label(false) ?>
                        <?= $form->field($model,'Document_No')->hiddenInput()->label(false) ?>
                        <?= $form->field($model,'Key')->hiddenInput()->label(false) ?>

                        <div class="row">
                            <div class=" row col-md-12">

                                <div class="col-md-6">
                                <?= $form->field($model, 'Withdrawal_Reason')->dropDownList([
                                        '0'=>'General_Withdrawal',
                                        '1'=>'Desceased Member',
                                        ],['prompt' => 'Select Withdrawal Reason']) 
                                    ?>
                                  
                                </div>

                                <div class="col-md-6">
                                    <?= $form->field($model, 'Balance_Option')->dropDownList([
                                        '1'=>'Cash_Withdrawal',
                                        ],['prompt' => 'Select Balance Option']) 
                                    ?>
                                </div>

                            </div>
                            <div class=" row col-md-12">
                                <div class="col-md-6">
                                    <?= $form->field($model, 'Closing_Reason')->textarea(['rows'=>4, 'cols'=>6]) ?>
                                    <?= $form->field($model, 'Speed_Process')->dropDownList([
                                            '0'=>'No',
                                            '1'=>'Yes',
                                            ],['prompt' => '-- Select Option --']) 
                                        ?>
                                </div>

                                
                            </div>

                        </div>
                            <div class="row">
                                <div class="form-group">
                                    <?= Html::submitButton('Submit Withdraw Request', ['class' => 'btn btn-success']) ?>
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
                            const parent = document.querySelector('.form-group field-loanapplicationheader-loan_product');
                            const helpbBlock = parent.children[2];
                            helpbBlock.innerText = msg;
                            disableSubmit();
                            
                        }else{ // An object represents correct details
                            const parent = document.querySelector('.form-group field-loanapplicationheader-loan_product');
                            const helpbBlock = parent.children[2];
                            helpbBlock.innerText = ''; 
                            enableSubmit();
                            
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
