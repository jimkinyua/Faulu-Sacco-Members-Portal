<?php

use yii\bootstrap4\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'Subscriptions';
$webroot = Yii::getAlias(@$webroot);
$absoluteUrl = \yii\helpers\Url::home(true);

use kartik\select2\Select2;

$Subs =$SubscriptionAccounts; //$Applicant->getSubscriptions();

//  echo '<pre>';
//         print_r($Subs);
//         exit;
// echo '<pre>';
// print_r($Applicant->getSubscriptions());
// exit;
?>

<!--THE STEPS THING--->
<div class="row">
    <div class="col-md-12">
        <?= $this->render('..\profile\_steps', ['model' => $Applicant]) ?>
        <!-- <h3> Internal Recoveries </h3> -->
    </div>
</div>
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

<!--END THE STEPS THING--->

<?php if ($model->isNewRecord == false) : ?>
    <div class=" col-md-12 ">
        <div class="border-dark shadow-lg table-wrapper table-responsive">
            <table class="table table-hover" id="Subscriptions">
                <thead>
                    <tr>
                        <th class="border-0">Product</th>
                        <th class="border-0"> Amount </th>
                        <!-- <th class="border-0">Action</th> -->

                    </tr>
                </thead>
                <tbody>
                    <tr>

                        <td>
                            <input class="form-control" id="ModelKey" type="hidden" value="<?= ($model->Key) ?>" />
                        </td>
                    </tr>
                    <?php if ($Subs) : ?>

                        <?php foreach ($Subs as $Subscription) : ?>
                            <?php if (empty($Subscription->Product_Type) || !empty($Subscription->Loan_Disbursement_Ac)) : ?>
                                <?php continue; ?>
                            <?php endif; ?>
                            <tr>
                                <td><span class="font-weight-normal"><?= @$Subscription->Product_Name ?></span></td>

                                <td>
                                    <input class="form-control ContributionAmount" placeholder="<?= @$Subscription->Monthly_Contribution ?>" type="number" value="<?= number_format(@$Subscription->Monthly_Contribution) ?>" aria-label="Amount You Wish To Contribute For This Product">
                                    <input class="form-control Key" type="hidden" value="<?= ($Subscription->Key) ?>" />
                                </td>

                                <?php
                                $link = Html::a('Remove', Url::to(['delete', 'Key' => urlencode($Subscription->Key),]), ['class' => 'btn btn-danger btn-md']);
                                ?>
                                

                            </tr>
                        <?php endforeach; ?>

                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
<?php endif; ?>
</div>
<?php $form = ActiveForm::begin(['id' => 'confrimation-form']); ?>
<?= $form->field($model, 'Key')->hiddenInput()->label(false) ?>


<div class="text-left">
    <?= Html::a('Previous Page', Url::to(['kin/index', 'Key' => $model->Key]), ['class' => 'btn btn-info mr-1',]) ?>
</div>

<div class="text-right">
    <div class="form-group">
        <img class="loader-element ButtonPreloader" src="<?= $webroot ?>/html&css/assets/img/brand/preloader-spinner.gif" height="50" alt="Faulu logo">
        <?= Html::submitButton('Next Page', ['class' => 'btn btn-info submitButton']) ?>
    </div>
</div>

<?php ActiveForm::end(); ?>



<input type="hidden" name="absolute" value="<?= Yii::$app->recruitment->absoluteUrl() ?>">
<input type="hidden" id="Application_No" value="<?= $Applicant->No ?>">
<?php

$script = <<<JS

    function submitRowData(row){
                var Key = row.find('.Key').val();
                var ParameterValue = row.find('.ContributionAmount').val();
                var Application_No = $('#Application_No').val();

                    var updateurl = '/subscriptions/commit-row-data';
                    $.post(updateurl,
                        {  'Application_No':Application_No ,
                            'Parameter_Value':ParameterValue,
                            'Key':Key,
                        },
                        function(data){
                            console.log(data);

                            if(data.length){
                                swal("Error", data, "dnger");
                                return false;
                            }
                            row.find('.Key').val(data.Key);
                        }   
                    );
    }

   

    $(function(){


        $('#SubscriptionStartDate').on('change', ()=>{
            var ParameterValue = $('#SubscriptionStartDate').val();
            var Application_No = $('#Application_No').val();
            var ModelKey = $('#ModelKey').val();
            var updateurl = '/subscriptions/commit-sub-start-date';
            $.post(updateurl,
                {  'Application_No':Application_No ,
                    'Parameter_Value':ParameterValue,
                    'Key':ModelKey,
                },
                function(data){
                    console.log(data);
                    if(data.length){
                        // Swal.fire("Warning", data , "warning");;
                        return false;
                    }
                    row.find('.Key').val(data.Key);
                }   
            );
        });
        
        $('#Subscriptions').on('change','.ContributionAmount', function(){
            // alert('hu')
            var currentrow = $(this).closest('tr');
            return submitRowData(currentrow)
        });
        
        /*Handle dismissal eveent of modal */
        $('.modal').on('hidden.bs.modal',function(){
            var reld = location.reload(true);
            setTimeout(reld,1000);
        });

    });

      $('.ButtonPreloader').hide();
        $('.submitButton').show();
        // table = $('#leaves').DataTable()
            // console.log(table.column( 3 ).data().sum());

        $('#confrimation-form').on('beforeSubmit', function () {
        $('.ButtonPreloader').show();
        $('.submitButton').hide();
        window.location.replace('/referral/index?Key='+$('#applicationsubscriptions-key').val());
        return false; // prevent default form submission
        })
        
JS;

$this->registerJs($script);
