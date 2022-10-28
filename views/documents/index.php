<?php
$this->title = 'Payslip Attachments';

use yii\widgets\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;

$webroot = Yii::getAlias(@$webroot);
$absoluteUrl = \yii\helpers\Url::home(true);
// echo '<pre>';
// print_r($MyAttachedDocs);
// exit;

if (is_array($MyAttachedDocs)) {
    $model->isNewRecord = 0;
} else {
    $model->isNewRecord = 1;
}
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
        ?>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <?= $this->render('..\loan\LoanSteps', ['model' => $LoanModel]) ?>
    </div>





    <?php $form = ActiveForm::begin(['id' => 'Attachements', 'options' => ['enctype' => 'multipart/form-data']]); ?>
    <?= $form->field($model, 'isNewRecord')->hiddenInput()->label(false) ?>
    <?= $form->field($model, 'LetterAttached')->hiddenInput()->label(false) ?>
    <?= $form->field($model, 'PayslipOneAttached')->hiddenInput()->label(false) ?>
    <?= $form->field($model, 'PayslipTwoattached')->hiddenInput()->label(false) ?>
    <?= $form->field($model, 'CertifiedBankStatementsAttached')->hiddenInput()->label(false) ?>


    <div class="row">
        <div class="col-md-12">
            <br>
            <h5><strong>(Ensure all your files have different names). For large file(s) compress using <a href="https://www.ilovepdf.com/compress_pdf" target="_blank">PDF COMPRESSOR</a></strong></h5>
            <table class="table">

                <tr>
                    <th></th>
                    <?php if ($LoanModel->Approval_Status == 'New') : ?>
                        <th></th>
                    <?php endif; ?>
                </tr>

                <?php if ($MyAttachedDocs) : ?>
                    <tr>
                        <td>
                            <table class="table table-condensed" style="margin: 3px; border:2em">
                                <h3> Attached Documents </h3>
                                <?php foreach ($MyAttachedDocs as $MyAttachedDocument) : ?>

                                    <?php
                                    $deletelink = Html::a('Delete', Url::to(['delete-application-file', 'filename' => $MyAttachedDocument->Key]), ['class' => 'btn btn-danger btn-sm']);
                                    ?>

                                    <td><?= Html::a($MyAttachedDocument->Document_No, ['read', 'Key' => $MyAttachedDocument->Key]); ?></td>
                                    <td width="15%">
                                        <a href="<?= Url::to(['kin/delete-attachment', 'Key' => urlencode($MyAttachedDocument->Key)]) ?>"><i class="fa fa-trash text-danger"></i><span class="text-danger"> Delete | </span></a>

                                    </td>

                                <?php endforeach; ?>
                            </table>
                        </td>
                    </tr>
                <?php endif; ?>


                <tr>
                    <td> <b> Most Recent Payslip </b></td>
                    <td>
                        <?= $form->field($model, 'PayslipOne')->fileInput()->label(false) ?>
                    </td>
                </tr>


                <tr>
                    <td> <b> Previous Month Payslip </b> </td>
                    <td>
                        <?= $form->field($model, 'PayslipTwo')->fileInput()->label(false) ?>
                    </td>
                </tr>

                <tr>
                    <td> <b> Letter </b> </td>

                    <td>
                        <?= $form->field($model, 'Letter')->fileInput()->label(false) ?>
                    </td>
                </tr>

                <tr>
                    <td> <b> CertifiedBankStatements </b> </td>

                    <?php if ($LoanModel->Approval_Status == 'New') : ?>
                        <td>
                            <?= $form->field($model, 'CertifiedBankStatements')->fileInput()->label(false) ?>
                        </td>
                    <?php endif; ?>
                </tr>



            </table>
            <?php if ($LoanModel->Approval_Status == 'New') : ?>
                <div class="text-left">
                    <?= Html::a('Previous Step', Url::to(['internal-deductions/index', 'Key' => $LoanModel->Key]), ['class' => 'btn btn-info mr-1',]) ?>
                </div>

                <div class="text-right">
                    <img class="loader-element ButtonPreloader" src="<?= $webroot ?>/html&css/assets/img/brand/preloader-spinner.gif" height="50" alt="Faulu logo">
                    <?= Html::submitButton('Next Step', ['class' => 'btn btn-info submitButton',]) ?>
                </div>

            <?php endif; ?>


        </div>
    </div>

    <?php ActiveForm::end(); ?>
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

    $('#Attachements').on('beforeSubmit', function () {
        $('.ButtonPreloader').show();
        $('.submitButton').hide();

        var yiiform = $(this);
        $.ajax({
                type: yiiform.attr('method'),
                url: yiiform.attr('action'),
                data: new FormData($('#Attachements')[0]),
                processData: false,
                contentType: false,
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
