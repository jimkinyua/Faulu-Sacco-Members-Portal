<?php

use yii\widgets\ActiveForm;
use yii\helpers\Html;
use borales\extensions\phoneInput\PhoneInput;
use yii\helpers\Url;

$webroot = Yii::getAlias(@$webroot);
$absoluteUrl = \yii\helpers\Url::home(true);


$this->title = 'Employment Details';
?>


<?php $form = ActiveForm::begin(['id' => 'EmploymentDetails']); ?>

<?= $this->render('..\profile\_steps', ['model' => $model]) ?>

<div class="row">
    <div class="col-md-12">
        <?php

        print ' <div class="alert alert-danger alert-dismissable ErrorPage">
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                            <h5><i class="icon fas fa-check"></i> Error!</h5>';
        print '</div>';


        ?>
    </div>
</div>


<div class="col-md-12">

    <div class="card card-success ">

        <div class="card-body">
            <?php if ($model->Approval_Status == 'New') : ?>
                <div class="row">
                    <div class=" row col-md-12">
                        <div class="col-md-6">
                            <?= $form->field($model, 'Employer_Code')->dropDownList($Employers, ['prompt' => 'Select Employer']) ?>
                            <?= $form->field($model, 'Designation')->textInput([]) ?>

                        </div>
                        <div class="col-md-6">

                            <?= $form->field($model, 'Payroll_No')->textInput() ?>
                            <?= $form->field($model, 'Occupation')->textInput() ?>

                        </div>

                    </div>
                </div>



            <?php else : ?>
                <div class="row">
                    <div class=" row col-md-12">
                        <div class="col-md-6">
                            <?= $form->field($model, 'Employer_Code')->textInput(['readonly' => true]) ?>
                            <?= $form->field($model, 'Designation')->textInput(['readonly' => true]) ?>
                        </div>
                        <div class="col-md-6">
                            <?= $form->field($model, 'Payroll_No')->textInput(['readonly' => true]) ?>
                            <?= $form->field($model, 'Occupation')->textInput(['readonly' => true]) ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
        <div class="text-left">
            <?= Html::a('Previous Page', Url::to(['communication/index', 'Key' => $model->Key]), ['class' => 'btn btn-info mr-1',]) ?>
        </div>

        <div class="text-right">
            <div class="form-group">
                <img class="loader-element ButtonPreloader" src="<?= $webroot ?>/html&css/assets/img/brand/preloader-spinner.gif" height="50" alt="Faulu logo">
                <?= Html::submitButton('Next Page', ['class' => 'btn btn-info submitButton']) ?>
            </div>

        </div>
    </div>



    <?php ActiveForm::end(); ?>

    <?php

    $script = <<<JS

    $('.ErrorPage').hide();
    $('.submitButton').show();
    $('.ButtonPreloader').hide();

    const Tabs = [];

    const DisableTabs = (TabIds)=>{
        console.log(TabIds)

        TabIds.forEach((elementId, index)=>{
            elementId.href = "javascript:void(0)";
        })
    }

    if(!$('#employmentdetails-employer_code').val() != '227') {
            $('.field-employmentdetails-occupation').hide()
            $('.field-employmentdetails-payroll_no').show()
    }else{
        $('.field-employmentdetails-occupation').show()
        $('.field-employmentdetails-payroll_no').show()
    }

   $('.breadcrumbb').find('a').each((index, element)=>{
       if(element.className == 'active'){ //Don't Disble Current Tab
        return true;
       }
       Tabs.push(element);
   })

   DisableTabs(Tabs)

    $('#EmploymentDetails').on('beforeSubmit', function () {
        $('.submitButton').hide();
        $('.ButtonPreloader').show();

        var yiiform = $(this);
        $.ajax({
                type: yiiform.attr('method'),
                url: yiiform.attr('action'),
                data: yiiform.serializeArray(),
            }
        )
            .done(function(data) {
                $('.submitButton').hide();
                $('.ButtonPreloader').show();

                if(data.success) {
                    // data is saved
                    $('.ErrorPage').text('');
                    $('.ErrorPage').hide();
                } else if (data.validation) {
                    // server validation failed
                    yiiform.yiiActiveForm('updateMessages', data.validation, true); // renders validation messages at appropriate places
                    // anchor.href = "javascript:void(0)";
                    $('.breadcrumbb').find('a').each((index, element)=>{
                        if(element.className == 'active'){ //Don't Disble Current Tab
                            return true;
                        }
                        Tabs.push(element);
                    })
                    DisableTabs(Tabs)

                }
                
                else if (data.error) {
                    // server validation failed
                    $('.ErrorPage').text(data.error);
                    $('.ErrorPage').show();

                    $('.submitButton').show();
                    $('.ButtonPreloader').hide();

                    // anchor.href = "javascript:void(0)";
                    $('.breadcrumbb').find('a').each((index, element)=>{
                        if(element.className == 'active'){ //Don't Disble Current Tab
                            return true;
                        }
                        Tabs.push(element);
                    })
                    DisableTabs(Tabs)

                }

                else {
                    // incorrect server response
                }
            })
            .fail(function () {
                // request failed
                $('.submitButton').show();
                $('.ButtonPreloader').hide();
            })

        return false; // prevent default form submission
    })

    $("#employmentdetails-employer_code").on('change.yii',function(){
        if($('#employmentdetails-employer_code').val() == '227') {
            $('.field-employmentdetails-occupation').show()
            $('.field-employmentdetails-payroll_no').hide()
            $('.employmentdetails-payroll_no').val('');
        }else{
            $('.field-employmentdetails-occupation').show()
            $('.field-employmentdetails-payroll_no').show()
            $('.employmentdetails-occupation').val('')
        }
    });
        
JS;

    $this->registerJs($script);
