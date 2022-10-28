<?php
use yii\widgets\ActiveForm;
use yii\helpers\Html;

$this->title = 'Terms and Conditions';
?>
<style>
table, th, td {
  border: 1px solid black;
  border-collapse: collapse;
}
th, td {
  /* padding: 5px; */
  text-align: left;
}
</style>


    <!--THE STEPS THING--->
    <div class="row">
        <div class="col-md-12">
        <?= $this->render('.\LoanSteps', ['model'=>$LoanModel]) ?>
        </div>
    </div>



        <div class="col-md-12">

             <div class="card-body">
                <?php $form = ActiveForm::begin(['id' => 'confrimation-form']); ?>
                        <h4> Terms and Conditions </h4>
                        <p>
                            <div class="CustomList">
                                <ol style="padding-left: revert;" >
                                    <li>
                                       <CustomList> I hereby declare that the information provided in this form is true to the best of my knowledge, and I understand that any false information given could render me liable to immediate disqualification.</CustomList>
                                    </li>

                                    <li>
                                        <p> Members are limited to four times (or as may be prescribed) the sum of shares and deposit held, but subject to availability of funds. For self-guaranteed loans only, uncommitted deposits shall be considered.  </p>
                                    </li>

                                    <li>
                                        <p> A member will be required to maintain a monthly deposit contribution depending on loan repayment period and amount contribution subject to the current requirements based on loan applied for as shown below:   </p>
                                      

                                   
                                        <p> 2/3 rule shall apply in the loan appraisal.</p>
                                    </li>
                           
                                    <li>
                                        <p> By clicking on the checkbox herein, I confirm that I am the owner of the Faulu Sacco Society Limited account and I further confirm acceptance and adherence to the terms and conditions above stated.</p>
                                    </li>


                                </ol>
                            </div>
                            
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
    

           