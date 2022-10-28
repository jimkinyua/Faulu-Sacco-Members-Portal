<?php
/**
 * Created by PhpStorm.
 * User: HP ELITEBOOK 840 G5
 * Date: 2/26/2020
 * Time: 5:41 AM
 */




use yii\helpers\Html;
use yii\widgets\ActiveForm;
//$this->title = 'AAS - Employee Profile'
?>
             <?= $this->render('_steps') ?>
            
             <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Personal Details</h3>
                </div>
                <div class="card-body">
                    <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>
                    <?= $form->field($model,'Key')->hiddenInput()->label(false) ?>

                    <div class="row">
                        <div class=" row col-md-12">

                            <div class="col-md-4">
                                <?= $form->field($model, 'First_Name')->textInput() ?>
                                <?= $form->field($model, 'Middle_Name')->textInput() ?>
                                <?= $form->field($model, 'Last_Name')->textInput() ?>
                                <?= $form->field($model, 'Marital_Status')->dropDownList([
                                    'Single' => 'Single',
                                    'Married' => 'Married',
                                    'Separated' => 'Separated',
                                    'Divorced' => 'Divorced',
                                    'Widow_er' => 'Widow_er',
                                    'Other' => 'Other'
                                ],['prompt' => 'Select Status']) ?>
                            </div>

                            <div class="col-md-4">

                                <?= $form->field($model, 'Initials')->textInput() ?>
                                <?= $form->field($model, 'Full_Name')->textInput(['readonly'=> true, 'disabled'=>true]) ?>
                                <?= $form->field($model, 'Gender')->dropDownList([
                                    '_blank_' => '_blank_',
                                    'Female' => 'Female',
                                    'Male' => 'Male',
                                ],['prompt' => 'Select Gender']) ?>

                                <?= $form->field($model, 'Birth_Date')->textInput(['type' => 'date']) ?>


                            </div>

                            <div class="col-md-4">
                                <?= $form->field($model, 'NHIF_Number')->textInput() ?>
                                <?= $form->field($model, 'NSSF_Number')->textInput() ?>
                                <?= $form->field($model, 'KRA_Number')->textInput() ?>
                                <?= $form->field($model, 'National_ID')->textInput() ?>
                            </div>
                        </div>
                    </div>
              </div> </div>
            

         
    

        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Communication Details</h3>
            </div>
            <div class="card-body">

                <div class="row">
                    <div class=" row col-md-12">
                        <div class="col-md-4">
                            <?= $form->field($model, 'Address')->textInput(['placeholder' => 'Postal Address']) ?>
                            <?= $form->field($model, 'Country_Region_Code')->dropDownList($countries, ['prompt' => 'Select Country of Origin..']) ?>
                            <?= $form->field($model, 'City')->dropDownList($countries, ['prompt' => 'Select City..']) ?>

                        </div>
                        <div class="col-md-4">
                             <?= $form->field($model, 'Post_Code')->dropDownList($countries, ['prompt' => 'Select Post Code..']) ?>
                            <?= $form->field($model, 'County')->textInput(['placeholder'=> 'County']) ?>
                            <?= $form->field($model, 'Phone_No')->textInput(['placeholder'=> 'Phone Number']) ?>
                          

                        </div>

                        <div class="col-md-4">

                            <?= $form->field($model, 'Mobile_Phone_No')->textInput(['placeholder '=> 'Cell Number']) ?>
                            <?= $form->field($model, 'E_Mail')->textInput(['placeholder'=> 'E-mail Address', 'type' => 'email']) ?>


                        </div>
                    </div>
                </div>







            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Other Details</h3>
            </div>
            <div class="card-body">

                <div class="row">
                    <div class=" row col-md-12">
                        <!-- <div class="col-md-6">

                            <?= $form->field($model, 'imageFile')->fileInput(['accept' => 'image/*']) ?>
                            <div class="image">
                                <?php if(!empty($model->ImageUrl)){
                                    print '<img src="'.Yii::$app->recruitment->absoluteUrl().$model->ImageUrl.'">';
                                } ?>
                            </div> -->






                        </div>
                        <div class="col-md-6">
                            <?= $form->field($model,'Key')->hiddenInput()->label(false) ?>

                            <!-- <?= $form->field($model, 'Disabled')->checkbox() ?> -->
                            <?= $form->field($model, 'Disabled')->dropDownList([
                                    '1' => 'Yes',
                                    '0' => 'No',
                                ],['prompt' => 'Select ...']) ?>

                            <?= $form->field($model, 'Describe_Disability')->textInput() ?>
                         


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
