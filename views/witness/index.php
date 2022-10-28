<?php

use yii\bootstrap4\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'Loan Witness';
$webroot = Yii::getAlias(@$webroot);
$absoluteUrl = \yii\helpers\Url::home(true);
$Witnesses = $LoanModel->getWitness();
$numberOfWitnesses = 0;
if (is_array($Witnesses)) {
    $numberOfWitnesses = count($Witnesses);
}
// echo '<pre>';
// print_r($numberOfWitnesses);
// exit;
?>
<div class="row">
    <div class="col-md-12">


        <?php

        if (Yii::$app->session->hasFlash('success')) {
            print ' <div class="alert alert-success alert-dismissable">
                                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                                    <h5><i class="icon fas fa-check"></i> Success!</h5>';
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


    </div>
</div>

<!--THE STEPS THING--->
<div class="row">
    <div class="col-md-12">
        <?= $this->render('..\loan\LoanSteps', ['model' => $LoanModel]) ?>
        <h3> Witness </h3>
    </div>
</div>

<!--END THE STEPS THING--->
<?php if ($LoanModel->isNewRecord == false) : ?>
    <?php if ($LoanModel->Approval_Status == 'New') : ?>
        <?php if ($numberOfWitnesses < 1) : ?>
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">
                            <?= \yii\helpers\Html::a(' Add Witness', Url::to(['witness/create', 'LoanNo' => $LoanModel->Application_No]), ['class' => 'create btn btn-info btn-md mr-2 ']) ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>

    <?php endif; ?>
    <div class="card card-body border-light shadow-sm table-wrapper table-responsive pt-0">
        <table class="table table-hover" id="leaves">
            <thead>
                <tr>
                    <th class="border-0">Witness ID No</th>
                    <th class="border-0">Witness Name</th>
                    <th class="border-0">Phone No</th>
                    <th class="border-0">Loan Amount</th>
                    <!-- <th class="border-0">Guaranteed Amount</th>	 -->
                    <th class="border-0">Status</th>
                    <?php if ($LoanModel->Approval_Status == 'New') : ?>
                        <th class="border-0">Action</th>
                    <?php endif; ?>

                </tr>
            </thead>
            <tbody>
                <?php if ($Witnesses) : ?>

                    <?php foreach ($Witnesses as $guarantor) : ?>
                        <tr>
                            <td><span class="font-weight-normal"><?= @$guarantor->ID_No ?></span></td>
                            <td><span class="font-weight-normal"><?= @$guarantor->Member_Name ?></span></td>
                            <td><span class="font-weight-normal"><?= @$guarantor->PhoneNo ?></span></td>
                            <td><span class="font-weight-normal"><?= @number_format($guarantor->AppliedAmount) ?></span></td>
                            <td><span class="font-weight-normal"><?= @$guarantor->Status ?></span></td>

                            <?php
                            $updateLink = Html::a('Edit', Url::to(['witness/update', 'Key' => urlencode($guarantor->Key)]), ['class' => 'update btn btn-info btn-md']);
                            $link = Html::a('Delete', Url::to(['witness/delete', 'Key' => urlencode($guarantor->Key)]), ['class' => 'btn btn-danger btn-md']);
                            ?>
                            <?php if ($LoanModel->Approval_Status == 'New') : ?>
                                <td><span class="font-weight-normal"><?= $link . '| ' . $updateLink ?></span></td>
                            <?php endif; ?>

                        </tr>
                    <?php endforeach; ?>

                <?php endif; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>

<?php $form = ActiveForm::begin(['id' => 'confrimation-form', 'options' => ['enctype' => 'multipart/form-data']]); ?>
<?= $form->field($model, 'loanFormKey')->hiddenInput()->label(false) ?>
<div class="text-left">
    <?= Html::a('Previous Step', Url::to(['guarantors/index', 'Key' => $LoanModel->Key]), ['class' => 'btn btn-info mr-1',]) ?>
</div>

<div class="text-right">
    <img class="loader-element ButtonPreloader" src="<?= $webroot ?>/html&css/assets/img/brand/preloader-spinner.gif" height="50" alt="Faulu logo">
    <?= Html::submitButton('Next Step', ['class' => 'btn btn-info submitButton',]) ?>
</div>

<?php ActiveForm::end(); ?>


<input type="hidden" name="absolute" value="<?= Yii::$app->recruitment->absoluteUrl() ?>">
<input type="hidden" value="<?= $numberOfWitnesses ?>" id="NumberOfWitnesses">


<?php

$script = <<<JS

        
   

    /*End Data tables*/
            $('#leaves').on('click','.update', function(e){
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

$('#confrimation-form').on('beforeSubmit', function () {
    $('.ButtonPreloader').show();
    $('.submitButton').hide();
    if( parseInt($('#NumberOfWitnesses').val()) <= 0){
        alert('Kindly Add A Witness')
        $('.ButtonPreloader').hide();
        $('.submitButton').show();
    }else if(parseInt($('#NumberOfWitnesses').val()) > 1){
        alert('You Can Add a Maximum of 1 Witness')
        $('.ButtonPreloader').hide();
        $('.submitButton').show();
    }
    
    else{
        window.location.replace('/loan/send-for-approval?Key='+$('#onllineguarantorrequests-loanformkey').val());
    }

    return false; // prevent default form submission
})
        
JS;

$this->registerJs($script);
