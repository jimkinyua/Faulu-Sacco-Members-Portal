<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use borales\extensions\phoneInput\PhoneInput;

?>
  <div class="row">
        <div class="col-md-12">
        </div>
    </div>
    
            <h4>Please fill out the following fields to help us to find Your Account </h4>

        <div class="card-body">
            <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data', 'autocomplete' => 'off']]); ?>
                <div class="row"> 
                    <div class="col-md-6">
                        <?= $form->field($model, 'memebershipType')->dropDownList(ArrayHelper::map($MembershipTypes, 'Code', 'Name'), ['prompt' => 'Select Memebership Type']) ?>      
                    </div>
                    <div class="col-md-6">
                        <?= $form->field($model, 'phoneNo')->widget(PhoneInput::className(), [
                                    'jsOptions' => [
                                        'preferredCountries' => ['ke'],
                                    ]]) ?>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group">
                        <?= Html::submitButton('Submit', ['class' => 'btn btn-success']) ?>
                    </div>

                    <div class="form-group">
                    </div>
                </div>
            <?php ActiveForm::end(); ?>
        </div>
                       

