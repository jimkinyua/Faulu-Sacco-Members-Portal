<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use borales\extensions\phoneInput\PhoneInput;

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
                                    <?= $form->field($model, 'ID_No')->textInput() ?>

                                
                                </div>

                                <div class="col-md-4">

                                    <?= $form->field($model, 'Gender')->dropDownList([
                                        'Female' => 'Female',
                                        'Male' => 'Male',
                                    ],['prompt' => 'Select Gender']) ?>

                                    <?= $form->field($model, 'Date_of_Birth')->textInput(['type' => 'date']) ?>
                                   
                                   <?= $form->field($model, 'KRA_Pin')->textInput() ?>

                                   <?= $form->field($model, 'isSignatoryMember')->dropDownList([
                                    'Yes' => 'Yes',
                                    'No' => 'No',
                                 
                                    ],['prompt' => 'Select Type']) ?>



                                </div>

                                <div class="col-md-4">
 
                                    <?= $form->field($model, 'Must_Sign')->dropDownList([
                                        'Yes' => 'Yes',
                                        'No' => 'No',
                                        ],['prompt' => '-- Select Opption --']) 
                                    ?>
                                     <?= $form->field($model, 'Email')->textInput() ?>

                                     <?= $form->field($model, 'PhoneNo')->widget(PhoneInput::className(), [
                                    'jsOptions' => [
                                        'preferredCountries' => ['ke'],
                                    ]]) ?>
                                     
                                     <?= $form->field($model, 'Member_No_If_Member')->textInput(['type' => 'text']) ?>


                                </div>

                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <!-- <h5>Attachements <strong>(Ensure all your files have different names). For large file(s) compress using <a href="https://www.ilovepdf.com/compress_pdf" target="_blank" >PDF COMPRESSOR</a></strong></h5> -->
                                    <table class="table">
                                            <tr>
                                                <th></th>
                                                <th></th>
                                                <th>Current Document</th>
                                            </tr>
                                            <?php foreach($RequiredAttachements as $RequiredAttachement): ?>
                                                    <tr>
                                                        <td> <?= $RequiredAttachement['Name'] ?> <span style="font-weight: bold; color: red;"> (Mandatory)</span></td>
                                                            <?php
                                                                $required = 'required = "required"';
                                                                if($MyAttachedDocs){
                                                                    foreach($MyAttachedDocs as $MyAttachedDocument){
                                                                        if($MyAttachedDocument->FileName ==$RequiredAttachement['Name']) $required = '';
                                                                    }
                                                                }
                                                            
                                                                // echo '<pre>';
                                                                // print_r($required);
                                                                // exit;
                                                            // if($requiredDoc->Mandatory && !@$MyDocs[$requiredDoc->DocumentID]) $rrrq = 'required = "required"';
                                                            ?>
                                                        <td>
                                                            <input type="file" accept=".pdf"  name="<?=@$RequiredAttachement['Name']?>"  <?=$required ?>> 
                                                        </td>
                                                        <td>
                                                            <table class="table table-condensed" style="margin: 0px;">
                                                                <?php if($MyAttachedDocs): ?>
                                                                    <?php foreach($MyAttachedDocs as $MyAttachedDocument):?>
                                                                        <?php if($MyAttachedDocument->FileName ==$RequiredAttachement['Name']): ?>
                                                                            <tr>
                                                                                <td><?=Html::a($MyAttachedDocument->FileName,['read','Key'=> $MyAttachedDocument->Key ]);?></td>
                                                                                <!-- <td width="15%"><?=Html::a('Delete', ['delete-application-file', 'filename' => $MyAttachedDocument->Key], ['class' => 'btn btn-sm btn-danger']);?></td> -->
                                                                            </tr>
                                                                        <?php endif; ?>
                                                                    
                                                                    <?php endforeach; ?>
                                                                <?php endif; ?>                                          
                                                            </table>
                                                        </td>
                                                    </tr>
                                            <?php endforeach; ?>

                                        </table>
                                    
                                    <!-- <?= Html::submitButton('Upload Files', ['class' => 'btn btn-success']) ?> -->

                                
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
