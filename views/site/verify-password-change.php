<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
$webroot = Yii::getAlias(@$webroot);
$absoluteUrl = \yii\helpers\Url::home(true);
?>

<div class="site-login">
        <h3 style="color: black;"> Enter Verification Code</h3>

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
        <?php $form = ActiveForm::begin(['options' => [
            'enctype' => 'multipart/form-data',
        'autocomplete' => 'off', 
        'id' => 'ValidateOTPForm',
        'enableAjaxValidation' => true,
        ]]); ?>
        <div class="row">
            <div class=" row col-md-12">

                <div class="col-md-12">
                    <?= $form->field($ValidationModel, 'Code')->textInput() ?>
                </div>

            </div>
        </div>
        <div class="row">
            <div class="form-group">

                <div class="CustomPreloader bg-soft flex-column justify-content-center align-items-center" style="display:none">
                    <img class="loader-element" src="<?= $webroot ?>/html&css/assets/img/brand/preloader-spinner.gif" height="50" alt="Mhasibu logo">
                </div>

                <?= Html::submitButton('Verify', ['class' => 'btn btn-danger', 'id'=>'VerifyOTP']) ?>
                <!-- <div style="font-size: x-large;font-weight: 600;">Time left = <span id="timer"></span></div> -->
            </div>

            <div class="form-group">
            </div>
        </div>
    </div>
                    
</div>
            

        <?php ActiveForm::end(); ?>
        <input type="hidden" name="url" value="<?= $absoluteUrl ?>">

    </div>
</div>

<?php

    $script = <<<JS
    $(function(){
        // timer(116);

        // $('#ValidateOTPForm').on('submit', function(e){

        //     $('#ValidateOTPForm').unbind('submit');
        //     e.preventDefault();
        //     e.stopImmediatePropagation();

        //     $('.CustomPreloader').show();// show your loading image
        //     $('#VerifyOTP').hide();

        //     $.ajax({
        //         url :  $(this).attr( 'action' ),
        //         type : 'POST',
        //         data : $('#ValidateOTPForm').serialize(),
        //         success : function(response){
        //             console.log(response);
        //             if(response !== null && typeof response === 'object' && Array.isArray(response) === false){
        //                 $('.CustomPreloader').hide();// show your loading image
        //                 $('#VerifyOTP').show();
        //                 return false;
        //             }else{
                        
        //             }
        //         },
               
        //     });
        //     return false;
        // });


    });
        
JS;

$this->registerJs($script);

