<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
?>
        
    <h3 class="card-title"> Enter Verification Code</h3>

    <div class="card-body">
        <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data','autocomplete' => 'off']]); ?>
        <div class="row">
            <div class=" row col-md-12">

                <div class="col-md-12">
                    <?= $form->field($model, 'token')->textInput() ?>
                </div>

            </div>
        </div>
        <div class="row">
            <div class="form-group">
                <?= Html::submitButton('Verify', ['class' => 'btn btn-danger']) ?>
                <?= Html::a('Resend Verification Token', ['/member-ship-phone-no/resend'], ['class'=>'btn btn-default']) ?>
            </div>

            <div class="form-group">
            </div>
        </div>
    </div>
                    
</div>
            

   










        <?php ActiveForm::end(); ?>
    </div>
</div>
