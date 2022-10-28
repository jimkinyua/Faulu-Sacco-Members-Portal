<?php
use yii\helpers\Html;
use yii\helpers\Url;
$this->title = 'CRB Clearance Certificate';
// echo '<pre>';
// print_r($LoanModel->getCRBClearanceCertificates());
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
                <h3> CRB Clearance Certificate </h3>
                <div class="alert " role="alert" style="background-color: pink;">
                    <h5> <?= ucwords('Seems you have been Blacklisted by CRB, Kindly Attach a CRB Clearance Certificate Before Submitting The Loan Application') ?> </h5>
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
                                <?= \yii\helpers\Html::a('Attach CRB Clearance Certificate',Url::to(['create', 'Key'=>$LoanModel->Key]),['class' => 'create btn btn-info btn-md mr-2 ']) ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
            <div class="card card-body border-light shadow-sm table-wrapper table-responsive pt-0">
                <table class="table table-hover" id="guarantors">
                    <thead>
                        <tr>
                            <th class="border-0">Description</th>
                            <?php if($LoanModel->Portal_Status=='Application'): ?>
                                <th class="border-0">Action</th>
                            <?php endif; ?>
                        </tr>
                    </thead>
                    <tbody>
                    <?php if($LoanModel->getCRBClearanceCertificates() ): ?>

                                    <?php foreach($LoanModel->getCRBClearanceCertificates() as $CRBClearanceCertificate): ?>
                                        <?php if(empty($CRBClearanceCertificate->LineNo)): ?> 
                                            <?php continue; ?>
                                        <?php endif; ?>
                                        <tr>
                                            <td><span class="font-weight-normal">CRB Certificate </span></td>
                                            <?php
                                            $link = Html::a('View Attachement',['read','No'=> $CRBClearanceCertificate->LineNo, 'Key'=>$LoanModel->Key ],['title'=>'View Attachement','class'=>'btn btn-success btn-md']);
                                            $deleteLink = Html::a('Delete Attachement',['delete','Key'=> $CRBClearanceCertificate->Key],['class'=>'btn btn-danger btn-md']);
                                            ?>
                                            <?php if($LoanModel->Portal_Status=='Application'): ?>
                                                <td><span class="font-weight-normal"><?= $link .' '. $deleteLink ?></span></td>                                                                            
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








