<?php

use yii\widgets\ActiveForm;
use yii\helpers\Html;
use borales\extensions\phoneInput\PhoneInput;
use kartik\depdrop\DepDrop;
use yii\helpers\Url;

$webroot = Yii::getAlias(@$webroot);
$absoluteUrl = \yii\helpers\Url::home(true);


$this->title = 'Communication Details';
// if (empty($model->Marketing_Texts)) {
//     $model->Marketing_Texts = 0;
// }
// echo '<pre>';
// print_r($model);
// exit;
?>

<?php $form = ActiveForm::begin(['id' => 'CommunicationDetails']); ?>

<?= $this->render('..\profile\_steps', ['model' => $model]) ?>

<div class="col-md-12">


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

    <div class="card card-success ">

        <div class="card-body">
                <div class="row">
                    <div class=" row col-md-12">
                        <div class="col-md-6">



                            <br>
                            <?= $form->field($model, 'Mobile_Phone_No')->widget(PhoneInput::className(), [
                                'jsOptions' => [
                                    'preferredCountries' => ['ke'],
                                ]
                            ]) ?>

                       
                        

                            <?= $form->field($model, 'Current_Address')->textInput() ?>




                        </div>
                        <div class="col-md-6">
                           
                            <?= $form->field($model, 'E_Mail')->textInput() ?>
                            

                        </div>
                    </div>

                </div>
        </div>
        <div class="text-left">
            <br>
            <?= Html::a('Previous Page', Url::to(['profile/index', 'Key' => $model->Key]), ['class' => 'btn btn-info mr-1',]) ?>
        </div>

        <div class="text-right">
            <img class="loader-element ButtonPreloader" src="<?= $webroot ?>/html&css/assets/img/brand/preloader-spinner.gif" height="50" alt="Faulu logo">
            <?= Html::submitButton('Next Page', ['class' => 'btn btn-info submitButton']) ?>
        </div>

        <?php ActiveForm::end(); ?>

        <?php

        $script = <<<JS

    $('.ErrorPage').hide()
    $('.submitButton').show();
    $('.ButtonPreloader').hide();

    const Tabs = [];

    const DisableTabs = (TabIds)=>{
        // console.log(TabIds)

        // TabIds.forEach((elementId, index)=>{
        //     elementId.href = "javascript:void(0)";
        // })
    }

   $('.breadcrumbb').find('a').each((index, element)=>{
       if(element.className == 'active'){ //Don't Disble Current Tab
        return true;
       }
       Tabs.push(element);
   })

   DisableTabs(Tabs)

    $('#CommunicationDetails').on('beforeSubmit', function () {
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
