<?php
use yii\widgets\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;

$webroot = Yii::getAlias(@$webroot);
$absoluteUrl = \yii\helpers\Url::home(true);


$this->title = 'Terms and Conditions';
?>
<style>
table, th, td {
  border: 1px solid black;
  border-collapse: collapse;
}
th, td {
  /* padding: 5px; */
  text-align: left;
}
</style>


    <!--THE STEPS THING--->
    <div class="row">
        <div class="col-md-12">
        <?= $this->render('.\LoanSteps', ['model'=>$LoanModel]) ?>
        </div>
    </div>



        <div class="col-md-12">

             <div class="card-body">
                <?php $form = ActiveForm::begin(['id' => 'confrimation-form']); ?>

                        <iframe src="data:application/pdf;base64,<?= $applicationForm; ?>" height="400px" width="90%"></iframe>
                                
                    <div class="row">
                        <div class="row col-md-12">
                        <?= $form->field($model, 'Key')->hiddenInput()->label(false); ?>
                        <hr>
                            <div class="col-lg-5">
                                <?= $form->field($model, 'AgreedToTerms')->checkBox([ 'style'=>'zoom:2.5;']) ?>
                            </div>
                        
                        </div>
                       
                    </div>
                    <div class="text-right">
                            <?= Html::a('Previous Step', Url::to(['guarantors/index', 'Key'=>$LoanModel->Key]) , ['class' => 'btn btn-warning mr-1', ]) ?>
                            <img class="loader-element ButtonPreloader" src="<?= $webroot ?>/html&css/assets/img/brand/preloader-spinner.gif" height="50" alt="Faulu logo">
                            <?= Html::submitButton('Submit Application', ['class' => 'btn btn-success submitButton',]) ?>
                    </div>
                   


                <?php ActiveForm::end(); ?>

            </div>
        </div>
    

        <?php

$script = <<<JS

        
   

    /*End Data tables*/
            $('#leaves').on('click','.update', function(e){
                 e.preventDefault();
                var url = $(this).attr('href');
                console.log('clicking...');
                $('.modal').modal('show')
                                .find('.modal-body')
                                .load(url); 
    
            });
            
            
           //Add an experience
        
         $('.create').on('click',function(e){
            e.preventDefault();
            var url = $(this).attr('href');
            console.log('clicking...');
            $('.modal').modal('show')
                            .find('.modal-body')
                            .load(url); 
    
         });
        
        /*Handle dismissal eveent of modal */
        $('.modal').on('hidden.bs.modal',function(){
            var reld = location.reload(true);
            setTimeout(reld,1000);
        });

        let currentTabId = 0// default

$('.ErrorPage').hide()
$('.submitButton').show();
$('.ButtonPreloader').hide();

const Tabs = [];

const DisableTabs = (TabIds)=>{
    console.log(TabIds)
    // if(element.id < ){

    // }
    TabIds.forEach((elementId, index)=>{
        console.log( parseInt(currentTabId))
        if( parseInt(elementId.id) < parseInt(currentTabId)){
            return true;
        }else{
            elementId.href = "javascript:void(0)";
        }
    })
}

$('.breadcrumbb').find('a').each((index, element)=>{
if(element.className == 'active'){ //Don't Disble Current Tab
    currentTabId = element.id;
}
Tabs.push(element);
})

DisableTabs(Tabs)

$('#confrimation-form').on('beforeSubmit', function () {
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