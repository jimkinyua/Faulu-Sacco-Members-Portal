<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
// $this->title = 'AAS - Employee Profile'
?>
        
                    <h3 class="card-title"> Payment Details</h3>
              
                    <div class="card-body">
                        <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>
                        <?= $form->field($model,'Application_No')->hiddenInput()->label(false) ?>

                        <div class="row">
                            <div class=" row col-md-12">

                                <div class="col-md-6">
                                    <?= $form->field($model, 'Mobile_Phone_No')->textInput(['readonly'=>'true']) ?>
                                </div>

                                <div class="col-md-6">
                                    <?= $form->field($model, 'Amount')->textInput(['type' => 'number']) ?>
                                </div>


                            </div>
                        </div>
                        <div class="row">

                            <div class="form-group">
                                <?= Html::submitButton('Deposit', ['class' => 'btn btn-success']) ?>
                            </div>


                        </div>
                    </div>
                    
             </div>
            

   










        <?php ActiveForm::end(); ?>
    </div>
</div>
