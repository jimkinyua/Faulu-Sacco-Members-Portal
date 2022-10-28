<?php

use yii\widgets\ActiveForm;
$this->title = 'Member Profile';
// echo '<pre>';
// print_r($model);
// exit;
?>
  

    <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data','autocomplete' => 'off']]); ?>
        <div class="row justify-content-center">
            <div class="col col-md-12">
                <!--Accordion-->
              
    
                <div class="accordion shadow-soft">
                    <div class="card card-sm card-body border-soft mb-0">
                        <a href="#panel-what-is-pixel" data-target="#panel-what-is-pixel" class="accordion-panel-header" data-toggle="collapse" role="button" aria-expanded="true" aria-controls="panel-what-is-pixel">
                            <span class="icon-title h4 mb-0 font-weight-bold">General Details</span>
                            <span class="icon"><i class="fas fa-plus"></i></span>
                        </a>

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

                        <div class="expand" id="panel-what-is-pixel">
                            <div class="pt-3">
                                <div class="row">
                                    <div class="col-md-6">
                                        <?= $form->field($model, 'Full_Name')->textInput(['readonly'=>true]) ?>
                                        <?= $form->field($model, 'Nationality')->textInput(['readonly'=>true]) ?>
                                        <?= $form->field($model, 'National_ID_No')->textInput(['readonly'=>true]) ?>
                                        <?= $form->field($model, 'Date_of_Birth')->textInput(['readonly'=>true]) ?>

                                    </div>

                                    <div class="col-md-6">
                                        <?= $form->field($model, 'Pin_Number')->textInput(['readonly'=>true]) ?>
                                        <!-- <?= $form->field($model, 'Marital_Status')->textInput(['readonly'=>true]) ?> -->
                                        <?= $form->field($model, 'Gender')->textInput(['readonly'=>true]) ?>
                                        <?= $form->field($model, 'Application_No')->textInput(['readonly'=>true]) ?>
                                        <?= $form->field($model, 'Occupation')->textInput(['readonly'=>true]) ?>
                                        
                                    </div>
                                </div>
                               

                            </div>
                        </div>
                    </div>
                    <div class="card card-sm card-body border-soft mb-0">
                        <a href="#panel-clients" data-target="#panel-clients" class="accordion-panel-header" data-toggle="collapse" role="button" aria-expanded="true" aria-controls="panel-clients">
                            <span class="icon-title h4 mb-0 font-weight-bold">Communication Details</span>
                            <span class="icon"><i class="fas fa-plus"></i></span>
                        </a>
                        <div class="expand" id="panel-clients">
                            <div class="pt-3">
                                <div class="row">

                                    <div class="col-md-6">
                                        <?= $form->field($model, 'Phone_No')->textInput(['readonly'=>true]) ?>
                                        <?= $form->field($model, 'Mobile_Phone_No')->textInput(['readonly'=>true]) ?>
                                    </div>

                                    <div class="col-md-6">
                                        <?= $form->field($model, 'E_Mail')->textInput(['readonly'=>true]) ?>
                                        <?= $form->field($model, 'SMS_Notification_Number')->textInput(['readonly'=>true]) ?>

                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="card card-sm card-body border-soft mb-0">
                        <a href="#panel-clients" data-target="#NextofKins" class="accordion-panel-header" data-toggle="collapse" role="button" aria-expanded="true" aria-controls="panel-clients">
                            <span class="icon-title h4 mb-0 font-weight-bold">Next of Kins </span>
                            <span class="icon"><i class="fas fa-plus"></i></span>
                        </a>
                        <br>
                        <div class="expand" id="NextofKins">
                        <div class="table-responsive">
                            <table class="table table-centered table-nowrap mb-0 rounded">
                                <thead class="thead-brown">
                                    <tr>
                                        <th class="border-0">First Name</th>
                                        <th class="border-0">Last Name</th>
                                        <th class="border-0">Allocation Percent</th>
                                        <th class="border-0">Date of Birth </th>
                                        <th class="border-0">Relationship</th>

                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Item -->
                                    <?php if([]): ?>
                                        <?php foreach([] as $NextOfKin): ?>
                                            <tr>

                                                <td class="border-1">                                             
                                                   
                                                    <div><span class="h6"><?= isset($NextOfKin->First_Name)?strtoupper($NextOfKin->First_Name):'' ?></span></div>
                                                
                                                </td>

                                                <td class="border-1 ">
                                                    <div><span class="h6"><?= isset($NextOfKin->Last_Name)?strtoupper($NextOfKin->Last_Name):'' ?></span></div>
                                                </td>

                                                <td class="border-1">
                                                    <div><span class="h6"><?= isset($NextOfKin->Allocation_Percent)?strtoupper($NextOfKin->Allocation_Percent):'' ?></span></div>
                                                </td>

                                                                                                
                                                <td class="border-1">
                                                    <div><span class="h6"><?= isset($NextOfKin->DOB)?date_format( date_create($NextOfKin->DOB), 'l jS F Y'):'Not Set' ;  ?></span></div>
                                                </td>
                                                
                                                <td class="border-1">
                                                    <div><span class="h6"><?= isset($NextOfKin->Type)?strtoupper($NextOfKin->Type):'' ?></span></div>
                                                </td>

                                               
                                                
                                            </tr>

                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                  
                                    <!-- End of Item -->

                                </tbody>
                            </table>
                        </div>

                        </div>
                    </div>

                                   
                </div>
                <!--End of Accordion-->
            </div>
        </div>
    <?php ActiveForm::end(); ?>




        <?php

$script = <<<JS

    $(function(){
        $('.field-Consituencies_Code').tooltip({'trigger':'focus', 'title': 'More Info About Constituicies Here'});
    });
        
JS;

$this->registerJs($script);



