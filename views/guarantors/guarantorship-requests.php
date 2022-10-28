<?php

use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'Loan Guarantors';
// echo '<pre>';
// print_r($GuarantorshipRequests);
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
<h1> Guarantorship Requests </h1>
<div class="card card-body border-light shadow-sm table-wrapper table-responsive pt-0">
    <table class="table table-hover" id="leaves">
        <thead>
            <tr>
                <th class="border-0">Loan No</th>
                <!-- <th class="border-0">Loan Amount </th> -->
                <th class="border-0"> Loanee Name </th>
                <th class="border-0"> Requested Amount</th>
                <!-- <th class="border-0">Application Date</th> -->
                <!-- <th class="border-0">Loan Product Name</th> -->
                <th class="border-0">Action</th>

            </tr>
        </thead>
        <tbody>

            <?php if (!is_object($GuarantorshipRequests)) : ?>

                <?php foreach ($GuarantorshipRequests as $GuarantorshipRequest) : ?>
                    <?php if(!isset($GuarantorshipRequest->Loan_No)): ?>
                        <?php continue; ?>
                    <?php endif; ?>
                    <tr>
                        <td><span class="font-weight-normal"><?= @$GuarantorshipRequest->Loan_No ?></span></td>
                        <!-- <td><span class="font-weight-normal"><?= @number_format($GuarantorshipRequest->AppliedAmount) ?></span></td> -->
                        <td><span class="font-weight-normal"><?= @$GuarantorshipRequest->Loanee_Name ?></span></td>
                        <td><span class="font-weight-normal"><?= @number_format($GuarantorshipRequest->Amount_Guaranteed) ?></span></td>
                        <!-- <td><span class="font-weight-normal"><?= @$GuarantorshipRequest->Application_Date ?></span></td> -->
                        <!-- <td><span class="font-weight-normal"><?= @$GuarantorshipRequest->Product_Name ?></span></td> -->
                        <?php
                        $acceptLink = Html::a(
                            'Accept',
                            Url::to([
                                'guarantors/accept',
                                'Key' => urlencode($GuarantorshipRequest->Key)
                            ]),
                            ['class' => 'create btn btn-info btn-md']
                        );
                        $rejectlink = Html::a('Reject', Url::to(['guarantors/reject', 'Key' => urlencode($GuarantorshipRequest->Key)]), ['class' => 'update btn btn-danger btn-md']);

                        ?>
                        <td><span class="font-weight-normal"><?= $acceptLink . ' ' . $rejectlink ?></span></td>

                    </tr>
                <?php endforeach; ?>

            <?php endif; ?>

            <?php if (!is_object($GuarantorSubstituitionRequests)) : ?>
                <?php foreach ($GuarantorSubstituitionRequests as $GuarantorSubstituitionRequest) : ?>
                    <tr>
                        <td><span class="font-weight-normal"><?= @$GuarantorSubstituitionRequest->Document_No ?></span></td>
                        <td><span class="font-weight-normal"><?= @number_format($GuarantorSubstituitionRequest->Amount) ?></span></td>
                        <td><span class="font-weight-normal"><?= @$GuarantorSubstituitionRequest->Applicant ?></span></td>
                        <td><span class="font-weight-normal"><?= @number_format($GuarantorSubstituitionRequest->Amount_Accepted) ?></span></td>
                        <?php
                        $acceptLink = Html::a('Accept', Url::to(['guarantors/accept-sub', 'Key' => urlencode($GuarantorSubstituitionRequest->Key)]), ['class' => 'update btn btn-info btn-md']);
                        $rejectlink = Html::a('Reject', Url::to(['guarantors/reject-sub', 'Key' => urlencode($GuarantorSubstituitionRequest->Key)]), ['class' => 'update btn btn-danger btn-md']);
                        ?>
                        <td><span class="font-weight-normal"><?= $acceptLink . ' ' . $rejectlink ?></span></td>

                    </tr>
                <?php endforeach; ?>

            <?php endif; ?>

            <?php if (!is_object($GuarantorSecurityReplacementsRequests)) : ?>
                <?php foreach ($GuarantorSecurityReplacementsRequests as $GuarantorSecurityReplacementsRequest) : ?>
                    <tr>
                        <td><span class="font-weight-normal"><?= @$GuarantorSecurityReplacementsRequest->Loan_No ?></span></td>
                        <td><span class="font-weight-normal"><?= @number_format($GuarantorSecurityReplacementsRequest->AppliedAmount) ?></span></td>
                        <td><span class="font-weight-normal"><?= @$GuarantorSecurityReplacementsRequest->Applicant ?></span></td>
                        <td><span class="font-weight-normal"><?= @number_format($GuarantorSecurityReplacementsRequest->GuaranteedAmount) ?></span></td>
                        <?php
                        $acceptLink = Html::a('Accept', Url::to(['guarantors/accept-sub-sec', 'Key' => urlencode($GuarantorSecurityReplacementsRequest->Key)]), ['class' => 'update btn btn-info btn-md']);
                        $rejectlink = Html::a('Reject', Url::to(['guarantors/reject-sub-sec', 'Key' => urlencode($GuarantorSecurityReplacementsRequest->Key)]), ['class' => 'update btn btn-danger btn-md']);
                        ?>
                        <td><span class="font-weight-normal"><?= $acceptLink . ' ' . $rejectlink ?></span></td>

                    </tr>
                <?php endforeach; ?>

            <?php endif; ?>

        </tbody>
    </table>
</div>


<input type="hidden" name="absolute" value="<?= Yii::$app->recruitment->absoluteUrl() ?>">
<?php

$script = <<<JS

    $(function(){
        
   

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
    });
        
JS;

$this->registerJs($script);
