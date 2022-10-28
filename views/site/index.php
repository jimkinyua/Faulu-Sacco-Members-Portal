<?php

/* @var $this yii\web\View */

use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'Member Dashboard';
$identity = Yii::$app->user->identity;

$kins = $identity->getMemberKins();
// echo '<pre>';
// print_r($MyAccounts);
// exit;
?>
<div class="row">
    <div class="col-md-12">


        <?php

        // if (Yii::$app->session->hasFlash('success')) {
        //     print ' <div class="alert alert-success alert-dismissable">
        //                     <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
        //                     <h5><i class="icon fas fa-check"></i> Success!</h5>';
        //     echo Yii::$app->session->getFlash('success');
        //     print '</div>';
        // } else if (Yii::$app->session->hasFlash('error')) {
        //     print ' <div class="alert alert-danger alert-dismissable">
        //                         <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
        //                     <h5><i class="icon fas fa-check"></i> Error!</h5>
        //                             ';
        //     echo Yii::$app->session->getFlash('error');
        //     print '</div>';
        // }
        ?>
    </div>
</div>

<div class="row">
    <div class="col-xl-3 col-lg-6 col-12">
        <div class="card pull-up">
            <div class="card-content">
                <div class="card-body">
                    <div class="media d-flex">
                        <div class="media-body text-left">
                            <h3 class="info"><?= ($MyAccounts->Deposit) ?></h3>
                            <h6>Deposits</h6>
                        </div>
                        <div>
                            <!-- <i class="icon-wallet info font-large-2 float-right"></i> -->
                        </div>
                    </div>
                    <div class="progress progress-sm mt-1 mb-0 box-shadow-2">
                        <div class="progress-bar bg-gradient-x-info" role="progressbar" style="width: 80%" aria-valuenow="80" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-lg-6 col-12">
        <div class="card pull-up">
            <div class="card-content">
                <div class="card-body">
                    <div class="media d-flex">
                        <div class="media-body text-left">
                            <h3 class="warning"><?= ($MyAccounts->ShareCapital) ?></h3>
                            <h6>Share Capital </h6>
                        </div>

                    </div>
                    <div class="progress progress-sm mt-1 mb-0 box-shadow-2">
                        <div class="progress-bar bg-gradient-x-danger" role="progressbar" style="width: 65%" aria-valuenow="65" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-lg-6 col-12">
        <div class="card pull-up">
            <div class="card-content">
                <div class="card-body">
                    <div class="media d-flex">
                        <div class="media-body text-left">
                            <h3 class="success"><?= ($MyAccounts->Sherehe) ?></h3>
                            <h6>Sherehe</h6>
                        </div>

                    </div>
                    <div class="progress progress-sm mt-1 mb-0 box-shadow-2">
                        <div class="progress-bar bg-gradient-x-info" role="progressbar" style="width: 75%" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-lg-6 col-12">
        <div class="card pull-up">
            <div class="card-content">
                <div class="card-body">
                    <div class="media d-flex">
                        <div class="media-body text-left">
                            <h3 class="danger"> <?= ($MyAccounts->FreeShares) ?> </h3>
                            <h6>Free Shares</h6>
                        </div>

                    </div>
                    <div class="progress progress-sm mt-1 mb-0 box-shadow-2">
                        <div class="progress-bar bg-gradient-x-danger" role="progressbar" style="width: 85%" aria-valuenow="85" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<!-- Donut Chart -->
<div class="row">

    <!-- Chart -->
    <div class="col-12">
        <div class="card box-shadow-0">
            <div class="card-content">
                <div class="row">
                    <div class="col-md-9 col-12">
                        <div class="">
                            <div class="card">

                                <div class="card-content collapse show draggable-container">
                                    <div class="card-body">

                                        <div class="jqueryui-ele-container">
                                            <div class="tabs-default">
                                                <ul>
                                                    <li><a href="#tabs-default-1"><i class="ft-profile"></i> My Profile </a></li>
                                                    <li><a href="#tabs-default-2"><i class="ft-group"></i> Next of Kin </a></li>
                                                    <li><a href="#tabs-default-3"><i class="ft-image"></i> Accounts </a></li>
                                                </ul>
                                                <div id="tabs-default-1">
                                                    <!-- User Profile Cards -->
                                                    <section id="user-profile-cards" class="row mt-2">
                                                        <!-- <div class="col-12">
                                                            <h4 class="text-uppercase">User Profile Cards</h4>
                                                            <p>User profile cards with border & shadow variant.</p>
                                                        </div> -->
                                                        <div class="col-xl-4 col-md-6 col-12">
                                                            <div class="card">
                                                                <div class="text-center">
                                                                    <div class="card-body">
                                                                        <img src="data:image/png;base64,<?= $image ?>" class="rounded-circle" alt="No Image Found" style="height:150px;width:150px;">
                                                                    </div>
                                                                    <div class="card-body">
                                                                        <h4 class="card-title"><?= $OtherStatisticsModel->Name ?></h4>
                                                                        <h6 class="card-subtitle text-bold"> Member Number: <?= $identity->{'No_'} ?></h6>
                                                                    </div>

                                                                </div>

                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="list-group list-group-flush">
                                                                <a href="#" class="list-group-item"><span> <b>National ID :</b> </span> <?= $identity->{'ID No_'} ?></a>
                                                                <a href="#" class="list-group-item"><span> <b> Email Address : </b> </span> <?= $identity->{'E-Mail'} ?></a>
                                                                <a href="#" class="list-group-item"><span> <b> Date of Birth : </b> </span> <?= date_format(date_create($identity->{'Date of Birth'}), 'l jS F Y')   ?></a>
                                                                <a href="#" class="list-group-item"><span> <b>Phone No :</b> </span> <?= $identity->{'Phone No_'} ?></a>
                                                                <a href="#" class="list-group-item"><span> <b>Payroll No :</b> </span> <?= $identity->{'Payroll_Staff No_'} ?></a>

                                                            </div>
                                                        </div>

                                                    </section>
                                                    <!--/ User Profile Cards -->
                                                </div>
                                                <div id="tabs-default-2">
                                                    <div class="card-content collapse show">
                                                    
                                                        <?php if (is_array($kins)) : ?>
                                                            <div class="table-responsive">
                                                                <table class="table mb-0">
                                                                    <thead>
                                                                        <tr>
                                                                            <th>Names </th>
                                                                            <th>Relationship</th>
                                                                            <th>Phone Number</th>
                                                                            <!-- <th>Date of Birth</th> -->
                                                                            <th>Allocation</th>
                                                                        </tr>
                                                                    </thead>
                                                                   
                                                                    <tbody>
                                                                        <?php foreach ($kins as $key => $kin) : ?>

                                                                            <tr>
                                                                                <td><?= @$kin->Name ?></td>
                                                                                <td><?= @$kin->Kin_Type  ?></td>
                                                                                <td><?= @$kin->Phone_No ?></td>
                                                                                <!-- <td><?= @date_format(date_create($kin->Date_of_Birth), 'l jS F Y')  ?></td> -->
                                                                                <td><?= @$kin->Allocation  ?></td>
                                                                            </tr>
                                                                        <?php endforeach; ?>

                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                               
                                                <div id="tabs-default-3">
                                                    <div class="col-md-12 col-12">
                                                        <div class="card border-light shadow-sm">
                                                            <div class="card-body">
                                                                <h2 class="h5 mb-4">My Acccounts</h2>
                                                              
                                                                <div class="HiddenAccounts">
                                                                    <?php if (is_array($accounts)) : ?>
                                                                        <?php foreach ($accounts as $key => $Scheme) : ?>
                                                                            <?php
                                                                             if($Scheme->Product_Category == 'Registration_Fee' || $Scheme->Loan_Disbursement_Account == 1 ){
                                                                                continue;
                                                                            }
                                                                            ?>
                                                                            <input type="hidden" id="<?= $key ?>" value="<?= abs($Scheme->Balance_LCY) ?>">
                                                                        <?php endforeach; ?>
                                                                    <?php endif; ?>
                                                                </div>


                                                                <div class="table-responsive">
                                                                    <table class="table" id="AccountsTable">
                                                                        <thead class="thead-brown">
                                                                            <tr>
                                                                                <th class="border-0">Account</th>
                                                                                <th class="border-0">Balance</th>
                                                                                <!-- <th class="border-0">Account Status</th> -->
                                                                                <th class="border-0">Action</th>

                                                                            </tr>
                                                                        </thead>
                                                                        <tbody>

                                                                        
                                                                            <!-- Item -->
                                                                            <?php if (is_array($accounts)) : ?>
                                                                                <?php foreach ($accounts as $Scheme) : ?>
                                                                                    <?php 
                                                                                    // echo '<pre>';
                                                                                    // print_r($Scheme);
                                                                                    // exit;

                                                                                        if($Scheme->Product_Category == 'Registration_Fee' || $Scheme->Loan_Disbursement_Account == 1 ){
                                                                                            continue;
                                                                                        }
                                                                                    ?>
                                                                                    <tr>
                                                                   
                                                                                        <td class="border-1">
                                                                                            <input type="hidden" class="SchemeBalance" value="<?= $Scheme->Available_Shares ?>">
                                                                                            <input type="hidden" class="SchemeName" value="<?= $Scheme->Product_Name ?>">
                                                                                            <div><span class="h6"><?= strtoupper($Scheme->Product_Name) ?></span></div>

                                                                                        </td>

                                                                                        <td class="border-1 ">
                                                                                            <div><span class="h6"><?= (abs($Scheme->Balance_LCY)) ?></span></div>
                                                                                        </td>


                                                                                        <?php
                                                                                        $topUplink = Html::a('Top Up', ['/account-deposit', 'Account' => $Scheme->No,], ['title' => 'Deposit Money To Account', 'class' => 'btn btn-danger btn-md']);
                                                                                        $statementLink = Html::a('View Statement', ['account-statement', 'Account' => $Scheme->No,], ['class' => 'btn btn-warning btn-md', 'target' => '_blank']);
                                                                                        ?>


                                                                                            <td><span class="font-weight-normal"><?= ' ' . $statementLink ?></span></td>

                                                                                    </tr>

                                                                                <?php endforeach; ?>
                                                                            <?php endif; ?>
                                                                 
                                                                            <!-- End of Item -->

                                                                        </tbody>
                                                                    </table>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3 col-12">
                        <div class="x_panel">
                            <div class="x_title">
                                <h2 class="dark-blue">Performance Summary </h2>
                                <ul class="nav navbar-right panel_toolbox">
                                    <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                                    </li>
                                    <li class="dropdown">
                                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><i class="fa fa-wrench"></i></a>

                                    </li>
                                    <li><a class=""><i class="fa fa-close"></i></a>
                                    </li>
                                </ul>
                                <div class="clearfix"></div>
                            </div>
                            <div class="x_content2">
                                <div id="sessions-browser-donut-chart" style="width: 100%; height: 300px;">

                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
    <!-- Chart  -->

</div>
</div>


<?php

$script = <<<JS
       
JS;

$this->registerJs($script);
