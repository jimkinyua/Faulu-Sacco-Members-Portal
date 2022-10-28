<?php

use yii\bootstrap4\ActiveForm;
use yii\helpers\Html;

?>
<?php $form = ActiveForm::begin(['id' => 'EmploymentDetails']); ?>



<div class="site-login">
    <h3 style="color: black;"> Employment Details :</h3>

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



    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title" id="from-actions-bottom-right">Employment Details</h4>
                <a class="heading-elements-toggle"><i class="la la-ellipsis-v font-medium-3"></i></a>

            </div>
            <div class="card-content collpase show">
                <div class="card-body">

                    <?php $form = ActiveForm::begin([
                        'id' => 'login-form',
                        'class' => 'form',
                        'options' => [
                            'autocomplete' => 'off',
                        ]
                    ]); ?>

                    <div class="form-body">
                        <!-- <h4 class="form-section"><i class="la la-eye"></i> About User</h4> -->
                        <div class="row">
                            <div class="form-group col-md-6 mb-2">
                                <!-- <label for="userinput1">New Password</label> -->
                                <?= $form->field($model, 'Employer_Code')->dropDownList($Employers, ['prompt' => 'Select Employer']) ?>
                                <?= $form->field($model, 'Designation')->textInput([]) ?>

                            </div>
                            <div class="form-group col-md-6 mb-2">
                                <!-- <label for="userinput2">Repeat Password</label> -->
                                <?= $form->field($model, 'Payroll_No')->textInput() ?>
                                <?= $form->field($model, 'Occupation')->textInput() ?>
                            </div>
                        </div>

                    </div>

                    <div class="form-actions text-left">
                        <?= Html::submitButton('Submit For Approval', ['class' => 'btn btn-warning', 'name' => 'login-button',]) ?>
                    </div>
                    </form>

                </div>
            </div>
        </div>
    </div>





    <?php ActiveForm::end(); ?>
</div>
<?php

$script = <<<JS

    if($('#membereditingheader-employer_code').val() != '002') {
            $('.field-membereditingheader-occupation').hide()
            $('.field-membereditingheader-payroll_no').show()
    }else{
        $('.field-membereditingheader-occupation').show()
        $('.field-membereditingheader-payroll_no').hide()
    }

    $("#membereditingheader-employer_code").on('change.yii',function(){
        if($('#membereditingheader-employer_code').val() == '002') {
            $('.field-membereditingheader-occupation').show()
            $('.field-membereditingheader-payroll_no').hide()
            $('#membereditingheader-payroll_no').val('');
        }else{
            $('.field-membereditingheader-occupation').show()
            $('.field-membereditingheader-payroll_no').show()
            $('#membereditingheader-occupation').val('')
        }
    });
        
JS;

$this->registerJs($script);
