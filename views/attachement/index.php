<?php

use kartik\file\FileInput;
use yii\bootstrap4\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;

$webroot = Yii::getAlias(@$webroot);
$absoluteUrl = \yii\helpers\Url::home(true);


$this->title = 'Attachments';
$this->params['breadcrumbs'][] = ['label' => 'File Uploads', 'url' => ['index']];

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


<!-- <div class="col-md-12"> -->
<?= $this->render('..\profile\_steps', ['model' => $Applicant]) ?>
<!-- </disv> -->


<input type="hidden" name="absolute" value="<?= Yii::$app->recruitment->absoluteUrl() ?>">
<input type="hidden" name="DocNum" value="<?= $Applicant->Application_No ?>">
<?php $form = ActiveForm::begin(['id' => 'Attachements', 'options' => ['enctype' => 'multipart/form-data']]); ?>

<div class="card border-danger ">
    <div class="card-body">
        <div class="row ">
            <div class="col-md-12">

                <table class="table">
                    <tr>
                        <th></th>
                        <th></th>
                        <th>Current Document</th>
                    </tr>
                    <?php foreach ($RequiredAttachements as $RequiredAttachement) : ?>
                        <?php
                        if ($RequiredAttachement['required']  === true) {
                            $required = 'required = "required"';
                            $mandatoryText = '(Mandatory)';
                        } else {
                            $required = '';
                            $mandatoryText = '';
                        }

                        if ($MyAttachedDocs) {
                            foreach ($MyAttachedDocs as $MyAttachedDocument) {
                                if (strtolower($MyAttachedDocument->Document_No) == strtolower($RequiredAttachement['Name'])) $required = '';
                            }
                        }
                        ?>
                        <tr>
                            <td> <?= $RequiredAttachement['Name'] ?> <b> <?= $RequiredAttachement['ExtraDesc'] ?></b> <span style="font-weight: bold; color: red;"> <?= $mandatoryText ?></span></td>

                            <td>
                                <input type="file" accept="<?= $RequiredAttachement['extensions'] ?>" name="<?= @$RequiredAttachement['Name'] ?>" <?= $required ?>>
                            </td>
                            <td>
                                <table class="table table-condensed" style="margin: 0px;">
                                    <?php if ($MyAttachedDocs) : ?>
                                        <?php foreach ($MyAttachedDocs as $MyAttachedDocument) : ?>
                                            <?php if (strtolower($MyAttachedDocument->Document_No) == strtolower($RequiredAttachement['Name'])) : ?>
                                                <tr>
                                                    <td><?= Html::a($MyAttachedDocument->Document_No, ['read', 'Key' => $MyAttachedDocument->Key]); ?></td>
                                                    <!-- <td width="15%"><?= Html::a('Delete', ['delete-application-file', 'filename' => $MyAttachedDocument->Key], ['class' => 'btn btn-sm btn-danger']); ?></td> -->
                                                </tr>
                                            <?php endif; ?>

                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </table>
                            </td>
                        </tr>
                    <?php endforeach; ?>

                </table>

                <!-- <?= Html::submitButton('Upload Files', ['class' => 'btn btn-success']) ?> -->


            </div>
        </div>
    </div>
</div>


<div class="row">
    <div class="col-12">
        <div class="text-left">
            <?= Html::a('Previous Page', Url::to(['subscriptions/index', 'Key' => $Applicant->Key]), ['class' => 'btn btn-info mr-1',]) ?>
        </div>
        <div class="text-right">
            <img class="loader-element ButtonPreloader" src="<?= $webroot ?>/html&css/assets/img/brand/preloader-spinner.gif" height="50" alt="Faulu logo">
            <?= Html::submitButton('Next Page', ['class' => 'btn btn-success submitButton']) ?>
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

                }
                
                else if (data.error) {
                    // server validation failed
                    $('.ErrorPage').text(data.error[0]);
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
