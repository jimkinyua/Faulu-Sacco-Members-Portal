<?php
$webroot = Yii::getAlias(@$webroot);
$absoluteUrl = \yii\helpers\Url::home(true);
$this->title = 'Loan Disbursal Details';
// echo '<pre>';
// print_r($DisbursalDetails);
// exit;
?>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Disbursment Details</h3>


                <table class="table table-condensed table-hover">

                    <tbody>
                        <tr>
                            <td style="text-align: left; font-size: 12px;"><span id="MainContent_lblForAppliedAmt">Applied Amount: </span></td>
                            <td style="text-align: right; font-size: 12px;" "=""><span id=" MainContent_lblAppliedAmt"><?= $DisbursalDetails->AppliedAmount ?></span></td>
                        </tr>
                        <tr>
                            <td style="text-align: left; font-size: 12px;"><span id="MainContent_lblForApprovedAmt">Approved Amount: </span></td>
                            <td style="text-align: right; font-size: 12px;" "=""><span id=" MainContent_lblApprovedAmt"><?= $DisbursalDetails->ApprovedAmount ?></span></td>
                        </tr>

                        <?php if (is_array($DisbursalDetails->LoanCharges)) : ?>

                            <?php foreach ($DisbursalDetails->LoanCharges as $Charge) : ?>
                                <tr>
                                    <td style="text-align: left; font-size: 12px;"><span id="MainContent_lblForAppliedAmt"><?= $Charge->ChargeName ?> </span></td>
                                    <td style="text-align: right; font-size: 12px;" "=""><span id=" MainContent_lblAppliedAmt"><?= $Charge->ChargeAmount ?></span></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                        <tr style="background-color: #337ab7; color: #000;">
                            <td style="text-align: left; font-size: 12px;"><span id="MainContent_lblForNetPaid">Net Paid: </span></td>
                            <td style="text-align: right; font-size: 12px;"><span id="MainContent_lblNetPaid"><?= $DisbursalDetails->NetAmount ?></span></td>
                        </tr>
                    </tbody>
                </table>

            </div>