<?php

use yii\widgets\ActiveForm;
use yii\helpers\Html;
use borales\extensions\phoneInput\PhoneInput;


$this->title = 'Member Profile';
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

<?php $form = ActiveForm::begin(); ?>

<?= $this->render('..\profile\_steps', ['model'=>$model]) ?>

    <div class="col-md-12">
    
        <div class="card card-success ">
         
            <div class="card-body">
                <?php if($model->Approval_Status == 'New'): ?>
                    <div class="row">
                        <div class=" row col-md-12">
                            <div class="col-md-6">
                                <?= $form->field($model, 'Business_Name')->textInput() ?>
                                <?= $form->field($model, 'Business_Postal_Address')->textInput() ?>
                                <?= $form->field($model, 'BusinessRegistration_No')->textInput() ?>
                            </div>
                            <div class="col-md-6">
                                <?= $form->field($model, 'Apprx_Monthly_Income')->textInput() ?>
                                <?= $form->field($model, 'Business_Physical_Location')->textInput() ?>
                                <?= $form->field($model, 'Pin_Number')->textInput() ?>                                
                            </div>

                        </div>
                    </div>

                    <?php if($model->Portal_Status == 'New'): ?>
                        <div class="row">
                            <div class="form-group">
                                <?= Html::submitButton('Save', ['class' => 'btn btn-primary']) ?>
                            </div>
                        </div>
                    <?php endif; ?>
                    <?php else: ?>
                        <div class="row">
                            <div class=" row col-md-12">
                                <div class="col-md-6">
                                        <?= $form->field($model, 'Business_Name')->textInput(['readonly'=>true]) ?>
                                        <?= $form->field($model, 'Apprx_Monthly_Income')->textInput(['readonly'=>true]) ?>
                                        <?= $form->field($model, 'BusinessRegistration_No')->textInput(['readonly'=>true]) ?>

                                </div>
                                <div class="col-md-6">
                                    <?= $form->field($model, 'Business_Postal_Address')->textInput(['readonly'=>true]) ?>
                                    <?= $form->field($model, 'Business_Physical_Location')->textInput(['readonly'=>true]) ?>
                                </div>
                            </div>
                        </div>
                <?php endif; ?>
            </div>
        </div>

        <?php ActiveForm::end(); ?>


