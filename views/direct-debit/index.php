<?php
use yii\helpers\Html;
use yii\helpers\Url;
$this->title = 'Direct Debit Information';
// echo '<pre>';
// print_r($LoanModel->getDirectDebitInformation());
// exit;
?>
  <div class="row">
        <div class="col-md-12">
            <?php

            if(Yii::$app->session->hasFlash('success')){
                print ' <div class="alert alert-success alert-dismissable">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                            <h5><i class="icon fas fa-check"></i> Success!</h5>';
                echo Yii::$app->session->getFlash('success');
                print '</div>';
            }else if(Yii::$app->session->hasFlash('error')){
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
                <?= $this->render('..\loan\LoanSteps', ['model'=>$LoanModel]) ?>
                <h3> Bank Details </h3>
            </div>
        </div>

    <!--END THE STEPS THING--->
    <?php if($LoanModel->isNewRecord == false): ?>
            <?php if($LoanModel->Approval_Status=='New'): ?>
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-body">
                                <?= \yii\helpers\Html::a('Add Bank Details',Url::to(['direct-debit/create', 'LoanNo'=>$LoanModel->Application_No]),['class' => 'create btn btn-info btn-md mr-2 ']) ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
            <div class="card card-body border-light shadow-sm table-wrapper table-responsive pt-0">
                <table class="table table-hover" id="guarantors">
                    <thead>
                        <tr>
                            <th class="border-0">Bank Name</th>
                            <th class="border-0">Branch</th>
                            <th class="border-0"> Account No </th>
                            <th class="border-0"> Amount </th>									
                            <?php if($LoanModel->Approval_Status=='New'): ?>
                                <th class="border-0">Action</th>
                            <?php endif; ?>

                        </tr>
                    </thead>
                    <tbody>
                    <?php if($LoanModel->getDirectDebitInformation() ): ?>

                                    <?php foreach($LoanModel->getDirectDebitInformation() as $DirectDebitInformation): ?>
                                        <?php if(empty($DirectDebitInformation->Bank_Code)): ?> 
                                            <?php continue; ?>
                                        <?php endif; ?>
                                        <tr>
                                            <td><span class="font-weight-normal"><?= @$DirectDebitInformation->Bank_Code ?></span></td>

                                            <td>
                                                <div class="font-weight-normal"><?= @$DirectDebitInformation->Branch_Code ?></div>
                                            </td>
                                            <td><span class="font-weight-normal"><?= @$DirectDebitInformation->Account_No ?></span></td>
                                            <td><span class="font-weight-normal"><?= @number_format($DirectDebitInformation->Amount) ?></span></td>

                                            <?php
                                                $updateLink = Html::a('Edit',Url::to(['update','Key'=> urlencode($DirectDebitInformation->Key) ]) ,['class'=>'update btn btn-info btn-md']);
                                                $link = Html::a('Remove',Url::to(['delete','Key'=> urlencode($DirectDebitInformation->Key) ]),['class'=>'btn btn-danger btn-md']);
                                            ?>
                                            <?php if($LoanModel->Approval_Status=='New'): ?>
                                                <td><span class="font-weight-normal"><?= $updateLink .' '. $link ?></span></td>                                                                            
                                            <?php endif; ?>

                                        </tr>
                                    <?php endforeach;?>
                                        
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
    <?php endif; ?>


<input type="hidden" name="absolute" value="<?= Yii::$app->recruitment->absoluteUrl() ?>">
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
    });
        
JS;

$this->registerJs($script);








