<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\date\DatePicker;

$this->title = $Title

?>
        
                <h3 class="card-title"><?= $this->title ?></h3>
            
                <div class="card-body">
                    <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data', 'autocomplete' => 'off']]); ?>
                    <div class="row">
                        <div class=" row col-md-12">

                            <div class="col-md-6">
                                <?= 
                               
                                    $form->field($model, 'startDate')->widget(DatePicker::classname(), [
                                        'options' => ['placeholder' => 'Select Start Date ..'],
                                        'pluginOptions' => [
                                            'autoclose' => true,
                                            'format' => 'dd-M-yyyy'
                                        ]
                                    ]);
                                
                                ?>                            
                            </div>

                            <div class="col-md-6">
                                
                                <?= 
                                    $form->field($model, 'endDate')->widget(DatePicker::classname(), [
                                        'options' => ['placeholder' => 'Select End Date ..'],
                                        'pluginOptions' => [
                                            'autoclose' => true,
                                            'format' => 'dd-M-yyyy'
                                        ]
                                    ]);
                                ?>

                            </div>

                        </div>
                    </div>
                        <div class="row">
                            <div class="form-group">
                                <?= Html::submitButton('Generate Statement', ['class' => 'btn btn-primary']) ?>
                            </div>
                        </div>
                    
                </div>
                
             </div>
        <?php ActiveForm::end(); ?>
    </div>
</div>
