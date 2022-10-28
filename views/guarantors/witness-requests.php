<?php

use yii\bootstrap4\Html;
use yii\helpers\Url;

$this->title = 'Witness Requests';
// echo '<pre>';
// print_r($GuarantorSubstituitionRequests);
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
<h1> Witness Requests </h1>
<div class="card card-body border-light shadow-sm table-wrapper table-responsive pt-0">
    <table class="table table-hover" id="leaves">
        <thead>
            <tr>
                <th class="border-0">Loan No</th>
                <th class="border-0">Loan Amount</th>
                <th class="border-0"> Loanee Name </th>
                <th class="border-0">Application Date</th>
                <th class="border-0">Loan Product Name</th>
                <th class="border-0">Action</th>

            </tr>
        </thead>
        <tbody>

            <?php if (!is_object($WitnessRequests)) : ?>

                <?php foreach ($WitnessRequests as $WitnessRequest) : ?>
                    <tr>
                        <td><span class="font-weight-normal"><?= @$WitnessRequest->Loan_No ?></span></td>
                        <td><span class="font-weight-normal"><?= @number_format($WitnessRequest->AppliedAmount) ?></span></td>
                        <td><span class="font-weight-normal"><?= @$WitnessRequest->ApplicantName ?></span></td>
                        <td><span class="font-weight-normal"><?= @$WitnessRequest->Application_Date ?></span></td>
                        <td><span class="font-weight-normal"><?= @$WitnessRequest->Product_Name ?></span></td>
                        <?php
                        $acceptLink = Html::a(
                            'Confrim',
                            Url::to(['guarantors/accept-witness', 'Key' => urlencode($WitnessRequest->Key), 'Accept' => 1]),
                            ['class' => 'btn btn-info btn-md', 'data' => [
                                'confirm' => 'Are you sure you want to Accept?',
                                'method' => 'POST',
                            ]]
                        );
                        $rejectlink = Html::a(
                            'Reject',
                            Url::to(['guarantors/reject-witness', 'Key' => urlencode($WitnessRequest->Key), 'Accept' => 0]),
                            [
                                'class' => 'btn btn-danger btn-md', 'data' => [
                                    'confirm' => 'Are you sure you want to Reject?',
                                    'method' => 'Post',
                                ]
                            ]
                        );
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
