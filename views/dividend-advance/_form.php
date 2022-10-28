<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
?>
        
                    <h3 class="card-title">Dividend Advance</h3>
              
                    <div class="card-body">
                        <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>
                        <?= $form->field($model,'Key')->hiddenInput()->label(false) ?>
                        
                        <div class="row">
                            <div class=" row col-md-12">
                                <div class=" row col-md-12">

                                    <div class="col-md-12">
                                        <?= $form->field($model, 'AppliedAmount')->textInput() ?>
                                    </div>

                                </div>
                            </div>
                        </div>

                
                        <div class="row">
                            <div class="form-group">
                                <?= Html::submitButton('Submit', ['class' => 'btn btn-success']) ?>
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

