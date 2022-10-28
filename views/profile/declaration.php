<?php

use yii\widgets\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;

$webroot = Yii::getAlias(@$webroot);
$absoluteUrl = \yii\helpers\Url::home(true);

$this->title = 'Terms and Conditions';

if (Yii::$app->session->hasFlash('error')) {
    print ' <div class="alert alert-danger alert-dismissable">
                         <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                            <h5><i class="icon fas fa-check"></i> Error!</h5>
                        ';

    echo Yii::$app->session->getFlash('error');
    print '</div>';

    print ' <div class="alert alert-danger alert-dismissable ErrorPage">
                                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                                    <h5><i class="icon fas fa-check"></i> Error!</h5>';
    print '</div>';
}

?>

<!--THE STEPS THING--->
<div class="row">
    <div class="col-md-12">
        <?= $this->render('..\profile\_steps', ['model' => $model]) ?>


        <div class="col-md-12">

            <div class="card-body">
                <?php $form = ActiveForm::begin(['id' => 'confrimation-form']); ?>

                <?php if ($model->Member_Category == Yii::$app->params['SystemConfigs']['IndividualAccount']) : ?>
                    <!-- <iframe src="data:application/pdf;base64,<?= $applicationForm; ?>" height="400px" width="100%"></iframe> -->
                <?php endif; ?>




                <div class="row">
                    <div class="row col-md-12">
                            <div class="CustomList">
                            <p> In accordance with the Co-operative Act, members of the Society including groups & corporate members are expected to; </p>
                                <ol style="padding-left: revert;" >
                                    <li>
                                        <p> Observe the law, the rules and the by-laws whenever transacting any business with the society.</p>

                                        </li>
                                    <li>
                                        <p> Pay their debt obligations to the society without fail and save regularly with the society to mobilize funds for lending to the members.  </p>
                                    </li>

                                    <li>
                                        <p> Liable for the society’s indebtedness in case of insolvency in accordance with the Act and the by laws.  </p>
                                      
                                    </li>
                                        

                                    <li>
                                        <p> Refrain from engaging in the business of money lending in competition with the society. </p>
                                    </li>
                                    <li>
                                        <p> Protect the image of the society and avoid unnecessary publicity, incitement or careless talk that can injure the reputation of the society.</p>
                                    </li>
                                    <li>
                                        <p> Support issues put forth that improve the sustainability of the Society and promote the goodwill of all members.  </p>
                                    </li>
                                    
                                    <li>
                                        <p> Comply with the By-laws, the Co-operative Societies Act, SACCO Act, Rules and Regulations and General Meeting Resolutions. </p>
                                    </li>
                                    

                                </ol>
                            </div>
                            <p> By clicking on the checkbox herein, I confirm that I am the owner of the Faulu Sacco Society Limited account and I further confirm acceptance and adherence to the terms and conditions above stated.</p>

                        <?= $form->field($model, 'Key')->hiddenInput()->label(false); ?>

                        <hr>
                        <div class="col-lg-5">
                            <?= $form->field($model, 'AcceptTerms')->checkBox([]) ?>
                        </div>


                    </div>
                    <div class="row">
                        <div class="form-group">
                            <img class="loader-element ButtonPreloader" src="<?= $webroot ?>/html&css/assets/img/brand/preloader-spinner.gif" height="50" alt="Faulu logo">
                        </div>

                    </div>
                </div>



            </div>
        </div>
        <div class="text-left">
            <?= Html::a('Previous Page', Url::to(['referral/index', 'Key' => $model->Key]), ['class' => 'btn btn-info mr-1',]) ?>
        </div>
        <div class="text-right">
            <?= Html::submitButton('Submit Application', ['class' => 'btn btn-success submitButton', 'name' => 'login-button']) ?>
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
