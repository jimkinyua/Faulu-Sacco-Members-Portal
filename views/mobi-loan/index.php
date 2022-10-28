<?php

use yii\widgets\ActiveForm;
use yii\helpers\Html;
use borales\extensions\phoneInput\PhoneInput;
$webroot = Yii::getAlias(@$webroot);
$absoluteUrl = \yii\helpers\Url::home(true);


$this->title = 'Mobi Loan';
?>
  <div class="row">
        <div class="col-md-12">
            <?php

            if(Yii::$app->session->hasFlash('success')){
                print ' <div class="alert alert-success alert-dismissable">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                            <h5><i class="icon fas fa-check"></i> Success!</h5>';
                echo Yii::$app->session->getFlash('success');
                print '</div>';
            }else if(Yii::$app->session->hasFlash('error')){
                print ' <div class="alert alert-danger alert-dismissable">
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                            <h5><i class="icon fas fa-check"></i> Error!</h5>
                                    ';
                echo Yii::$app->session->getFlash('error');
                print '</div>';
            }
            ?>
        </div>
    </div>

<?php $form = ActiveForm::begin(['id' => 'MobiRequestForm']); ?>

<?php

if(Yii::$app->session->hasFlash('success')){
    print ' <div class="alert alert-success alert-dismissable">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                <h5><i class="icon fas fa-check"></i> Success!</h5>';
    echo Yii::$app->session->getFlash('success');
    print '</div>';
}else if(Yii::$app->session->hasFlash('error')){
    print ' <div class="alert alert-danger alert-dismissable">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                <h5><i class="icon fas fa-check"></i> Error!</h5>
                        ';
    echo Yii::$app->session->getFlash('error');
    print '</div>';
}
?>

        <?= $form->field($model,'QualifiedAmount')->hiddenInput()->label(false) ?>

    
        <div class="card card-success ">

         
            <div class="card-body">
                    <div class="row">

                        <div class=" row col-md-12">
            
                            <div class="col-md-12"> 
                                <div class="alert alert-info" role="alert">
                                You Qualify for a maximum of  <?= $model->QualifiedAmount ?>  KES
                                </div>

                                <?= $form->field($model, 'AppliedAmount')->textInput(['max'=>$model->QualifiedAmount]) ?>
                            </div>
                        </div>
                    </div>

                    <div class="row col-md-12">
                        <div class="form-group">

                        <div class="CustomPreloader bg-soft flex-column justify-content-center align-items-center" style="display:none">
                            <img class="loader-element" src="<?= $webroot ?>/html&css/assets/img/brand/preloader-spinner.gif" height="50" alt="Mhasibu logo">
                        </div>

                            <?= Html::submitButton('Apply', ['class' => 'btn btn-primary ApplyMobi']) ?>
                        </div>
                    </div>
            </div>
        </div>
        <input type="hidden" name="url" value="<?= $absoluteUrl ?>">


        <?php ActiveForm::end(); ?>

        <?php

$script = <<<JS

    $(function(){

        // $('#MobiRequestForm').on('submit', function(e){
        //     $('#MobiRequestForm').unbind('submit');
        //     e.preventDefault();
        //     e.stopImmediatePropagation();
        //     $('.CustomPreloader').show();// show your loading image
        //     $('.ApplyMobi').hide();

        //     $.ajax({
        //         url : $(this).attr( 'action' ),
        //         type : 'POST',
        //         data : $('#MobiRequestForm').serialize(),
        //         success : function(response){
        //             console.log(response);
        //             if(response !== null && typeof response === 'object' && Array.isArray(response) === false){
        //                 $('.CustomPreloader').hide();// show your loading image
        //                 $('.ApplyMobi').show();
        //                 return false;
        //             }else{
        //                 return false;
        //             }
        //         },
               
        //     });
        //     return false;
        // });


        $('#MobiRequestForm').on('beforeSubmit', function () {
            $('.CustomPreloader').show();// show your loading image
            $('.ApplyMobi').hide();

            var yiiform = $(this);
            $.ajax({
                    type: yiiform.attr('method'),
                    url: yiiform.attr('action'),
                    data: yiiform.serializeArray(),
                }
            )
                .done(function(data) {
                    if(data.success) {
                        // data is saved
                        window.location.replace('/mobi-loan/validate-otp?VtyieYETRg='+data.VtyieYETRg);
                    } else if (data.validation) {
                        // server validation failed
                        yiiform.yiiActiveForm('updateMessages', data.validation, true); // renders validation messages at appropriate places
                    } else {
                        // incorrect server response
                        yiiform.yiiActiveForm('updateMessages', 'Internal Server Error', true); // renders validation messages at appropriate places

                    }
                })

                .fail(function () {
                    // request failed
                    $('.CustomPreloader').hide();// hide your loading image
                    $('.ApplyMobi').show();
                })

            return false; // prevent default form submission
        })

            
    });
        
JS;
$this->registerJs($script);





