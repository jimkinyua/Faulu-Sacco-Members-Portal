<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
?>
        
                    <h3 class="card-title">Signatory Details</h3>
              
                    <div class="card-body">
                        <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>
                        <?= $form->field($model,'Key')->hiddenInput()->label(false) ?>

                        <div class="row">
                            <div class=" row col-md-12">

                                <div class="col-md-4">
                                    <?= $form->field($model, 'First_Name')->textInput() ?>
                                    <?= $form->field($model, 'Middle_Name')->textInput() ?>
                                    <?= $form->field($model, 'Last_Name')->textInput() ?>
                                
                                </div>

                                <div class="col-md-4">

                                    <?= $form->field($model, 'Gender')->dropDownList([
                                        '_blank_' => '_blank_',
                                        'Female' => 'Female',
                                        'Male' => 'Male',
                                    ],['prompt' => 'Select Gender']) ?>

                                    <?= $form->field($model, 'Date_of_Birth')->textInput(['type' => 'date']) ?>

                                    <?= $form->field($model, 'isSignatoryMember')->dropDownList([
                                    'Yes' => 'Yes',
                                    'No' => 'No',
                                 
                                    ],['prompt' => 'Select Type']) ?>


                                </div>

                                <div class="col-md-4">
                                    <?= $form->field($model, 'Must_Be_Present')->dropDownList([
                                        'Yes' => 'Yes',
                                        'No' => 'No',
                                        ],['prompt' => '-- Select Opption --']) 
                                    ?>
                                    <?= $form->field($model, 'Must_Sign')->dropDownList([
                                        'Yes' => 'Yes',
                                        'No' => 'No',
                                        ],['prompt' => '-- Select Opption --']) 
                                    ?>
                                    
                                    <?= $form->field($model, 'Member_No_If_Member')->dropDownList(ArrayHelper::map($Members, 'Code', 'Name'),['prompt' => 'Member']) ?>

                                </div>

                            </div>
                        </div>
                        <div class="row">

<div class="form-group">
    <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
</div>


</div>
                    </div>
                    
             </div>
            

   










        <?php ActiveForm::end(); ?>
    </div>
</div>
