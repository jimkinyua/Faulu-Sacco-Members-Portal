<?php

use borales\extensions\phoneInput\PhoneInput;
use yii\bootstrap4\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'Accounts';

use kartik\select2\Select2;

$webroot = Yii::getAlias(@$webroot);
$absoluteUrl = \yii\helpers\Url::home(true);


// echo '<pre>';
// print_r($model);
// exit;
?>
<?php $form = ActiveForm::begin(['id' => 'AccountDetails']); ?>
<!--THE STEPS THING--->
<div class="row">
    <div class="col-md-12">
        <?= $this->render('..\profile\_steps', ['model' => $Applicant]) ?>
        <!-- <h3> Internal Recoveries </h3> -->
    </div>
</div>
<!--END THE STEPS THING--->


<div class="col-md-12">

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

    <div class="card border-danger ">
        <div class="card-body">
            <div class="row col-12">
                <?= $form->field($model, 'ATM')->checkbox(['checked' => $model->ATM == 1,  'value' => true])  ?>
            </div>
            <div class="row col-12">
                <?= $form->field($model, 'Mobile')->checkbox(['checked' => $model->Mobile == 1,  'value' => true])  ?>
            </div>
            <div class="row col-12">
                <?= $form->field($model, 'FOSA')->checkbox(['checked' => $model->FOSA == 1,  'value' => true])  ?>
            </div>

        </div>
    </div>
    <div class="text-left">
        <?= Html::a('Previous Page', Url::to(['subscriptions/index', 'Key' => $model->Key]), ['class' => 'btn btn-info mr-1',]) ?>
    </div>
    <div class="text-right">
        <img class="loader-element ButtonPreloader" src="<?= $webroot ?>/html&css/assets/img/brand/preloader-spinner.gif" height="50" alt="Faulu logo">
        <?= Html::submitButton('Next Page', ['class' => 'btn btn-info submitButton']) ?>
    </div>
</div>


<?php ActiveForm::end(); ?>



<input type="hidden" name="absolute" value="<?= Yii::$app->recruitment->absoluteUrl() ?>">
<input type="hidden" id="Application_No" value="<?= $Applicant->Application_No ?>">
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

   $('.breadcrumbb').find('a').each((index, element)=>{
       if(element.className == 'active'){ //Don't Disble Current Tab
        return true;
       }
       Tabs.push(element);
   })

   DisableTabs(Tabs)

    $('#AccountDetails').on('beforeSubmit', function () {
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
