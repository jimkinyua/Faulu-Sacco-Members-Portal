<?php
use yii\widgets\ActiveForm;
use yii\helpers\Html;
use borales\extensions\phoneInput\PhoneInput;


$this->title = 'Membership Activation';

$AmountToPay = 1000; //Default
if(Yii::$app->user->identity->memebershipType == 'GRP'){
    $AmountToPay = 5000;
}

if(Yii::$app->session->hasFlash('success')){
    print ' <div class="alert alert-success alert-dismissable">
                     <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                            <h5><i class="icon fas fa-check"></i> Success!</h5>
';
    echo Yii::$app->session->getFlash('success');
    print '</div>';
}else if(Yii::$app->session->hasFlash('error')){
    print ' <div class="alert alert-danger alert-dismissable">
                         <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                            <h5><i class="icon fas fa-check"></i> Error!</h5>
                        ';
    echo Yii::$app->session->getFlash('error');
    print '</div>';
}

?>




        <div class="col-md-12">
             <div class="card-body">
               <!-- <h2>Membership Activation</h2> -->
               <h3>Your membership application has been approved. Kindly make a payment of KES <?= $AmountToPay ?> </h3>
               <br>
                <?php $form = ActiveForm::begin(['id' => 'application-confirmation-form',  'options' => ['enctype' => 'multipart/form-data']]); ?>
                    <div class="row">
                        <!-- <div class="col-lg-4 col-sm-4 col-md-4">
                            <h3>Select your payment option</h3>
                            <input type="hidden" name="InvoicedAmount" value="">
                            <div class="btn-group-vertical btn-group-lg" role="group" aria-label="payment-options">
                                <label class="btn" style="text-align: left;">
                                    <input type="radio" name="payment-method" required value="3" > M-PESA <img src="/html&css/assets/img/mpesa.png" style="height: 40px;" />
                                </label>
                                <label class="btn" style="text-align: left;">
                                    <input type="radio" name="payment-method" required value="1" > Bank Deposit <img src="/html&css/assets/img/bank.png" style="height: 40px;" />
                                </label>
                            </div>
                        </div> -->
                        <div class="col-lg-12 col-sm-12 col-md-12" id="payment-guideline">
                            <u> <h3 align="center">Mpesa Payment Methods </h3> </u>
                            <br>
                            <div class="row">
                                <div class="col-md-12" id="mpesa-response"></div> <!--style="font: italic bold 12px/30px Georgia, serif;"-->
                                    <div class="col-md-12"><p ><strong style="color:red;">NB:</strong>There are two acceptable methods of payment i.e. the STK PUSH and C2B. Please Feel free to use either of the two. Guidelines for each are provided below.</p></div>
                                    <br>
                                        <div class="col-md-6">
                                            <div class="panel panel-default">
                                            <div class="panel-body">
                                                    <div class="form-group">
                                                        <h4>STK-Push Payment Mpesa Guidelines</h4>
                                                        <!--<input type="hidden" name="_csrf-licensing" value="amYSgaq5r6uAlq7NmyEj4YWjfFkmeHh-1LN96TvuXeMHM2rq-d752MnenL_jGGDV6fQwPU8nJwis7AuPeIxv1Q==">--> 
                                                        <ul>
                                                            <li><strong>Unlock your phone</strong> and ensure it's on</li>
                                                            <li>Enter the phone number you want to use <i><strong>e.g 07xxxxxxxx</strong></i></li>
                                                            <li>Send payment request to the entered phone number by clicking the button below</li>
                                                            <li style="color:red;">If a request is <strong>not sent</strong> to your phone, and it brings an error <strong>'Operation cancelled 09'</strong>, please use the other method.</li>
                                                            <li>Enter your <strong>Mpesa Pin</strong> and press okay</li>
                                                            <li>You will receive an SMS confirming the transaction</li> 
                                                            <!-- <li>Click Save button below after receiving the confirmation sms</li>  -->
                                                        </ul> 
                                                        
                                                        <?= $form->field($model, 'STKpushNo')->widget(PhoneInput::className(), [
                                                        'jsOptions' => [
                                                            'preferredCountries' => ['ke'],
                                                        ]]) ?>

                                                        <span id="number-response"></span>
                                                        <br>
                                                        <?= Html::submitButton('Send Request To Phone', ['class' => 'btn btn-success',]) ?>
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
                                                        <h4>M-PESA C2B Payment Guidelines</h4>
                                                            <ul>
                                                                <li>Go to&nbsp;<strong>M-PESA</strong>&nbsp;Menu on your mobile phone</li>
                                                                <li>Select&nbsp;<strong>Pay Bill</strong></li>
                                                                <li>Enter 540700 <strong>&nbsp;</strong>as the Business Number</li>
                                                                <li>Enter <strong> <?= $model->Application_No ?></strong>&nbsp;as&nbsp;<strong>ACCOUNT NUMBER</strong>&nbsp;option</li>
                                                                <li>Enter <?= $AmountToPay ?> as the amount to pay (NO COMMAS) </li>
                                                                <li>Enter your&nbsp;<strong>M-PESA PIN</strong></li>
                                                                <li>Then send the&nbsp;request</li>
                                                                <li>You will receive an SMS confirming the transaction</li>
                                                                <li>Once your membership number is Processed, you will receive a confrimation message from us with your member number</li>

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
                <?php ActiveForm::end(); ?>
            </div>
        </div>

       

        <?php

$script = <<<JS

    $(function(){
        
      

    });
        
JS;

$this->registerJs($script);


           