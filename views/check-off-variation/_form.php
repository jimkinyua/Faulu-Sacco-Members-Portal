<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;


$this->title = 'Check Off Variation Form';
$absoluteUrl = \yii\helpers\Url::home(true);

// echo '<pre>';
// print_r($model->getCheckOffVariationLines());
// exit;

?>

<h3 class="card-title"><?= $this->title ?></h3>
<?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>
<div class="card-body">
    <?= $form->field($model, 'Key')->hiddenInput()->label(false) ?>


    <div class="row">
        <div class=" row col-md-12">

            <div class="col-md-12">
                <?= $form->field($model, 'Effective_Date')->textInput(['type' => 'date', 'readonly' => true, 'disabled' => true]) ?>
            </div>
            <?php if ($model->isNewRecord === false) : ?>
                <div class=" col-lg-12 ">
                    <div class="border-dark shadow-sm table-wrapper table-responsive">
                        <table class="table table-hover" id="Subscriptions">
                            <thead>
                                <tr>
                                    <th class="border-0">Product</th>
                                    <th class="border-0"> Current Contribution </th>
                                    <th class="border-0">New Contribution</th>

                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($model->getCheckOffVariationLines()) : ?>

                                    <?php foreach ($model->getCheckOffVariationLines() as $CheckOffVariationLines) : ?>
                                        <?php if (empty($CheckOffVariationLines->Acount_Code)) : ?>
                                            <?php continue; ?>
                                        <?php endif; ?>
                                        <tr>
                                            <td><span class="font-weight-normal"><?= empty($CheckOffVariationLines->Description) ? $CheckOffVariationLines->Acount_Code : $CheckOffVariationLines->Description ?></span></td>

                                            <td>
                                                <input class="form-control" type="text" value="<?= number_format($CheckOffVariationLines->Current_Contribution) ?>" readonly>
                                                <input class="form-control Key" type="hidden" value="<?= ($CheckOffVariationLines->Key) ?>" />
                                            </td>
                                            <td>
                                                <input class="form-control ContributionAmount" type="text" value="<?= number_format($CheckOffVariationLines->New_Contribution) ?>">
                                            </td>



                                        </tr>
                                    <?php endforeach; ?>

                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php endif; ?>

        </div>
    </div>


    <br>

    <div class="row">
        <div class="form-group">
            <?= Html::submitButton('Submit ', ['class' => 'btn btn-success', 'id' => 'SubmitButton']) ?>
        </div>

    </div>

</div>

<?php ActiveForm::end(); ?>
<input type="hidden" name="url" value="<?= $absoluteUrl ?>">

<?php

$script = <<<JS

    function submitRowData(row){
                var Key = row.find('.Key').val();
                var ParameterValue = row.find('.ContributionAmount').val();
                var Application_No = $('#Application_No').val();

                    var updateurl = '/check-off-variation/commit-row-data';
                    $.post(updateurl,
                        {  
                            'Parameter_Value':ParameterValue,
                            'Key':Key,
                        },
                        function(data){
                            console.log(data);

                            if(data.length){
                                swal("Error", data , "error");;
                                row.find('.ContributionAmount').val(0)
                                return false;
                            }
                            row.find('.Key').val(data.Key);
                        }   
                    );
    }

    $(function(){
        
        $('#Subscriptions').on('change','.ContributionAmount', function(){
            // alert('hu')
            var currentrow = $(this).closest('tr');
            return submitRowData(currentrow)
        });
        
        /*Handle dismissal eveent of modal */
        $('.modal').on('hidden.bs.modal',function(){
            var reld = location.reload(true);
            setTimeout(reld,1000);
        });
    });
        
JS;

$this->registerJs($script);
