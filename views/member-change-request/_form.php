<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
$this->title = 'Change Request';

?>
        
                    <!-- <h3 class="card-title">Guarantor/Security Details</h3> -->
              
                    <div class="card-body">
                        <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>
                        <?= $form->field($model,'Key')->hiddenInput()->label(false) ?>
                        <?= $form->field($model,'Document_No')->hiddenInput()->label(false) ?>
                        
                        <h3> General Details </h3>
                        <div class="row">
                            <div class=" row col-md-12">
                                <div class=" row col-md-12">

                                    <div class="col-md-6">
                                        <?= $form->field($model, 'First_Name')->textInput() ?>
                                        <?= $form->field($model, 'E_Mail_Address')->textInput(['placeholder'=> 'E-mail Address', 'type' => 'email']) ?>
                                       
                                    </div>

                                    <div class="col-md-6">
                                        <?= $form->field($model, 'Mobile_Phone_No')->textInput(['placeholder'=> 'Phone Number']) ?>
                                        <?= $form->field($model, 'National_ID_No')->textInput(['placeholder'=> 'Phone Number']) ?>                                        
                                  
                                    </div>

                                    
                                </div>
                            </div>
                        </div>

                        <!-- <h3> Communication Details <h3> -->
                        <!-- <div class="row">
                            <div class=" row col-md-12">
                                <div class="col-md-4">
                                    <?= $form->field($model, 'Address')->textInput(['placeholder' => 'Postal Address']) ?>

                                </div>
                                <div class="col-md-4">
                                    <?= $form->field($model, 'Phone_No')->textInput(['placeholder'=> 'Phone Number']) ?>
                                

                                </div>

                                <div class="col-md-4">

                                    <?= $form->field($model, 'E_Mail')->textInput(['placeholder'=> 'E-mail Address', 'type' => 'email']) ?>


                                </div>
                            </div>
                        </div> -->

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
<?php

$script = <<<JS

    $(function(){
        
        $('#loanappsecurities-type').change(function(e){
            const SecurityType = e.target.value;
            const No = $('#loanappsecurities-application_no').val();
            const Key = $('#loanappsecurities-key').val();
            var dropdown = $('#loanappsecurities-code');

            if(SecurityType == 'Guarantor'){
                var url = $('input[name=url]').val()+'guarantors/get-members';
            }else{
                var url = $('input[name=url]').val()+'guarantors/get-loan-securities';
            }
            $.get(url, {'SecurityType': SecurityType}, function(response) {
                dropdown.empty();
                dropdown.append($('<option id="itemId"></option>').attr('value', '').text('-- Select Option --')); //append Here
                $.each(response, function (key, entry) {
                    dropdown.append($('<option id="itemId'+ entry.Code+'"></option>').attr('value', entry.Code).text(entry.Description)); //append Here
                })
            })
    
        });
    });
        
JS;

$this->registerJs($script);

