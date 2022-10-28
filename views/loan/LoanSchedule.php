<?php

use yii\widgets\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;


$webroot = Yii::getAlias(@$webroot);
$absoluteUrl = \yii\helpers\Url::home(true);

$this->title = 'Loan Schedule';
?>
<style>
    table,
    th,
    td {
        border: 1px solid black;
        border-collapse: collapse;
    }

    th,
    td {
        /* padding: 5px; */
        text-align: left;
    }
</style>


<!--THE STEPS THING--->
<div class="row">
    <div class="col-md-12">
        <?= $this->render('.\LoanSteps', ['model' => $LoanModel]) ?>
    </div>
</div>

<!-- <div class="col-md-12"> -->

<div class="card-body">
    <?php $form = ActiveForm::begin(['id' => 'confrimation-form']); ?>

    <iframe src="data:application/pdf;base64,<?= $applicationForm; ?>" height="400px" width="90%"></iframe>

    <div class="text-left">
        <br>
        <?= Html::a('Previous Step', Url::to(['loan/update', 'Key' => $LoanModel->Key]), ['class' => 'btn btn-info mr-1',]) ?>
    </div>
    <div class="text-right">
        <?= Html::submitButton('Next Step', ['class' => 'btn btn-info submitButton',]) ?>
        <img class="loader-element ButtonPreloader" src="<?= $webroot ?>/html&css/assets/img/brand/preloader-spinner.gif" height="50" alt="Faulu logo">

    </div>


    <?php ActiveForm::end(); ?>

</div>
</div>

<?php

$script = <<<JS
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

//    DisableTabs(Tabs)




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
                
            })
            .fail(function () {
               
            })

        return false; // prevent default form submission
    })
        
JS;

$this->registerJs($script);
