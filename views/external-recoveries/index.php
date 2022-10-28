<?php
use yii\helpers\Html;
use yii\helpers\Url;
$this->title = 'Loans From Other Institutions ';
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
                <h3> <?= $this->title ?> </h3>
            </div>
        </div>

    <!--END THE STEPS THING--->
    <?php if($LoanModel->isNewRecord == false): ?>
            <?php if($LoanModel->Approval_Status=='New'): ?>
                <div class="row">
                    <div class="card">
                        <div class="card-header">
                            <div class="alert " role="alert" style="background-color: darkgoldenrod;">
                            <h5 style="color: white;"> Add details of loans with other instituitions that you would wish the Sacco to offset  </h5>
                        </div>
                    </div>

                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-body">
                                <?= \yii\helpers\Html::a('Add Loan From Another Instituition',Url::to(['create', 'LoanNo'=>$LoanModel->Application_No]),['class' => 'create btn btn-info btn-md mr-2 ']) ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
            <div class="card card-body border-light shadow-sm table-wrapper table-responsive pt-0">
                <table class="table table-hover" id="guarantors">
                    <thead>
                        <tr>
                            <th class="border-0"> Code</th>
                            <th class="border-0">Description</th>
                            <th class="border-0"> Amount </th>
                            <?php if($LoanModel->Approval_Status=='New'): ?>
                                <th class="border-0">Action</th>
                            <?php endif; ?>
                        </tr>
                    </thead>
                    <tbody>
                    <?php if($LoanModel->getExternalRecoveryInformation() ): ?>

                                    <?php foreach($LoanModel->getExternalRecoveryInformation() as $ExternalRecoveryInformation): ?>
                                        <?php if(empty($ExternalRecoveryInformation->Recovery_Code)|| empty($ExternalRecoveryInformation->Description)): ?> 
                                            <?php continue; ?>
                                        <?php endif; ?>
                                        <tr>
                                            <td><span class="font-weight-normal"><?= @$ExternalRecoveryInformation->Recovery_Code ?></span></td>

                                            <td>
                                                <div class="font-weight-normal"><?= @$ExternalRecoveryInformation->Description ?></div>
                                            </td>
                                            <td><span class="font-weight-normal"><?= @number_format($ExternalRecoveryInformation->Amount) ?></span></td>

                                            <?php
                                                $updateLink = Html::a('Edit',Url::to(['update','Key'=> urlencode($ExternalRecoveryInformation->Key) ]) ,['class'=>'update btn btn-info btn-md']);
                                                $link = Html::a('Remove',Url::to(['delete','Key'=> urlencode($ExternalRecoveryInformation->Key) ]),['class'=>'btn btn-danger btn-md']);
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








