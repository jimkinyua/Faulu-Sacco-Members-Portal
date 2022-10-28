<?php

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
use kartik\depdrop\DepDrop;

$webroot = Yii::getAlias(@$webroot);
$absoluteUrl = \yii\helpers\Url::home(true);
?>
<!--THE STEPS THING--->
<div class="row">
    <div class="col-md-12">
        <?= $this->render('..\loan\LoanSteps', ['model' => $model]) ?>
    </div>
    <div class="row">
        <div class="col-md-12">
            <!-- <?= \yii\helpers\Html::a('Payment Schedule', Url::to(['payment-schedule', 'Key' => $model->Key]), ['class' => 'btn btn-info btn-md mr-2 ']) ?> -->
        </div>
    </div>
</div>

<!--END THE STEPS THING--->
<h3 class="card-title"></h3>




<div class="card-body">

    <?php $form = ActiveForm::begin(['id' => 'PaymentDetails', 'options' => ['enctype' => 'multipart/form-data']]); ?>
    <?= $form->field($model, 'Member_No')->hiddenInput()->label(false) ?>
    <?= $form->field($model, 'Application_No')->hiddenInput()->label(false) ?>
    <?= $form->field($model, 'Key')->hiddenInput()->label(false) ?>



    <div class="row">
        <div class="col row col-md-12">
            <?php if ($model->Approval_Status == 'New') : ?>

                <div class="col-md-6">

                    <?= $form->field($model, 'Mode_of_Disbursement')->dropDownList([
                        'FOSA' => 'FOSA',
                        'Bank' => 'EFT',
                    ], ['prompt' => '-- Select Option --'])
                    ?>
                    <?= $form->field($model, 'Disbursement_Account')->textInput(['type' => 'text']) ?>
                    <?= $form->field($model, 'New_Monthly_Installment')->textInput([]) ?>




                </div>




                <div class="col-md-6 bank-details">
                    <?= $form->field($model, 'Pay_to_Bank_Code')->dropDownList($Banks, ['prompt' => 'Select Bank']) ?>

                    <?= $form->field($model, 'Pay_to_Branch_Code')->widget(DepDrop::classname(), [
                        'options' => [],
                        'pluginOptions' => [
                            'depends' => ['paymentdetails-pay_to_bank_code'],
                            'placeholder' => 'Select Branch ..',
                            'url' => Url::to(['/payment-details/branches'])
                        ]
                    ]); ?>
                    <?= $form->field($model, 'Pay_to_Account_No')->textInput(['type' => 'text']) ?>
                    <?= $form->field($model, 'Pay_to_Account_Name')->textInput(['type' => 'text']) ?>

                </div>



            <?php else : ?>


            <?php endif; ?>

        </div>

    </div>


    <?php if ($model->Approval_Status == 'New') : ?>
        <div>
            <div class="text-right">
                <?= Html::submitButton('Next Step', ['class' => 'btn btn-info submitButton',]) ?>
                <img class="loader-element ButtonPreloader" src="<?= $webroot ?>/html&css/assets/img/brand/preloader-spinner.gif" height="50" alt="Faulu logo">
            </div>

            <div class="text-left">
                <?= Html::a('Previous Step', Url::to(['payslip-information/index', 'Key' => $model->Key]), ['class' => 'btn btn-info mr-1',]) ?>
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
           

          
        let m = $('#paymentdetails-mode_of_disbursement').val();
        var url = $('input[name=url]').val()+'loan-repayment/get-member-accounts';
        if(m == 'FOSA'){
            $('.bank-details').hide();
            $('#portalloanrepayment-phoneno').val('N/A');
            $('#paymentdetails-disbursement_account').prop('disabled', true);
        } else{
            $('.bank-details').show();
        }    


        /*Handle dismissal eveent of modal */
        $('.modal').on('hidden.bs.modal',function(){
            var reld = location.reload(true);
            setTimeout(reld,1000);
        });

        
        $('#paymentdetails-mode_of_disbursement').change(function(e){
            const Source = e.target.value;
            var url = $('input[name=url]').val()+'loan-repayment/get-member-accounts';
            if(Source == 'FOSA'){
                $('.bank-details').hide();
                $('#portalloanrepayment-phoneno').val('N/A');
                $('#paymentdetails-disbursement_account').prop('disabled', true);
            } else{
                $('.bank-details').show();
            }    
        });


       

    });

    function disableSubmit(){
            document.getElementById('SubmitButton').setAttribute("disabled", "true");
    }
    
    function enableSubmit(){
        document.getElementById('SubmitButton').removeAttribute("disabled");
    
    }

    let currentTabId = 0// default

$('.ErrorPage').hide()
$('.submitButton').show();
$('.ButtonPreloader').hide();

const Tabs = [];

const DisableTabs = (TabIds)=>{
    console.log(TabIds)
    // if(element.id < ){

    // }
    TabIds.forEach((elementId, index)=>{
        console.log( parseInt(currentTabId))
        if( parseInt(elementId.id) < parseInt(currentTabId)){
            return true;
        }else{
            elementId.href = "javascript:void(0)";
        }
    })
}

$('.breadcrumbb').find('a').each((index, element)=>{
   if(element.className == 'active'){ //Don't Disble Current Tab
    currentTabId = element.id;
   }
   Tabs.push(element);
})

// DisableTabs(Tabs)




$('#PaymentDetails').on('beforeSubmit', function () {
    $('.ButtonPreloader').show();
    $('.submitButton').hide();

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
                // anchor.href = "javascript:void(0)";
                $('.breadcrumbb').find('a').each((index, element)=>{
                    if(element.className == 'active'){ //Don't Disble Current Tab
                        return true;
                    }
                    Tabs.push(element);
                })
                DisableTabs(Tabs)
                 $('.submitButton').show();
                $('.ButtonPreloader').hide();

            }
            
            else if (data.error) {
                // server validation failed
                $('.ErrorPage').text(data.error);
                $('.ErrorPage').show();
                $('.submitButton').show();
                $('.ButtonPreloader').hide();
                // anchor.href = "javascript:void(0)";
                $('.breadcrumbb').find('a').each((index, element)=>{
                    if(element.className == 'active'){ //Don't Disble Current Tab
                        return true;
                    }
                    Tabs.push(element);
                })
                DisableTabs(Tabs)

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
        
JS;

$this->registerJs($script);
