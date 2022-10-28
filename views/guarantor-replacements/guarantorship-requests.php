<?php
use yii\helpers\Html;
use yii\helpers\Url;
$this->title = 'Loan Guarantors';
// echo '<pre>';
// print_r($LoanModel->getGuarantors());
// exit;
?>
<h1> Guarantorship Requests </h1>
            <div class="card card-body border-light shadow-sm table-wrapper table-responsive pt-0">
                <table class="table table-hover" id="leaves">
                    <thead>
                        <tr>
                            <th class="border-0">Loan No</th>
                            <th class="border-0">Loan Amount</th>
                            <th class="border-0"> Requester </th>												
                            <th class="border-0">Loan Principal</th>	
                            <th class="border-0">Action</th>

                        </tr>
                    </thead>
                    <tbody>
                    <?php if(!is_object($GuarantorshipRequests)): ?>

                                    <?php foreach($GuarantorshipRequests as $GuarantorshipRequest): ?> 
                                        <tr>
                                            <td><span class="font-weight-normal"><?= @$GuarantorshipRequest->Loan_No ?></span></td>
                                            <td><span class="font-weight-normal"><?= @number_format($GuarantorshipRequest->AppliedAmount) ?></span></td>
                                            <td><span class="font-weight-normal"><?= @$GuarantorshipRequest->ApplicantName ?></span></td>
                                            <td><span class="font-weight-normal"><?= @number_format($GuarantorshipRequest->Loan_Principal) ?></span></td>
                                            <?php
                                                $acceptLink = Html::a('Accept',Url::to(['guarantors/accept','Key'=> urlencode($GuarantorshipRequest->Key) ]) ,['class'=>'update btn btn-info btn-md']);
                                                $rejectlink = Html::a('Reject',Url::to(['guarantors/reject','Key'=> urlencode($GuarantorshipRequest->Key) ]), ['class'=>'update btn btn-danger btn-md']);
                                            ?>
                                                <td><span class="font-weight-normal"><?= $acceptLink .' '. $rejectlink ?></span></td>                                                                            

                                        </tr>
                                    <?php endforeach;?>
                                        
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








