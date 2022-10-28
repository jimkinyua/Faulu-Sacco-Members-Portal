<?php

use kartik\depdrop\DepDrop;
use yii\bootstrap4\ActiveForm;
use yii\bootstrap4\Html;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;


$this->title = 'Fixed Deposit';
$absoluteUrl = \yii\helpers\Url::home(true);

?>

<h3 class="card-title">Standing Order Details</h3>
<?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>
<div class="card-body">
    <?= $form->field($model, 'Key')->hiddenInput()->label(false) ?>
    <?= $form->field($model, 'Document_No')->hiddenInput()->label(false) ?>



    <div class="row">
        <div class=" row col-md-12">

            <?php if ($model->Approval_Status == 'New') : ?>
                <div class="col-md-3">
                    <?= $form->field($model, 'STO_Type')->dropDownList(ArrayHelper::map($STOTypes, 'Code', 'Name'), ['prompt' => '--Select Option--']) ?>
                    <?= $form->field($model, 'Standing_Order_Class')->dropDownList([
                        'Internal' => 'Internal',
                        'External' => 'External',
                        'Loan_Principle' => 'Loan Principle',
                        'Loan_Interest' => 'Loan Interest',
                        'Loan_Principle_x002B_Interest' => 'Loan Principle Interest',

                    ], ['prompt' => '--Select Option--']) ?>

                    <?= $form->field($model, 'Amount_Type')->dropDownList([
                        'Fixed' => 'Fixed',
                        'Sweep' => 'Sweep',
                    ], ['prompt' => '--Select Option--']) ?>

                    <?= $form->field($model, 'Salary_Based')->radioList(
                        [
                            1 => 'Yes',
                            0 => 'No',
                        ]
                    )
                    ?>

                </div>

                <div class="col-md-3">

                    <?= $form->field($model, 'Account_No')->dropDownList(ArrayHelper::map($MemberAccounts, 'Code', 'Name'), ['prompt' => '--Select Option--']) ?>
                    <?= $form->field($model, 'Start_Date')->textInput(['type' => 'date']) ?>
                    <?= $form->field($model, 'Period')->textInput() ?>
                    <?= $form->field($model, 'Amount')->textInput() ?>


                </div>

                <div class="col-md-3 MemberAccountDetails">

                    <?= $form->field($model, 'Destination_Member_No')->textInput() ?>
                    <?= $form->field($model, 'Destination_Account')->dropDownList([], ['prompt' => '--Select Option--']) ?>

                </div>

                <div class="col-md-3 BankDetails">




                    <?= $form->field($model, 'EFT_Account_Name')->textInput() ?>
                    <?= $form->field($model, 'EFT_Bank_Name')->dropDownList(
                        Arrayhelper::map($Banks, 'Code', 'Name'),
                        [
                            'prompt' => 'Select Bank',
                        ]
                    ) ?>

                    <?= $form->field($model, 'EFT_Brannch_Code')->widget(DepDrop::classname(), [
                        'options' => [],
                        'pluginOptions' => [
                            'depends' => ['standingordercard-eft_bank_name'],
                            'placeholder' => 'Select Sub Sub Sector',
                            'url' => Url::to(['/standing-order/branches'])
                        ]
                    ]); ?>


                </div>

            <?php else : ?>

            <?php endif; ?>

        </div>
    </div>

    <?php if ($model->Approval_Status == 'New') : ?>

        <div class="row">
            <div class="form-group">
                <?= Html::submitButton('Submit ', ['class' => 'btn btn-success', 'id' => 'SubmitButton']) ?>
            </div>

        </div>
    <?php endif; ?>

</div>

<?php ActiveForm::end(); ?>
<input type="hidden" name="url" value="<?= $absoluteUrl ?>">

<?php

$script = <<<JS

const PopulateDropDown = (url, dropDown, memberID)=>{
    $.get(url, {'AccountNo':memberID }, function(response) {
        dropDown.empty();
        dropDown.append($('<option id="itemId"></option>').attr('value', '').text('-- Select Option --')); //append Here
    $.each(response, function (key, entry) {
        if(memberID == entry.Code){
            dropDown.append($('<option id="itemId'+ entry.Code+'" selected></option>').attr('value', entry.Code).text(entry.Name)); //append Here
            return true // Continue;
        }
        dropDown.append($('<option id="itemId'+ entry.Code+'"></option>').attr('value', entry.Code).text(entry.Name)); //append Here
    })});
}


if( $('#standingordercard-standing_order_class').val() == 'External'){
    $('.MemberAccountDetails').hide();
    $('.BankDetails').show();

}else{
    $('.MemberAccountDetails').show();
    $('.BankDetails').hide();

}



function enableOrDisableAmount(){
    if( $('#standingordercard-amount_type').val() == 'Sweep'){
        $('#standingordercard-amount').val(0);
        $('#standingordercard-amount').prop('readonly', true);
    }else{
        $('#standingordercard-amount').prop('readonly', false);
    }
}

let url = '/standing-order/member-accounts';
let dropDown = $('#standingordercard-destination_account');
let memberID = $('#standingordercard-destination_member_no').val()
PopulateDropDown(url, dropDown, memberID)


$('#standingordercard-standing_order_class').on('change', (element)=>{
    if($('#standingordercard-standing_order_class').val() == 'External'){
        $('.MemberAccountDetails').hide();
        $('.BankDetails').show();

    }else{
        $('.MemberAccountDetails').show();
        $('.BankDetails').hide();

    }
})

$('#standingordercard-amount_type').on('change', (element)=>{
 enableOrDisableAmount()
})


$('#standingordercard-destination_member_no').on('change', ()=>{
    let url = '/standing-order/member-accounts';
    let dropDown = $('#standingordercard-destination_account');
    let memberID = $('#standingordercard-destination_member_no').val()
    if(memberID){
        PopulateDropDown(url, dropDown, memberID)
    }
});



JS;

$this->registerJs($script);
