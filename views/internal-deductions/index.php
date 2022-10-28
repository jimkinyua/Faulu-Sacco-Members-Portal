<?php

use yii\bootstrap4\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'Internal Recoveries';

use kartik\select2\Select2;

$webroot = Yii::getAlias(@$webroot);
$absoluteUrl = \yii\helpers\Url::home(true);

// echo '<pre>';
// print_r($LoanModel->getDirectDebitInformation());
// exit;
?>

<!--THE STEPS THING--->
<div class="row">
    <div class="col-md-12">
        <?= $this->render('..\loan\LoanSteps', ['model' => $LoanModel]) ?>
    </div>

        <div class="card">

            <h3 class="card-title"></h3>

            <div class="card-body">

                <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>
                <?= $form->field($model, 'Key')->hiddenInput()->label(false) ?>
                <?= $form->field($model, 'Loan_Top_Up')->hiddenInput()->label(false) ?>





                <div class="row">
                    <div class="col row col-md-12 d-flex">

                        <div class="col-md-6">

                            <?= $form->field($model, 'Loan_Top_Up')->widget(Select2::classname(), [
                                'data' => $LoanToRecover,
                                'options' => ['placeholder' => 'Select Loan To Be Bridged',],
                                'pluginOptions' => [
                                    'allowClear' => true
                                ],
                            ]);
                            ?>

                        </div>
                        <br>

                        <div class="col-md-6">
                            <?= Html::submitButton('Bridge', ['class' => 'btn btn-info ', 'id' => 'SubmitButton']) ?>
                        </div>


                    </div>
                </div>
                <?php ActiveForm::end(); ?>

            </div>




        </div>

        <!--END THE STEPS THING--->
        <?php if ($LoanModel->isNewRecord == false) : ?>

            <div class="card card-body border-light shadow-sm table-wrapper table-responsive pt-0">
                <table class="table table-hover" id="guarantors">
                    <thead>
                        <tr>
                            <th class="border-0">Loan No</th>
                            <th class="border-0">Loan Amount</th>
                            <th class="border-0"> Current Balance </th>
                            <th class="border-0">Action</th>

                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($LoanModel->getInternalRecoveryInformation()) : ?>

                            <?php foreach ($LoanModel->getInternalRecoveryInformation() as $InternalRecoveryInformation) : ?>
                                <?php if (empty($InternalRecoveryInformation->Loan_Top_Up) || empty($InternalRecoveryInformation->Outstanding_Balance)) : ?>
                                    <?php continue; ?>
                                <?php endif; ?>
                                <tr>
                                    <td><span class="font-weight-normal"><?= @$InternalRecoveryInformation->Recovery_Code ?></span></td>

                                    <td>
                                        <div class="font-weight-normal"><?= @number_format($InternalRecoveryInformation->Amount) ?></div>
                                    </td>
                                    <td><span class="font-weight-normal"><?= @number_format($InternalRecoveryInformation->Current_Balance) ?></span></td>

                                    <?php
                                    $link = Html::a('Remove', Url::to(['delete', 'Key' => urlencode($InternalRecoveryInformation->Key),]), ['class' => 'btn btn-danger btn-md']);
                                    ?>
                                        <td><span class="font-weight-normal"><?= $link  ?></span></td>
                                </tr>
                            <?php endforeach; ?>

                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>


        <input type="hidden" name="absolute" value="<?= Yii::$app->recruitment->absoluteUrl() ?>">
        <?php $form = ActiveForm::begin(['id' => 'PayslipInformation', 'options' => ['enctype' => 'multipart/form-data']]); ?>
        <div class="text-left">
            <?= Html::a('Previous Step', Url::to(['payslip-information/update', 'Key' => $LoanModel->Key]), ['class' => 'btn btn-info mr-1',]) ?>
        </div>

        <div class="text-right">
            <?= Html::submitButton('Next Step', ['class' => 'btn btn-info submitButton',]) ?>
        </div>

        <?php ActiveForm::end(); ?>


        <?php

        $script = <<<JS

    $(function(){
        
   

    /*End Data tables*/
            $('#guarantors').on('click','.update', function(e){
                 e.preventDefault();
                var url = $(this).attr('href');
                console.log('clicking...');
                $('.modal').modal('show')
                                .find('.modal-body')
                                .load(url); 
    
            });
            
            
           //Add an experience
        
         $('.create').on('click',function(e){
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

    DisableTabs(Tabs)

    $('#PayslipInformation').on('beforeSubmit', function () {
        $('.ButtonPreloader').show();
        $('.submitButton').hide();
        // alert($('#loaninternaldeductions-key').val())
        window.location.replace('/guarantors/index?Key='+$('#loaninternaldeductions-key').val());
        return false; // prevent default form submission
    })


    });
        
JS;

        $this->registerJs($script);
