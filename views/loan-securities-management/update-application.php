<?php

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
$absoluteUrl = \yii\helpers\Url::home(true);
// echo '<pre>';
// print_r($model->getReplacements());
// exit;

?>
        
                        <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>
                            <?= $form->field($model,'Key')->hiddenInput()->label(false) ?>
                                            <?= $form->field($model, 'Loan_No')->dropDownList(Arrayhelper::map($MemberLoans, 'Code', 'Name'),['prompt' => '-- Select Loan --' ,'disabled'=>true]) ?>


                            
                    <?php if($model->Portal_Status == 'New'): ?>
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-body">
                                    <?= \yii\helpers\Html::a('Submit ',['submit', 'DocNum'=>$model->Document_No],['class' => 'btn btn-success btn-md mr-2']) ?>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                    
                            

                        <?php ActiveForm::end(); ?>
                    </div>

    
                    <div class="card card-body border-light shadow-sm table-wrapper table-responsive pt-0">
                        <h2> Current Loan Securities </h2>
                        <table class="table table-hover" id="leaves">
                            <thead>
                                <tr>
                                    <th class="border-0">Type of Security</th>
                                    <th class="border-0"> Security Value </th>
                                    <th class="border-0"> Guaranteed Amount </th>
                                    <?php if($model->Portal_Status == 'New'): ?>
                                        <th class="border-0">Action</th>
                                   <?php endif; ?>
            
                                </tr>
                            </thead>
                            <tbody>
                            <?php if($model->getLines() ): ?>

                                            <?php foreach($model->getLines() as $guarantor): ?> 
                                                <tr>
                                                    <td><span class="font-weight-normal"><?= @$guarantor->Description ?></span></td>
                                                    <td><span class="font-weight-normal"><?= @number_format($guarantor->Value) ?></span></td>
                                                    <td><span class="font-weight-normal"><?= @number_format($guarantor->Actual_Guaranteed_Amount) ?></span></td>
                                                    <?php
                                                        $ReplaceWithSecurity = Html::a('Replace With Security',Url::to(['security-replacements/create-security',
                                                        'DocNum'=> urlencode($model->Document_No), 'Existing'=>$guarantor->Code, 'LoanNo'=>$model->Loan_No ]) ,
                                                        ['class'=>'update btn btn-info btn-xs']
                                                       );

                                                        $ReplaceWithGuarantor = Html::a('Replace With Guarantor',Url::to(['security-replacements/create',
                                                        'DocNum'=> urlencode($model->Document_No),'Existing'=>$guarantor->Code ,'LoanNo'=>$model->Loan_No ]) ,
                                                        ['class'=>'update btn btn-success btn-xs btn-md']
                                                      );
                                                    ?>

                                                      <?php if($model->Portal_Status == 'New'): ?>
                                                        <td><span class="font-weight-normal"><?= $ReplaceWithSecurity . ' '. $ReplaceWithGuarantor ?></span></td>                                                                            
                                                    <?php endif; ?>

                                                </tr>
                                            <?php endforeach;?>
                                                
                            <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="card card-body border-light shadow-sm table-wrapper table-responsive pt-0">
                        <h2> Replacements </h2>
                        <table class="table table-hover" id="leaves">
                            <thead>
                                <tr>
                                    <th class="border-0">Type of Security</th>
                                    <th class="border-0">Description</th>
                                    <?php if($model->Portal_Status == 'New'): ?>
                                        <th class="border-0">Action</th>
                                    <?php endif; ?>
            
                                </tr>
                            </thead>
                            <tbody>
                            <?php if($model->getReplacements() ): ?>

                                            <?php foreach($model->getReplacements() as $replacement): ?> 
                                                <tr>
                                                    <td><span class="font-weight-normal"><?= @$replacement->Description ?></span></td>
                                                    <td><span class="font-weight-normal"><?= @$replacement->Security_Details ?></span></td>
                                                    <?php
                                                      if($replacement->Type == 'Security'){
                                                        $editLink = Html::a('Edit',Url::to(['security-replacements/update-security','Key'=> urlencode($replacement->Key) ]) ,['class'=>'btn update btn-success btn-md']);
                                                      }else{
                                                        $editLink = Html::a('Edit',Url::to(['security-replacements/update','Key'=> urlencode($replacement->Key) ]) ,['class'=>'btn update btn-success btn-md']);
                                                      }
                                                        $deleteLink = Html::a('Remove',Url::to(['security-replacements/delete','Key'=> urlencode($replacement->Key) ]) ,['class'=>'btn btn-danger btn-md']);
                                                    ?>

                                                     <?php if($model->Portal_Status == 'New'): ?>
                                                        <td><span class="font-weight-normal"><?= $editLink. ' '. $deleteLink?></span></td>                                                                            
                                                    <?php endif; ?>

                                                </tr>
                                            <?php endforeach;?>
                                                
                            <?php endif; ?>
                            </tbody>
                        </table>
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
