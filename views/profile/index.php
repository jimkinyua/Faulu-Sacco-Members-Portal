<?php

use yii\widgets\ActiveForm;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use kartik\date\DatePicker;
use kartik\depdrop\DepDrop;
use yii\helpers\Url;

$webroot = Yii::getAlias(@$webroot);
$absoluteUrl = \yii\helpers\Url::home(true);

$this->title = 'Member Profile';
// echo '<pre>';
// print_r($Applicant);
// exit;
?>


<?= $this->render('_steps', ['model' => $Applicant]) ?>

<div class="col-md-12">

    <div class="card">

        <div class="row">
            <div class="col-md-12">
                <?php

                if (Yii::$app->session->hasFlash('success')) {
                    print ' <div class="alert alert-success alert-dismissable">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                            <h5><i class="icon fas fa-check"></i> Success!</h5>';
                    echo Yii::$app->session->getFlash('success');
                    print '</div>';
                } else if (Yii::$app->session->hasFlash('error')) {
                    print ' <div class="alert alert-danger alert-dismissable">
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                            <h5><i class="icon fas fa-check"></i> Error!</h5>
                                    ';
                    echo Yii::$app->session->getFlash('error');
                    print '</div>';
                }

                print ' <div class="alert alert-danger alert-dismissable ErrorPage">
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                            <h5><i class="icon fas fa-check"></i> Error!</h5>';
                print '</div>';


                ?>
            </div>
        </div>

        <div class="card-body">
            <?php $form = ActiveForm::begin(

                [
                    'options' => ['enctype' => 'multipart/form-data', 'autocomplete' => 'off'],
                    'id' => 'GeneralInformation'
                ]
            ); ?>

            <div class="row">
                <div class=" row col-md-12">
                    <?= $form->field($model, 'Key')->hiddenInput()->label(false) ?>
                    <?= $form->field($model, 'No')->hiddenInput()->label(false) ?>

                        <div class="col-md-6">
                            
                                <?= $form->field($model, 'Name')->textInput(['readonly' => true]) ?>
                                <?= $form->field($model, 'Last_Name')->textInput() ?>
                                <?= $form->field($model, 'Second_Name')->textInput() ?>

                                
                                <?php if ($model->Member_Category == Yii::$app->params['SystemConfigs']['IndividualAccount']) : ?>

                                    <?= $form->field($model, 'Marital_Status')->dropDownList([
                                        'Single' => 'Single',
                                        'Married' => 'Married',
                                        'Widowed' => 'Widowed',
                                        'Divorced' => 'Divorced',
                                        'Withheld' => 'Withheld',
                                    ], ['prompt' => 'Select Status']) ?>

                                <?php endif; ?>
                        </div>
                        <div class="col-md-6">
                           
                                <?php if ($model->Member_Category == Yii::$app->params['SystemConfigs']['IndividualAccount']) : ?>
                                    <?= $form->field($model, 'ID_No')->textInput(['readonly' => true]) ?>
                                    <?= $form->field($model, 'P_I_N_Number')->textInput() ?>
                                    <?= $form->field($model, 'Date_of_Birth')->widget(DatePicker::classname(), [
                                        'options' =>
                                        ['placeholder' => 'Enter birth date ...',],
                                        'pluginOptions' => ['autoclose' => true, 'format' => 'yyyy-m-dd',]
                                    ]); ?>
                                <?php endif; ?>


          
                                <?php if ($model->Member_Category == Yii::$app->params['SystemConfigs']['IndividualAccount']) : ?>
                                    <?= $form->field($model, 'Gender')->dropDownList([
                                        'Female' => 'Female',
                                        'Male' => 'Male',
                                    ], ['prompt' => '-- Select Option --']) ?>


                                <?php endif; ?>

                        </div>


                </div>
            </div>

                <!-- <div class="row"> -->
                <div class="text-right">
                    <img class="loader-element ButtonPreloader" src="<?= $webroot ?>/html&css/assets/img/brand/preloader-spinner.gif" height="50" alt="Faulu logo">
                    <?= Html::submitButton('Next Page', ['class' => 'btn btn-info submitButton']) ?>
                </div>

                <!-- </div> -->



        </div>


    </div>
</div>

<?php ActiveForm::end(); ?>

<?php

$script = <<<JS

    $('.ErrorPage').hide()
    $('.submitButton').show();
    $('.ButtonPreloader').hide();

    const Tabs = [];

    const DisableTabs = (TabIds)=>{
        console.log(TabIds)

        TabIds.forEach((elementId, index)=>{
            elementId.href = "javascript:void(0)";
        })
    }

   $('.breadcrumbb').find('a').each((index, element)=>{
       if(element.className == 'active'){ //Don't Disble Current Tab
        return true;
       }
       Tabs.push(element);
   })

   DisableTabs(Tabs)




    $('#GeneralInformation').on('beforeSubmit', function () {
        $('.ButtonPreloader').show();
        $('.submitButton').hide();

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
                      $('.ButtonPreloader').hide();
                      $('.submitButton').show();
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
            })

        return false; // prevent default form submission
    })
        
JS;

$this->registerJs($script);
