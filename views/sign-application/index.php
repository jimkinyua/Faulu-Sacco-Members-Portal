<?php

use yii\widgets\ActiveForm;
use yii\helpers\Html;
use borales\extensions\phoneInput\PhoneInput;
use yii\helpers\Url;
$webroot = Yii::getAlias(@$webroot);
$absoluteUrl = \yii\helpers\Url::home(true);
// echo '<pre>';
// print_r($SignedApplication);
// exit;

$this->title = 'Form Signing';
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

<?php $form = ActiveForm::begin(); ?>

<?= $this->render('..\loan\LoanSteps', ['model' => $model]) ?>

    <div class="col-md-12">
    
        <div class="card card-success ">
            <!-- <div class="card-body">
                    <h3 class="card-title"> Application Form Signing </h3>
                    <div class="alert " role="alert" style="background-color: pink;">
                    <h5> Please download  the completed application form for Signing <a href="<?= Url::to(['sign-application/download-application-form', 'Key'=>urldecode($model->Key)] ) ?>"  >Download Application Form</a></strong></h5>
            </div> -->
            
            <div class="row">
                <div class=" row col-md-12">
                <table class="table">
                        <tr>
                            <th></th>
                            <th></th>
                            <th>Current Document</th>
                        </tr>
                                <tr>
                                    <td> Payslip Attachements </td>
                                    <td>
                                        <?= $form->field($attachementModel, 'PayslipDetails')->fileInput() ?>
                                        
                                    </td>
                                     <td>
                                        <table class="table table-condensed" style="margin: 0px;">
                                        <?php if($SignedApplication): ?>
                                            <?php if($SignedApplication[0]->File_Name =='Signed Application Form'): ?>
                                                        <tr>
                                                            <td><?=Html::a($SignedApplication[0]->File_Name,['read','No'=> $SignedApplication[0]->Line_No ]);?></td>
                                                            <!-- <td width="15%"><?=Html::a('Delete', ['delete-application-file', 'File_Name' => $SignedApplication[0]->Key], ['class' => 'btn btn-sm btn-danger']);?></td> -->
                                                        </tr>
                                            <?php endif; ?> 
                                        <?php endif; ?>                                         
                                        </table>
                                    </td>
                                </tr>

                    </table>

                </div>
            </div>

            

                <div class="text-left">
                    <?= Html::a('Previous Step', Url::to(['documents/index', 'Key' => $model->Key]), ['class' => 'btn btn-info mr-1',]) ?>
                </div>

                <div class="text-right">
                    <img class="loader-element ButtonPreloader" src="<?= $webroot ?>/html&css/assets/img/brand/preloader-spinner.gif" height="50" alt="Faulu logo">
                    <?= Html::submitButton('Next Step', ['class' => 'btn btn-info submitButton',]) ?>
                </div>


                    <!-- <?= Html::submitButton('Upload Application Form', ['class' => 'btn btn-success']) ?> -->
            

        </div>
        

        <?php ActiveForm::end(); ?>

        <input type="hidden" name="absolute" value="<?= Yii::$app->recruitment->absoluteUrl() ?>">
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

// DisableTabs(Tabs)

$('#confrimation-form').on('beforeSubmit', function () {
    $('.ButtonPreloader').show();
    $('.submitButton').hide();
    window.location.replace('/sign-application?Key='+$('#onllineguarantorrequests-loanformkey').val());

        // window.location.replace('/loan/send-for-approval?Key='+$('#onllineguarantorrequests-loanformkey').val());

    return false; // prevent default form submission
})

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
   
JS;

$this->registerJs($script);

