<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use borales\extensions\phoneInput\PhoneInput;
use yii\helpers\Url;


$attachments = $model->getKinAttachments();

//$this->title = 'AAS - Employee Profile'
// echo '<pre>';
// print_r($attachments);
// exit;

?>
<!-- <h3 class="card-title">Kin Details</h3> -->

<div class="card-body">
    <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>
    <?= $form->field($model, 'Key')->hiddenInput()->label(false) ?>
    <?= $form->field($model, 'isNewRecord')->hiddenInput()->label(false) ?>



    <div class="row">
        <div class=" row col-md-12">

            <div class="col-md-4">
                <?= $form->field($model, 'Name')->textInput() ?>
                <?= $form->field($model, 'Allocation')->textInput(['']) ?>


            </div>

            <div class="col-md-4">



                <?= $form->field($model, 'Date_of_Birth')->textInput(['type' => 'date']) ?>

                <?= $form->field($model, 'Kin_Type')->dropDownList([
                    'Child' => 'Child',
                    'Spouse' => 'Spouse',
                    'Parent' => 'Parent',
                    'Nephew' => 'Nephew',
                    'Niece' => 'Niece',
                    'Uncle' => 'Uncle',
                    'Aunt' => 'Aunt',
                    'Cousin' => 'Cousin',
                    'Other' => 'Other',
                ], ['prompt' => 'Select Relationship']) ?>

            </div>

            <div class="col-md-4">
                <?= $form->field($model, 'Phone_No')->widget(PhoneInput::className(), [
                    'jsOptions' => [
                        'preferredCountries' => ['ke'],
                    ]
                ]) ?> </div>

        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'IdentificationDocument')->fileInput() ?>
            <?= $form->field($model, 'PassportSizePhoto')->fileInput() ?>
        </div>
    </div>

    <?php if (is_array($attachments)) : ?>
        <?php foreach ($attachments as $attachment) : ?>
            <li class="small">
                <p>
                    <a href="<?= Url::to(['kin/delete-attachment', 'Key' => urlencode($attachment->Key)]) ?>"><i class="fa fa-trash text-danger"></i><span class="text-danger"> Delete | </span></a>
                    <?= $attachment->Document_No ?> <a href="<?= $attachment->Document_No ?>" target="_blank"></a>
                </p>
            </li>
        <?php endforeach; ?>
    <?php endif; ?>


    <div class="row">
        <div class="btn-group" role="group" aria-label="Basic example">
            <?= Html::submitButton('Save Kin Details', ['class' => 'btn btn-info']) ?>

        </div>

    </div><br>
</div>





</div>

</div>













<?php ActiveForm::end(); ?>
</div>
</div>