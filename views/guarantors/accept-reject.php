<?php

use yii\bootstrap4\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;

//  echo '<pre>';
// print_r($loanData);
// exit;

?>

<?php

if (Yii::$app->session->hasFlash('success')) {
    print ' <div class="alert alert-success alert-dismissable">
             <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                    <h5><i class="icon fas fa-check"></i> Success!</h5>
';
    echo Yii::$app->session->getFlash('success');
    print '</div>';
} else if (Yii::$app->session->hasFlash('error')) {
    print ' <div class="alert alert-danger alert-dismissable">
             <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                    <h5><i class="icon fas fa-check"></i> Error!</h5>
                            ';
    echo Yii::$app->session->getFlash('error');
    print '</div>';
}
?>


<?php if ($model->Accepted === true) : ?>
    <h3 class="card-title">Guarantor Acceptance </h3>
<?php else : ?>
    <h3 class="card-title">Guarantor Rejectance </h3>
<?php endif; ?>

<?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>

<div class="card-body">
    <div class="row">
        <div class=" row col-md-12">

            <div class="col-md-12">
                <?php if ($model->Accepted === true) : ?>

                    <div class="card-body">
                        <?= $form->field($model, 'Key')->hiddenInput()->label(false) ?>
                        <?= $form->field($model, 'Loan_No')->hiddenInput()->label(false) ?>
                    </div>

                    <div class="row">
                        <div class=" row col-md-12">
                            <div class="col-md-4">
                                <?= $form->field($model, 'Loanee_Name')->textInput(['readonly' => true]) ?>
                                <?= $form->field($model, 'Amount_Guaranteed')->textInput(['readonly' => true, 'value'=>0]) ?>
                                <?= $form->field($model, 'Installments')->textInput(['readonly' => true, 'value'=>number_format($loanData[0]->Installments)]) ?>
                            </div>

                            <div class="col-md-4">
                                <?= $form->field($model, 'Loan_Product_Type_Name')->textInput(['readonly' => true, 'value'=>$loanData[0]->Installments]) ?>
                                <?= $form->field($model, 'Repayment_Start_Date')->textInput(['readonly' => true, 'value'=>$loanData[0]->Repayment_Start_Date]) ?>
                                <?= $form->field($model, 'Amount_Accepted')->textInput([]) ?>
                            </div>

                            <div class="col-md-4">
                                <?= $form->field($model, 'Repayment')->textInput(['readonly' => true, 'value'=>number_format($loanData[0]->Repayment)]) ?>
                                <?= $form->field($model, 'Available_Shares')->textInput(['readonly' => true]) ?>
                                <?= $form->field($model, 'AcceptTerms')->checkbox() ?>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="alert alert-success mb-2" role="alert">
                                <p>
                                    By Accepting the Terms and conditions, I pledge my shares with the society and any earnings with my current and future
                                    employer.
                                </p>
                                <p>
                                    I further understand that the defaulted amount(s) may be recovered by an offest against my share deposits in the society or by the achievement
                                    of my salary or properties and that I shall not be eligible for loans unless the amount in default is equal to the share deposits owned by the defaulter
                                </p>
                            </div>
                        </div>
                    </div>
                <?php else : ?>
                    <?= $form->field($model, 'Rejection_Reason')->textarea(['rows' => '7', 'cols' => '4', 'minlength' => true]) ?>
                <?php endif; ?>
            </div>

        </div>
    </div>
    <?php if ($model->Accepted === true) : ?>
        <div class="row">
            <div class="form-group form-actions">
                <?= Html::submitButton('Accept', ['class' => 'btn btn-info']) ?>
            </div>
        </div>

    <?php else : ?>
        <div class="row">
            <div class="form-group form-actions">
                <?= Html::submitButton('Reject', ['class' => 'btn btn-danger']) ?>
            </div>
        </div>

    <?php endif; ?>



</div>
<?php ActiveForm::end(); ?>



</div>

</div>
</div>
<?php

$script = <<<JS

    $(function(){
        
        $('#loanappsecurities-type').change(function(e){
            const SecurityType = e.target.value;
            const No = $('#loanappsecurities-application_no').val();
            const Key = $('#loanappsecurities-key').val();
            var dropdown = $('#loanappsecurities-code');

            if(SecurityType == 'Guarantor'){
                dropdown.replaceWith('<input type="text" id="loanappsecurities-code" class="form-control" name="LoanAppSecurities[Code]" aria-required="true">');
            }else{
                var url = '/guarantors/get-loan-securities';
                dropdown.replaceWith('<select id="loanappsecurities-code" class="form-control" name="LoanAppSecurities[Code]" aria-required="true"></select>');
                $.get(url, {'SecurityType': SecurityType}, function(response) {
                    $('#loanappsecurities-code').empty();
                    $('#loanappsecurities-code').append($('<option id="itemId"></option>').attr('value', '').text('-- Select Option --')); //append Here
                $.each(response, function (key, entry) {
                    $('#loanappsecurities-code').append($('<option id="itemId'+ entry.Code+'"></option>').attr('value', entry.Code).text(entry.Description)); //append Here
                })
            })
            }
           
    
        });
    });

    $('.LoadForm').on('click', function(e) {
        e.preventDefault();
        var url = $(this).attr('href');
        console.log('clicking...');
        $(document).find('.confirm-content').load(url);
    });
        
JS;

$this->registerJs($script);
