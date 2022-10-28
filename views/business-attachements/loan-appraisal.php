<?php
$this->title = 'Loan Appraisal Report';
?>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Loan Appraisal Report</h3>

                </div>
                <div class="card-body">
                    <!--<iframe src="data:application/pdf;base64,<?/*= $content; */?>" height="950px" width="100%"></iframe>-->
                    <?php
                    if($report){ ?>
                        <iframe src="data:application/pdf;base64,<?= $content; ?>" height="950px" width="100%"></iframe>
                   <?php } ?>



                </div>
            </div>
        </div>
    </div>

<?php
$script  = <<<JS
    $('select[name="payperiods"]').select2();
JS;

$this->registerJs($script, yii\web\View::POS_READY);










