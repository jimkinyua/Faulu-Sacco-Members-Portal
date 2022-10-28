<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use borales\extensions\phoneInput\PhoneInput;

?>

<div class="site-login">
        <h3 style="color: black;"> Reset Password </h3>
        <?php
            if(Yii::$app->session->hasFlash('success')){
                print ' <div class="alert alert-success alert-dismissable">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                            ';
                echo Yii::$app->session->getFlash('success');
                print '</div>';
            }else if(Yii::$app->session->hasFlash('error')){
                print ' <div class="alert alert-danger alert-dismissable">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                                    ';
                echo Yii::$app->session->getFlash('error');
                print '</div>';
            }
        ?>

        <div class="card-body">
            <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data', 'autocomplete' => 'off']]); ?>
                <div class="row"> 
                    <div class="col-md-12">
                  
         
                        <?= $form->field($model, 'Id_No')->textInput([
                                        'autofocus' => true,
                                        'placeholder' => 'ID Or Passport No'
                                    ])->label(false) 
                        ?>

      
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
</div>
                       

