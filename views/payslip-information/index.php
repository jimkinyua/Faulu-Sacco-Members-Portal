<?php

use yii\bootstrap4\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'Payslip Information';
$webroot = Yii::getAlias(@$webroot);
$absoluteUrl = \yii\helpers\Url::home(true);
// echo '<pre>';
// print_r($LoanModel->getPayslipInformation());
// exit;
?>
<div class="row">
    <div class="col-md-12">
    </div>
</div>

<!--THE STEPS THING--->
<div class="row">
    <div class="col-md-12">
        <?= $this->render('..\loan\LoanSteps', ['model' => $LoanModel]) ?>

    </div>
</div>



<!--END THE STEPS THING--->
<?php if ($LoanModel->isNewRecord == false) : ?>
    <?php $form = ActiveForm::begin(['id' => 'confrimation-form']); ?>
    <?= $form->field($model, 'Key')->hiddenInput()->label(false) ?>


    <div class="card card-body border-light shadow-sm table-wrapper table-responsive pt-1">

        <h3> Payslip Information </h3>


        <div class="row">
            <div class="col-md-6 table-responsive">
                <table class="table mb-0" id="PayslipInformation">
                    <thead>
                        <tr>
                            <th>Description</th>
                            <th>Amount</th>
                                <!-- <th class="border-0">Action</th> -->

                        </tr>
                    </thead>
                    <br>

                    <tbody>
                        <?php if ($LoanModel->getPayslipInformation()) : ?>

                            <?php foreach ($LoanModel->getPayslipInformation() as $PayslipInformation) : ?>
                                <tr>
                                       
                                    <td><?= @$PayslipInformation->Description ?></span></td>


                                    <td>
                                        <span class=" d-flex align-items-center">
                                            <div class="d-block">
                                                <input class="form-control ParameterValue" value="<?= number_format($PayslipInformation->Amount) ?>" required />
                                            </div>
                                        </span>
                                        <input class="form-control Key" type="hidden" value="<?= ($PayslipInformation->Key) ?>" />

                                    </td>



                                    <?php
                                    $updateLink = ''; //Html::a('Edit',Url::to(['payslip-information/update','Key'=> urlencode($PayslipInformation->Key) ]) ,['class'=>'update btn btn-info btn-sm']);
                                    $link = ''; //Html::a('Remove',Url::to(['payslip-information/delete','Key'=> urlencode($PayslipInformation->Key) ]),['class'=>'btn btn-danger btn-sm']);
                                    ?>
                                    >

                                </tr>
                            <?php endforeach; ?>

                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
           
        </div>
    </div>
    <div class="text-right">

        <?= Html::a('Previous Step', Url::to(['loan/update', 'Key' => $LoanModel->Key]), ['class' => 'btn btn-warning mr-1',]) ?>

        <img class="loader-element ButtonPreloader" src="<?= $webroot ?>/html&css/assets/img/brand/preloader-spinner.gif" height="50" alt="Faulu logo">
        <?= Html::submitButton('Next Step', ['class' => 'btn btn-success submitButton',]) ?>
    </div>
    <?php ActiveForm::end(); ?>

<?php endif; ?>


<input type="hidden" name="absolute" value="<?= Yii::$app->recruitment->absoluteUrl() ?>">
<input type="hidden" id="LoanNumber" value="<?= $LoanModel->Loan_No ?>">

<?php

$script = <<<JS

    function submitRowData(row){
            var Key = row.find('.Key').val();
            var ParameterValue = row.find('.ParameterValue').val();
            var LoanNo = $('#LoanNumber').val();

                var updateurl = '/payslip-information/commit-row-data';
                $.post(updateurl,
                    {  'LoanNo':LoanNo ,
                        'Parameter_Value':ParameterValue,
                        'Key':Key,
                    },
                    function(data){
                        // console.log(data);
                        if(data.length){
                            Swal.fire({
                                icon: 'error',
                                title: 'Oops...',
                                text: data,
                            });
                            row.find('.ParameterValue').val('')
                            return false;
                        }

                        $('#ThirdBasic').val(data.OneThird);
                        $('#EstimatedRepayment').val(data.EstimatedMonthly);
                        $('#AvailableRepayment').val(data.AvailableForRepayment);
                        $('#AdjustedNetIncome').val(data.AdjustNetIncome);
                        row.find('.Key').val(data.Key);
                    }   
                );



        }


    $(function(){

        $('#PayslipInformation').on('change','.ParameterValue', function(){
            var currentrow = $(this).closest('tr');
            return submitRowData(currentrow)
        });
        
        /*Handle dismissal eveent of modal */
        $('.modal').on('hidden.bs.modal',function(){
            var reld = location.reload(true);
            setTimeout(reld,1000);
        });
    });

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




$('#confrimation-form').on('beforeSubmit', function () {
    $('.ButtonPreloader').show();
    $('.submitButton').hide();

        window.location.replace('/internal-deductions/index?Key='+$('#payslipinformation-key').val());

    return false; // prevent default form submission
})
        
JS;

$this->registerJs($script);
