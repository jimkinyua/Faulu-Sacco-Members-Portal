<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use borales\extensions\phoneInput\PhoneInput;

?>
        
                    <h3 class="card-title">Parent Details</h3>
              
                    <div class="card-body">
                        <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>
                        <?= $form->field($model,'Key')->hiddenInput()->label(false) ?>

                        <div class="row">
                            <div class=" row col-md-12">

                                <div class="col-md-6">
                                    <?= $form->field($model, 'First_Name')->textInput() ?>
                                    <?= $form->field($model, 'Surname')->textInput() ?>
                                    <?= $form->field($model, 'Other_Names')->textInput() ?>
                                
                                </div>

                                <div class="col-md-6">

                                    <?= $form->field($model, 'National_ID_No')->textInput(['type'=>'text']) ?>

                                    <?= $form->field($model, 'Mobile_Phone')->widget(PhoneInput::className(), [
                                    'jsOptions' => [
                                        'preferredCountries' => ['ke'],
                                    ]]) ?>

                                     <?= $form->field($model, 'Membership_No')->textInput(['type'=>'text']) ?>


                                </div>

                            </div>
                        </div>
                            <div class="row">
                                <div class="form-group">
                                    <?= Html::submitButton('Save', ['class' => 'btn btn-primary']) ?>
                                </div>
                            </div>
                        
                    </div>
                    
             </div>
            

   










        <?php ActiveForm::end(); ?>
    </div>
</div>
