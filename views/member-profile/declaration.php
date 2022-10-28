<?php
use yii\widgets\ActiveForm;
use yii\helpers\Html;

$this->title = 'Terms and Conditions';

if(Yii::$app->session->hasFlash('success')){
    print ' <div class="alert alert-success alert-dismissable">
                     <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                            <h5><i class="icon fas fa-check"></i> Success!</h5>
';
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

    <!--THE STEPS THING--->
    <div class="row">
        <div class="col-md-12">
            <?= $this->render('..\profile\_steps', ['model'=>$model]) ?>
        </div>
    </div>



        <div class="col-md-12">

             <div class="card-body">
                <?php $form = ActiveForm::begin(['id' => 'confrimation-form']); ?>
                   <?php if($model->Member_Category == Yii::$app->params['SystemConfigs']['GroupAccount']): ?>
                    <!-- <p> -->
                            <div class="CustomList">
                            <p> n accordance with the Co-operative Act, members of the Society including groups & corporate members are expected to; </p>

                                <ol style="padding-left: revert;" >
                                    <li>
                                        <p> Observe the law, the rules and the by-laws whenever transacting any business with the society.</p>

                                        </li>
                                    <li>
                                        <p> Pay their debt obligations to the society without fail and save regularly with the society to mobilize funds for lending to the members.  </p>
                                    </li>

                                    <li>
                                        <p> Liable for the society’s indebtedness in case of insolvency in accordance with the Act and the by laws.  </p>
                                      
                                    </li>
                                        

                                    <li>
                                        <p> Refrain from engaging in the business of money lending in competition with the society. </p>
                                    </li>
                                    <li>
                                        <p> Protect the image of the society and avoid unnecessary publicity, incitement or careless talk that can injure the reputation of the society.</p>
                                    </li>
                                    <li>
                                        <p> Support issues put forth that improve the sustainability of the Society and promote the goodwill of all members.  </p>
                                    </li>
                                    
                                    <li>
                                        <p> Comply with the By-laws, the Co-operative Societies Act, SACCO Act, Rules and Regulations and General Meeting Resolutions. </p>
                                    </li>
                                    


                                </ol>
                            </div>
                            <p> By clicking on the checkbox herein, I confirm that I am the owner of the Mhasibu Sacco Society Limited account and I further confirm acceptance and adherence to the terms and conditions above stated.</p>

                            
                        <!-- </p> -->
                   <?php endif; ?>

                    <div class="row">
                        <div class="row col-md-12">
                        <?= $form->field($model, 'Key')->hiddenInput()->label(false); ?>

                        <hr>
                            <div class="col-lg-5">
                                <?= $form->field($model, 'AcceptTerms')->checkBox([]) ?>
                            </div>
                        
                            
                    
                        </div>
                        <div class="row">
                        <div class="col-lg-12">
                                     <?= Html::submitButton('Submit Profile', ['class' => 'btn btn-success form-control', 'name' => 'login-button']) ?>
                            </div>
                        </div>
                    </div>

                   


                <?php ActiveForm::end(); ?>

            </div>
        </div>
    

           