<?php
use yii\helpers\Html;
use yii\helpers\Url;
$this->title = 'CashFlow  Information';
// echo '<pre>';
// print_r($LoanModel->getCashAnalysisInformation());
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
                <h3> CashFlow  Information</h3>
                <div class="alert " role="alert" style="background-color: darkgoldenrod;">
                    <h5 style="color: white;"> Kindly provide us with an annual summary of the incomes and expenses </h5>
                </div>

            </div>
        </div>

    <!--END THE STEPS THING--->
    <?php if($LoanModel->isNewRecord == false): ?>
            <?php if($LoanModel->Portal_Status=='Application'): ?>
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-body">
                                <?= \yii\helpers\Html::a('Add Cash Flow Information',Url::to(['create', 'LoanNo'=>$LoanModel->Application_No]),['class' => 'create btn btn-info btn-md mr-2 ']) ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
            <div class="card card-body border-light shadow-sm table-wrapper table-responsive pt-0">
                <table class="table table-hover" id="guarantors">
                    <thead>
                        <tr>
                            <th class="border-0">Type Of Transaction</th>
                            <th class="border-0">Description</th>
                            <th class="border-0">Amount</th>
                            <?php if($LoanModel->Portal_Status=='Application'): ?>
                                <th class="border-0">Action</th>
                            <?php endif; ?>

                        </tr>
                    </thead>
                    <tbody>
                    <?php if($LoanModel->getCashAnalysisInformation() ): ?>

                                    <?php foreach($LoanModel->getCashAnalysisInformation() as $CashAnalysisInformation): ?> 
                                        <tr>
                                            <td><span class="font-weight-normal"><?= @$CashAnalysisInformation->Entry_Type ?></span></td>

                                            <td><span class="font-weight-normal"><?= @$CashAnalysisInformation->Description ?></span></td>
                                            <td>
                                                <a href="#" class="d-flex align-items-center">
                                                    <div class="d-block">
                                                        <div class="small text-gray"><?= @number_format($CashAnalysisInformation->Amount) ?></div>
                                                    </div>
                                                </a>
                                            </td>
                                            
                                            <?php
                                                $updateLink = Html::a('Edit',Url::to(['update','Key'=> urlencode($CashAnalysisInformation->Key) ]) ,['class'=>'update btn btn-info btn-md']);
                                                $link = Html::a('Remove',Url::to(['delete','Key'=> urlencode($CashAnalysisInformation->Key) ]),['class'=>'btn btn-danger btn-md']);
                                            ?>
                                            <?php if($LoanModel->Portal_Status=='Application'): ?>
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








