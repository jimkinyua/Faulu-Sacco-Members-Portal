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
                                      

                                    </li>
                                        <p>
                                            <table style="border: solid;font-size: xx-small;">
                                                    <tr>
                                                        <th>Loans Amount (Kshs)</th>
                                                        <th> Up to 48 Months Minimum Contribution (Kshs)</th>
                                                        <th> Beyond 48 Months Minimum Contribution (Kshs)</th>
                                                    </tr>

                                                    <tr>
                                                        <td>Up to 500,000</td>
                                                        <td>1,600</td>
                                                        <td>2,000</td>
                                                    </tr>

                                                    <tr>
                                                        <td>500,001 - 1,000,000</td>
                                                        <td>1,600</td>
                                                        <td>4,000</td>
                                                    </tr>

                                                    <tr>
                                                        <td>1,000,001   - 1,500,000</td>
                                                        <td>3,000</td>
                                                        <td>7,500</td>
                                                    </tr>

                                                    <tr>
                                                        <td>2,000,001   - 3,000,000</td>
                                                        <td>6,000</td>
                                                        <td>10,000</td>
                                                    </tr>

                                                    <tr>
                                                        <td>3,000,001   - 4,000,000</td>
                                                        <td>10,000</td>
                                                        <td>15,000</td>
                                                    </tr>

                                                    <tr>
                                                        <td>4,000,001   - 50,000,000</td>
                                                        <td>15,000</td>
                                                        <td>20,000</td>
                                                    </tr>

                                                </table>
                                        </p>


                                    <li>
                                        <p> 2/3 rule shall apply in the loan appraisal.</p>
                                    </li>
                                    <li>
                                        <p> Outstanding loans must have been cleared/ offset before a new loan is granted OR the member allows the Sacco to offset the outstanding loans as per the standing policy guiding respective loan products. </p>
                                    </li>
                                    <li>
                                        <p> Members must have contributed for a minimum period of six consecutive months having a minimum share/deposit contribution  </p>
                                    </li>
                                    <li>
                                        <p> The guarantors must be members of the society, one can guarantee a maximum of 7 loans including theirs. </p>
                                    </li>
                                    <li>
                                        <p> Lumpsum contribution for the purpose of securing a loan can be considered only if such money remains in the Society for at least six months, OR subject to a commission between 10% to 40% commission on the lumpsum for members in good standing </p>
                                    </li>
                                    <li>
                                        <p> In case of default in payment the entire balance of the loan will immediately become due and payable at the discretion of the Board and all deposits owned by the member and held by the member and any interest and deposits due to the member will be set against the owed amount. The member will also be liable for any costs incurred in the agencies so appointed for the loan balance and accumulated interest. Any remaining balance will be deducted from the member's salary and or terminal benefits and the employer is authorized to make all necessary deduction by authority of the member's signature appended below. </p>
                                    </li>
                                    <li>
                                        <p> Members shall be required to provide email address of their bank for validation of bank statements for loans of KES. 1 million and above.</p>
                                    </li>
                                    <li>
                                        <p> All loan applicants of amounts KES. 200,000 and above will be expected to sign a direct debit Authority Form for payment. </p>
                                    </li>
                                    <li>
                                        <p> By clicking on the checkbox herein, I confirm that I am the owner of the Mhasibu Sacco Society Limited account and I further confirm acceptance and adherence to the terms and conditions above stated.</p>
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
    

           