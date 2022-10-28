<?php

use yii\widgets\ActiveForm;
use yii\helpers\Html;
use kartik\depdrop\DepDrop;
use yii\helpers\Url;

$webroot = Yii::getAlias(@$webroot);
$absoluteUrl = \yii\helpers\Url::home(true);



$this->title = 'Refferal Details';
?>
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

<?php $form = ActiveForm::begin(['id' => 'RefferalDetails']); ?>

<?= $this->render('..\profile\_steps', ['model' => $model]) ?>

<div class="col-md-12">

    <div class="card card-success border-danger ">

        <div class="card-body">
            <?= $form->field($model, 'Key')->hiddenInput()->label(false) ?>

                <div class="row">
                    <div class=" row col-md-12">
                        <div class="col-md-6">

                            <?= $form->field($model, 'Recruited_by_Type')->dropDownList([
                                'Marketer' => 'Marketer',
                                'Members' => 'Members',
                                // 'Others' => 'Others',


                            ], ['prompt' => '-- Select Option --',]) ?>
                        </div>
                        <div class="col-md-6">

                            <?= $form->field($model, 'Recruited_By' )->textInput() ?>



                        </div>

                    </div>
                </div>


                <!-- </div> -->
         
        </div>
    </div>
    <div class="text-right">
        <img class="loader-element ButtonPreloader" src="<?= $webroot ?>/html&css/assets/img/brand/preloader-spinner.gif" height="50" alt="Faulu logo">
        <?= Html::submitButton('Next Page', ['class' => 'btn btn-info submitButton']) ?>
    </div>
    <div class="text-left">
        <?= Html::a('Previous Page', Url::to(['attachement/index', 'Key' => $model->Key]), ['class' => 'btn btn-info mr-1',]) ?>
    </div>
    <?php ActiveForm::end(); ?>

    <?php

    $script = <<<JS

    const getDropDownValues = (url, refferedby)=>{
        if(refferedby == 'Marketer'){
            $('.field-refferalmodel-recruited_by').find('label').text('Select Sales Person')
            $('#refferalmodel-recruited_by').replaceWith('<select id="refferalmodel-recruited_by" class="form-control" name="RefferalModel[Recruited_By]" aria-required="true"></select>');

            $.get(url,function(response) {
                $('#refferalmodel-recruited_by').append($('<option id="itemId" selected="true"></option>').attr('value', '').text('Select Sales Person')); //append Here
                $.each(response, function (key, entry) {
                    if($('#Myreffer').val() == entry.Code){
                        $('#refferalmodel-recruited_by').append($('<option id="itemId'+ entry.Code+'" selected="true"></option>').attr('value', entry.Code).text(entry.Description)); //append Here
                        return true;
                    }
                    $('#refferalmodel-recruited_by').append($('<option id="itemId'+ entry.Code+'"></option>').attr('value', entry.Code).text(entry.Description)); //append Here
                })
            });
        }else{
            $('.field-refferalmodel-recruited_by').find('label').text('Enter Member ID')
            $('#refferalmodel-recruited_by').replaceWith('<input type="text" id="refferalmodel-recruited_by" class="form-control" name="RefferalModel[Recruited_By]" aria-required="true">');
        }
    }

    $(function(){
        var url = '/referral/get-marketers';
        var dropdown = $('#refferalmodel-member_no'); 
        dropdown.hide();
        $('.submitButton').show();
        $('.ButtonPreloader').hide();

        getDropDownValues(url, $('#refferalmodel-recruited_by').val())


        $('#refferalmodel-recruited_by_type').change(function(e){
            var refferedby = e.target.value;
            getDropDownValues(url, refferedby)
        });

        $('.ErrorPage').hide()
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

    $('#RefferalDetails').on('beforeSubmit', function () {
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
    });
        
JS;

    $this->registerJs($script);
