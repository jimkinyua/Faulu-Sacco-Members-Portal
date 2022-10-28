<?php

use yii\widgets\ActiveForm;
use yii\helpers\Html;
use kartik\depdrop\DepDrop;
use yii\helpers\Url;

$this->title = 'Bank Details';
?>

<?php $form = ActiveForm::begin(); ?>
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

<?= $this->render('..\profile\_steps', ['model'=>$Applicant]) ?>

    <div class="col-md-12">
    
        <div class="card card-success ">
         
            <div class="card-body">
            <?php if($Applicant->Portal_Status == 'New'): ?>
                    <div class="row">
                        <div class=" row col-md-12">

                            <div class="col-md-4">
                                <?= $form->field($model, 'Bank_Code')->dropDownList($Banks, ['prompt' => '-- Select Option --', 'id'=>'Bank_Code']) ?>

                            </div>

                            <div class="col-md-4">                               
                            <?= $form->field($model, 'Branch_Code')->widget(DepDrop::classname(), [
                                        'options'=>['id'=>'Account_Type_ID'],
                                        'pluginOptions'=>[
                                            'depends'=>['Bank_Code'],
                                            'placeholder'=>'Select...',
                                            'url'=>Url::to(['/bank-details/get-bank-branches'])]
                                    ]); ?> 
                            </div>

                            <div class="col-md-4">
                                <?= $form->field($model, 'Account_No')->textInput() ?>
                            </div>
                            <?php if($Applicant->Member_Category == Yii::$app->params['SystemConfigs']['GroupAccount']): ?>
                                <div class="col-md-4">
                                
                                    <?= $form->field($model, 'Signing_Instructions')->dropDownList([
                                        'One_to_Sign'=>'One Signatory To Sign',
                                        'Two_to_Sign'=>'Two Signatories To Sign',
                                        'Three_to_Sign'=>'Three Signatories To Sign',
                                        'One_to_Sign'=>'All Signatories To Sign',
                                        ],['prompt' => '-- Select Option --']) ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group">
                            <?= Html::submitButton('Save', ['class' => 'btn btn-primary']) ?>
                        </div>
                    </div>
                    <?php else: ?>
                        <div class="row">
                            <div class=" row col-md-12">

                                <div class="col-md-4">
                                    <?= $form->field($model, 'Bank_Code')->dropDownList([
                                        'Single' => 'Single',
                                        'Married' => 'Married',
                                        'Separated' => 'Separated',
                                        'Divorced' => 'Divorced',
                                        'Widow_er' => 'Widow_er',
                                        'Other' => 'Other'
                                    ],['prompt' => 'Select Status', 'disabled'=>true]) ?>

                                </div>

                                <div class="col-md-4">
                                    <?= $form->field($model, 'Branch_Code')->textInput(['readonly'=>true]) ?>
                                </div>

                                <div class="col-md-4">
                                    <?= $form->field($model, 'Account_No')->textInput(['readonly'=>true]) ?>
                                </div>
                            </div>
                        </div>
                <?php endif; ?>
            </div>
        </div>

        <?php ActiveForm::end(); ?>


