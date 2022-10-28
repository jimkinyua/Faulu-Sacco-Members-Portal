<?php

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
$absoluteUrl = \yii\helpers\Url::home(true);

?>
              <!--THE STEPS THING--->
            <div class="row">
                <div class="col-md-12">
                    <?= $this->render('.\LoanSteps', ['model'=>$model]) ?>
                </div>
                <div class="row">
                                <div class="col-md-12">
                                    <?= \yii\helpers\Html::a('Payment Schedule',Url::to(['payment-schedule', 'Key'=>$model->Key]),['class' => 'btn btn-info btn-md mr-2 ']) ?>
                                </div>
                            </div>
            </div>

    <!--END THE STEPS THING--->
                    <h3 class="card-title"></h3>
                    
                
                      

                    <div class="card-body">
                        
                        <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>
                        <?= $form->field($model,'Member_No')->hiddenInput()->label(false) ?>
                        <?= $form->field($model,'Application_No')->hiddenInput()->label(false) ?>
                        <?= $form->field($model,'Key')->hiddenInput()->label(false) ?>

                        

                        <div class="row">
                            <div class=" row col-md-12">
                                <?php if($model->Portal_Status=='Application'): ?>

                                    <div class="col-md-3">
                                    <?= $form->field($model, 'Loan_Product')->dropDownList(Arrayhelper::map($loanProducts, 'Code', 'Name'),['prompt' => 'Select Loan Type']) ?>

                                        <?= $form->field($model, 'Application_Category')->dropDownList([
                                            'Salary'=>'Salary',
                                            'Business'=>'Business',
                                            'salary_and_Business'=>'salary_and_Business',
                                            ],['prompt' => '-- Select Option --'])?>

                                        <?= $form->field($model, 'Total_Principle_Repayment')->textInput(['type' => 'text', 'readonly'=>true, 'value' => number_format($model->Total_Principle_Repayment)]) ?>
                                    </div>

                                    <div class="col-md-3">
                                        <?= $form->field($model, 'Applied_Amount')->textInput(['type' => 'text','value' => number_format($model->Applied_Amount)]) ?>
                                        <?= $form->field($model, 'Total_Interest_Repayment')->textInput(['type' => 'text', 'readonly'=>true, 'value' => number_format($model->Total_Interest_Repayment)]) ?>
                                    </div>

                                    <div class="col-md-3">
                                        <?= $form->field($model, 'Repayment_Period_M')->textInput(['type' => 'text', 'value' => number_format($model->Repayment_Period_M)]) ?>
                                        <?= $form->field($model, 'Total_Loan_Repayment')->textInput(['type' => 'text', 'readonly'=>true, 'value' => number_format($model->Total_Loan_Repayment)]) ?>
                                    </div>
                                    <div class="col-md-3">
                                        <?= $form->field($model, 'Recovery_Mode')->dropDownList([
                                            'Checkoff'=>'Checkoff',
                                            'Standing_Order'=>'Standing_Order',
                                            'EFT'=>'EFT',
                                            'RTGS'=>'RTGS',
                                            'Cash'=>'Cash',
                                            'Cheque'=>'Cheque',
                                            ],['prompt' => 'Select Loan Type']) 
                                        ?>
                                         <?= $form->field($model, 'Monthly_Installment')->textInput(['type' => 'text', 'readonly'=>true, 'value' => number_format($model->Monthly_Installment)]) ?>

                                    
                                    </div>
                                <?php else: ?>
                                    <div class="col-md-3">
                                        <?= $form->field($model, 'Loan_Product')->dropDownList(Arrayhelper::map($loanProducts, 'Code', 'Name'),['prompt' => 'Select Loan Type', 'disabled'=>true]) ?>
                                        <?= $form->field($model, 'Total_Principle_Repayment')->textInput(['type' => 'text', 'readonly'=>true, 'value' => number_format($model->Total_Principle_Repayment)]) ?>
                                    </div>

                                    <div class="col-md-3">
                                        <?= $form->field($model, 'Applied_Amount')->textInput(['type' => 'text','value' => number_format($model->Applied_Amount)]) ?>
                                        <?= $form->field($model, 'Total_Interest_Repayment')->textInput(['type' => 'text', 'readonly'=>true, 'value' => number_format($model->Total_Interest_Repayment)]) ?>
                                    </div>

                                    <div class="col-md-3">
                                        <?= $form->field($model, 'Repayment_Period_M')->textInput(['type' => 'text', 'value' => number_format($model->Repayment_Period_M)]) ?>
                                        <?= $form->field($model, 'Total_Loan_Repayment')->textInput(['type' => 'text', 'readonly'=>true, 'value' => number_format($model->Total_Loan_Repayment)]) ?>
                                    </div>
                                    <div class="col-md-3">
                                        <?= $form->field($model, 'Recovery_Mode')->dropDownList([
                                            'Checkoff'=>'Checkoff',
                                            'Standing_Order'=>'Standing_Order',
                                            'EFT'=>'EFT',
                                            'RTGS'=>'RTGS',
                                            'Cash'=>'Cash',
                                            'Cheque'=>'Cheque',
                                            ],['prompt' => 'Select Loan Type', 'disabled'=>true]) 
                                        ?>
                                         <?= $form->field($model, 'Monthly_Installment')->textInput(['type' => 'text', 'readonly'=>true, 'value' => number_format($model->Monthly_Installment)]) ?>

                                    </div>

                                <?php endif; ?>

                            </div>
                       
                        </div>
                        <h3> Economic Sector Funding Details (Purpose of the Loan)</h3>
                        <hr>
                        <div class="row">
                            <div class=" row col-md-12">
                                <?php if($model->Portal_Status=='Application'): ?>

                                    <div class="col-md-4">
                                        <?= $form->field($model, 'Sector_Code')->dropDownList(Arrayhelper::map($EconomicSectors, 'Code', 'Name'),
                                        ['prompt' => 'Select Economic Sector',
                                        'onchange'=>'
                                            $.post("'.Yii::$app->urlManager->createUrl('loan/sub-sectors?id=').
                                                '"+$(this).val(),function( data ){
                                                $( "select#loanapplicationheader-subsector_code" ).html( data );
                                            });'
                                        ]) ?>
                                    </div>

                                    <div class="col-md-4">
                                    <?= $form->field($model, 'Subsector_Code')->dropDownList([],['prompt' => 'Select Sub Sector',
                                            'onchange'=>'
                                                $.post("'.Yii::$app->urlManager->createUrl('loan/sub-sub-sectors?SubSectorCode=').'"+$(this).val(),function( data ){
                                                    $( "select#loanapplicationheader-sub_subsector_code" ).html( data );
                                                });'
                                            ]) 
                                        ?>                                    </div>

                                    <div class="col-md-4">
                                        <?= $form->field($model, 'Sub_Subsector_Code')->dropDownList([],['prompt' => '--Select Option--', ]) ?>
                                    </div>

                                <?php else: ?>
                            
                                    <div class="col-md-4">
                                        <?= $form->field($model, 'Sector_Code')->dropDownList(Arrayhelper::map($loanProducts, 'Code', 'Name'),['prompt' => 'Select Loan Type','disabled'=>true]) ?>
                                    </div>

                                    <div class="col-md-4">
                                        <?= $form->field($model, 'Subsector_Code')->dropDownList([],['prompt' => '--Select Option--', 'disabled'=>true]) ?>                                    
                                    </div>

                                    <div class="col-md-4">
                                        <?= $form->field($model, 'Sub_Subsector_Code')->dropDownList([],['prompt' => '--Select Option--', 'disabled'=>true]) ?>
                                    </div>
                                    

                                <?php endif; ?>

                            </div>
                        </div>

                        <?php if($model->Portal_Status=='Application'): ?>
                            <div class="row">
                                <div class="form-group">
                                    <?= Html::submitButton('Save', ['class' => 'btn btn-success ', 'id'=>'SubmitButton']) ?>
                                </div>
                            </div>
                        <?php endif; ?>
                       
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
