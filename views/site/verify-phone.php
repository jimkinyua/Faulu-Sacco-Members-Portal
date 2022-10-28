<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
?>

<h3 class="card-title"> Enter Verification Code</h3>

<div class="card-body">
    <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data', 'autocomplete' => 'off']]); ?>
    <div class="row">

        <div class=" row col-md-12">

            <div class="col-md-12">
                <?php
                if (Yii::$app->session->hasFlash('success')) {
                    print ' <div class="alert alert-success alert-dismissable">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                        ';
                    echo Yii::$app->session->getFlash('success');
                    print '</div>';
                } else if (Yii::$app->session->hasFlash('error')) {
                    print ' <div class="alert alert-danger alert-dismissable">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                                ';
                    echo Yii::$app->session->getFlash('error');
                    print '</div>';
                }
                ?>
            </div>

            <div class="col-md-12">
                <?= $form->field($model, 'token')->textInput() ?>
            </div>

        </div>
    </div>
    <div class="row">
        <div class="form-group">
            <?= Html::submitButton('Verify', ['class' => 'btn btn-danger', 'id' => 'VerifyOTP']) ?>
            <?= Html::a('Resend Verification Token', ['resend'], ['class' => 'btn btn-default', 'id' => 'ResendToken']) ?>
            <!-- <div style="font-size: x-large;font-weight: 600;">Time left = <span id="timer"></span></div> -->
        </div>

        <div class="form-group">
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
        timer(116)
    });
        
JS;

$this->registerJs($script);
