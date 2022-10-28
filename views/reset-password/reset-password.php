<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
?>
        
    <h3 class="card-title"> Reset Your Password</h3>

    <div class="card-body">
        <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>
        <div class="row">
            <div class=" row col-md-12">

                <div class="col-md-12">
                    <?= $form->field($model, 'password')->passwordInput() ?>
                </div>
                <div class="col-md-12">
                    <?= $form->field($model, 'confirmPassword')->passwordInput() ?>
                </div>

            </div>
        </div>
        <div class="row">
            <div class="form-group">
                <?= Html::submitButton('Save Changes', ['class' => 'btn btn-danger']) ?>
            </div>

            <div class="form-group">
            </div>
        </div>
    </div>
                    
</div>
            

   










        <?php ActiveForm::end(); ?>
    </div>
</div>
