<?php
use yii\widgets\ActiveForm;
use yii\helpers\Html;

$this->title = 'Terms and Conditions';
?>

    <!--THE STEPS THING--->
    <div class="row">
        <div class="col-md-12">
        <?= $this->render('.\LoanSteps', ['model'=>$LoanModel]) ?>
        </div>
    </div>



        <div class="col-md-12">

             <div class="card-body">
                <?php $form = ActiveForm::begin(['id' => 'confrimation-form']); ?>

                   <p>I hereby declare that the information provided in this form is true to the best of my knowledge, and I understand that any false information given could render me liable to immediate disqualification.

                    </p>
                    <p>
                        <ol>

                        <li> <b> ACCURACY OF CONTENT:</b> The content of this application 
                                is accurate and contains no false information.
                        </li>

                        <li> <b> Contact Information: </b> You are aware Mhasibu Sacco will contact Guarantors,
                            if applicable, regarding your suitability. Finally you understand that submission of false information or misrepresentation
                                and/or submission of falsified documentation constitutes serious misconduct for which sever disciplinary
                            sanctions can be imposed. I consent to all of 
                            the foregoing as part of the process of evaluation of my application
                        </li>

                        </ol>
                    </p>

                    <div class="row">
                        <div class="row col-md-12">
                        <?= $form->field($model, 'Key')->hiddenInput()->label(false); ?>
                        <?= $form->field($model,'Application_No')->hiddenInput()->label(false) ?>

                        <hr>
                            <div class="col-lg-5">
                                <?= $form->field($model, 'AgreedToTerms')->checkBox([ 'style'=>'zoom:2.5;']) ?>
                            </div>
                        
                            
                     

                        </div>
                        <div class="row">
                        <div class="col-lg-12">
                                     <?= Html::submitButton('Submit Loan Application', ['class' => 'btn btn-success form-control', 'name' => 'login-button']) ?>
                            </div>
                        </div>
                    </div>

                   


                <?php ActiveForm::end(); ?>

            </div>
        </div>
    

           