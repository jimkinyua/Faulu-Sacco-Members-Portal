<?php

use kartik\depdrop\DepDrop;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;


$webroot = Yii::getAlias(@$webroot);
$absoluteUrl = \yii\helpers\Url::home(true);
?>
<!--THE STEPS THING--->
<div class="row">
    <div class="col-md-12">
        <?= $this->render('.\LoanSteps', ['model' => $model]) ?>
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

    <?php $form = ActiveForm::begin(['id' => 'LoanDetails', 'options' => ['enctype' => 'multipart/form-data']]); ?>
    <?= $form->field($model, 'Member_No')->hiddenInput()->label(false) ?>
    <?= $form->field($model, 'Loan_No')->hiddenInput()->label(false) ?>
    <?= $form->field($model, 'Key')->hiddenInput()->label(false) ?>



    <div class="row">
        <div class="col row col-md-12">

                <div class="col-md-6">
                    <?= $form->field($model, 'Loan_Product_Type')->dropDownList(Arrayhelper::map($loanProducts, 'Code', 'Name'), ['prompt' => 'Select Loan Type']) ?>
                    <?= $form->field($model, 'Requested_Amount')->textInput(['type' => 'text', 'value' => $model->Requested_Amount]) ?>
                    
                    <?= $form->field($model, 'Sub_Sectors')->dropDownList(
                        Arrayhelper::map($SubEconomicSectors, 'Code', 'Description'),
                        [
                            'prompt' => 'Select Economic Sector',
                            'onchange' => '$.post("/loan/sub-sub-sectors?type="+$(this).val(), (data) => {
                                                $("#loandetails-purpose_of_loan").empty();
                                                $("select#loandetails-purpose_of_loan").html( data );
                                            })'
                        ]
                    ) ?>
                  

                </div>

                <div class="col-md-6">

                 
                <?= $form->field($model, 'Sectors')->dropDownList(
                        Arrayhelper::map($EconomicSectors, 'Code', 'Name'),
                        [
                            'prompt' => 'Select Sector',
                            'onchange' => '$.post("/loan/sub-sectors?type="+$(this).val(), (data) => {
                                $("#loandetails-sub_sectors").empty();
                                $("select#loandetails-sub_sectors").html( data );
                            })'
                        ]
                    ) ?>

                    <?= $form->field($model, 'Installments')->textInput(['type' => 'text', 'value' => $model->Installments]) ?>




                    <?= $form->field($model, 'Purpose_of_Loan')->dropDownList(
                        Arrayhelper::map($SubSubEconomicSectors, 'Code', 'Description'),
                        [
                            'prompt' => 'Select Sub Sub Sector',
                        ]
                    ) ?>

                </div>


        </div>

    </div>


    <?php if ($model->Status == 'Open') : ?>
        <!-- <div class="row"> -->
        <div class="text-right">
            <!-- <button type="button" class="btn btn-warning mr-1">
                    <i class="ft-x"></i> Go Back
                </button> -->
            <img class="loader-element ButtonPreloader" src="<?= $webroot ?>/html&css/assets/img/brand/preloader-spinner.gif" height="50" alt="Faulu logo">
            <?= Html::submitButton('Next Step', ['class' => 'btn btn-info submitButton',]) ?>
        </div>

        <!-- </div> -->
    <?php endif; ?>

</div>

</div>


<?php ActiveForm::end(); ?>
</div>
</div>
<input type="hidden" name="url" value="<?= $absoluteUrl ?>">

<?php

$script = <<<JS

    $('.ErrorPage').hide()
    $('.submitButton').show();
    $('.ButtonPreloader').hide();
    $('#loandetails-loan_period').prop('max',$('#loandetails-loan_period').val());

     $(document).on('change','#loandetails-product_code', function(){
            var Key = $('#loandetails-key').val();
            var Product = $('#loandetails-product_code').val();
            var updateurl = 'set-loan-product';
            $.post(updateurl,
                {  
                    'Product':Product,
                    'Key':Key,
                },
                function(data){
                    console.log(data);

                    if(data.error){
                        // Swal.fire("Warning", data , "warning");;
                    $('.ErrorPage').text(data.error);
                    $('.ErrorPage').show();
                    $('.submitButton').show();
                    $('.ButtonPreloader').hide();
                        return false;
                    }
                    $('#loandetails-loan_period').val(data.Maximum_Repayment_Period);
                    $('#loandetails-loan_period').prop('max',data.Maximum_Repayment_Period);
                }   
            );
    });

    $('input[type="number"]').on('keyup', function(){ 
        if ($(this).val() > $(this).attr('max')*1) { 
            $(this).val($(this).attr('max'));   
        }  
     });

    const Tabs = [];

    const DisableTabs = (TabIds)=>{
        console.log(TabIds)

        TabIds.forEach((elementId, index)=>{
            // elementId.href = "javascript:void(0)";
        })
    }

   $('.breadcrumbb').find('a').each((index, element)=>{
       if(element.className == 'active'){ //Don't Disble Current Tab
        return true;
       }
       Tabs.push(element);
   })

//    DisableTabs(Tabs)




    $('#LoanDetails').on('beforeSubmit', function () {
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
                    $('.ErrorPage').text(data.validation);
                    $('.ErrorPage').show();
                    $('.submitButton').show();
                    $('.ButtonPreloader').hide();
                    $('.breadcrumbb').find('a').each((index, element)=>{
                        if(element.className == 'active'){ //Don't Disble Current Tab
                            return true;
                        }
                        Tabs.push(element);
                    })
                    // DisableTabs(Tabs)

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
