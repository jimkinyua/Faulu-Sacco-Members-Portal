<?php
/**
 * Created by PhpStorm.
 * User: HP ELITEBOOK 840 G5
 * Date: 2/26/2020
 * Time: 5:41 AM
 */




use yii\helpers\Html;
use yii\widgets\ActiveForm;
use borales\extensions\phoneInput\PhoneInput;

//$this->title = 'AAS - Employee Profile'
?>
        
                    <h3 class="card-title">Nominee Details</h3>
              
                    <div class="card-body">
                        <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>
                        <?= $form->field($model,'Key')->hiddenInput()->label(false) ?>

                        <div class="row">
                            <div class=" row col-md-12">

                                <div class="col-md-6">
                                    <?= $form->field($model, 'FullName')->textInput() ?>
                                    <?= $form->field($model, 'Type')->dropDownList([
                                    'National_ID' => 'National ID',
                                    'Birth_Certificate'=> 'Birth Certificate',
                                    'Passport_No'=> 'Passport',
                                    ],['prompt' => '-- Select Option --']) 
                                    ?>

                                    <?= $form->field($model, 'Email')->textInput(['type'=>'email']) ?>
                                
                                </div>

                                <div class="col-md-6">

                                    <?= $form->field($model, 'Relationship')->dropDownList([
                                    'Mother' => 'Mother',
                                    'Father'=> 'Father',
                                    'Spouse' => 'Spouse',
                                    'Daughter' => 'Daughter',
                                    'Son' => 'Son',
                                    'Brother' => 'Brother',
                                    'Sister' => 'Sister',
                                    'Aunt' => 'Aunt',
                                    'Aunt' => 'Uncle',
                                    'Friend' => 'Friend',
                                    ],['prompt' => '-- Select Option --']) 
                                    ?>

                                    <?= $form->field($model, 'National_ID_No')->widget(\yii\widgets\MaskedInput::class, ['mask' => '99999999',])?>

                                    <?= $form->field($model, 'Percent_Allocation')->textInput(['type' => 'number', 'maxlength' => true]) ?>
                                    <?= $form->field($model, 'Phone_No')->widget(PhoneInput::className(), [
                                    'jsOptions' => [
                                        'preferredCountries' => ['ke'],
                                    ]]) ?>

                                    

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
                                                            <input type="file" accept=".jpg,.png,.pdf,.xlsx,.docx,.xls"  name="<?=@$RequiredAttachement['Name']?>"  <?=$required ?>> 
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
