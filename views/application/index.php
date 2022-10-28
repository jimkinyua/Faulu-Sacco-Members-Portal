<?php

/* @var $this yii\web\View */
use yii\helpers\Html;
$this->title = 'Member Dashboard';

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
    
<div class="site-index ">
    <div class="body-content">
        <div class="row justify-content-md-center">
            <div class="col-12 col-sm-6 col-xl-4 mb-4">
                <div class="card border-light shadow-sm">
                    <div class="card-body">
                        <div class="row d-block d-xl-flex align-items-center">
                            <div class="col-12 col-xl-5 text-xl-center mb-3 mb-xl-0 d-flex align-items-center justify-content-xl-center">
                                <div class="icon icon-shape icon-md icon-shape-brown rounded mr-4 mr-sm-0"><span class="fas fa-pizza-slice"></span></div>
                                <div class="d-sm-none">
                                    <h2 class="h5 text-brown" > SHARE CAPITAL</h2>
                                    <h3 class="mb-1"><?=number_format($model->Share_Capital) ?></h3>
                                </div>
                            </div>
                            <hr>
                            <div class="col-12 col-xl-7 px-xl-0">
                                <div class="d-none d-sm-block">
                                    <h2 class="h5 text-mhasibu-brown">SHARE CAPITAL</h2>
                                    <h3 class="mb-1"><?=number_format($model->Share_Capital) ?></h3>
                                </div>
                                <small> <span class="icon icon-small"><span class="fas fa"></span></span> </small> 
                                <div class="small mt-2">                               
                                    <!-- <span class="fas fa-angle-up text-success"></span>                                   
                                    <span class="text-success font-weight-bold">18.2%</span> Since last month -->
                                </div>
                            </div>
                        </div>
                        <div class="row d-block d-xl-flex align-items-center">
                            <div class="col-12 col-xl-5 text-xl-center mb-3 mb-xl-0 d-flex align-items-center justify-content-xl-center">
                                <div class="icon icon-shape icon-md icon-shape-brown rounded mr-4 mr-sm-0"><span class="fas fa-baby"></span></div>
                                <div class="d-sm-none">
                                    <h2 class="h5 text-mhasibu-brown">CHILD SCHEME</h2>
                                    <h3 class="mb-1"><?=number_format($model->Child_Scheme) ?></h3>
                                </div>
                            </div>
                            <div class="col-12 col-xl-7 px-xl-0">
                                <div class="d-none d-sm-block">
                                <h2 class="h5 text-mhasibu-brown">CHILD SCHEME</h2>
                                    <h3 class="mb-1"><?=number_format($model->Child_Scheme) ?></h3>
                                </div>
                                <small> <span class="icon icon-small"><span class="fas fa"></span></span> </small> 
                                <div class="small mt-2">                               
                                    <!-- <span class="fas fa-angle-up text-success"></span>                                   
                                    <span class="text-success font-weight-bold">18.2%</span> Since last month -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-xl-4 mb-4">
                <div class="card border-light shadow-sm">
                    <div class="card-body">
                        <div class="row d-block d-xl-flex align-items-center">
                            <div class="col-12 col-xl-5 text-xl-center mb-3 mb-xl-0 d-flex align-items-center justify-content-xl-center">
                                <div class="icon icon-shape icon-md icon-shape-brown rounded mr-4"><span class="fas fa-piggy-bank"></span></div>
                                <div class="d-sm-none">
                                    <h2 class="h5 text-mhasibu-brown">SAVINGS</h2>
                                    <h3 class="mb-1"><?=number_format($model->Savings) ?></h3>
                                </div>
                            </div>
                            <div class="col-12 col-xl-7 px-xl-0">
                                <div class="d-none d-sm-block">
                                    <h2 class="h5 text-mhasibu-brown">SAVINGS</h2>
                                    <h3 class="mb-1"><?=number_format($model->Savings )?></h3>
                                </div>
                                <small> <span class="icon icon-small"><span class="fas fa"></span></span> </small> 
                                <div class="small mt-2">                               
                                    <!-- <span class="fas fa-angle-up text-success"></span>                                   
                                    <span class="text-success font-weight-bold">18.2%</span> Since last month -->
                                </div>
                            </div>
                        </div>

                        <div class="row d-block d-xl-flex align-items-center">
                            <div class="col-12 col-xl-5 text-xl-center mb-3 mb-xl-0 d-flex align-items-center justify-content-xl-center">
                                <div class="icon icon-shape icon-md icon-shape-brown rounded mr-4"><span class="fas fa-glass-cheers"></span></div>
                                <div class="d-sm-none">
                                    <h2 class="h5 text-mhasibu-brown">HOLIDAY SCHEME</h2>
                                    <h3 class="mb-1"><?=number_format($model->Holiday_Scheme) ?></h3>
                                </div>
                            </div>
                            <div class="col-12 col-xl-7 px-xl-0">
                                <div class="d-none d-sm-block">
                                <h2 class="h5 text-mhasibu-brown">HOLIDAY SCHEME</h2>
                                    <h3 class="mb-1"><?=number_format($model->Holiday_Scheme )?></h3>
                                </div>
                                <small> <span class="icon icon-small"><span class="fas fa"></span></span> </small> 
                                <div class="small mt-2">                               
                                    <!-- <span class="fas fa-angle-up text-success"></span>                                   
                                    <span class="text-success font-weight-bold">18.2%</span> Since last month -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-xl-4 mb-4">
                <div class="card border-light shadow-sm">
                    <div class="card-body">
                        <div class="row d-block d-xl-flex align-items-center">
                            <div class="col-12 col-xl-5 text-xl-center mb-3 mb-xl-0 d-flex align-items-center justify-content-xl-center">
                                <div class="icon icon-shape icon-md icon-shape-brown rounded mr-4"><span class="fas fa-hand-holding"></span></div>
                                <div class="d-sm-none">
                                    <h2 class="h5 text-mhasibu-brown"> LOAN BALANCE </h2>
                                    <h3 class="mb-1"><?=number_format($model->Loans) ?></h3>
                                </div>
                            </div>
                            <div class="col-12 col-xl-7 px-xl-0">
                                <div class="d-none d-sm-block">
                                    <h2 class="h5 text-mhasibu-brown"> LOAN BALANCE </h2>
                                    <h3 class="mb-1"><?=number_format($model->Loans )?></h3>
                                </div>
                                <small> <span class="icon icon-small"><span class="fas fa"></span></span> </small> 
                                <div class="small mt-2">                               
                                    <!-- <span class="fas fa-angle-up text-success"></span>                                   
                                    <span class="text-success font-weight-bold">18.2%</span> Since last month -->
                                </div>
                            </div>
                        </div>

                        <div class="row d-block d-xl-flex align-items-center">
                            <div class="col-12 col-xl-5 text-xl-center mb-3 mb-xl-0 d-flex align-items-center justify-content-xl-center">
                                <div class="icon icon-shape icon-md icon-shape-brown rounded mr-4"><span class="fas fa-coins"></span></div>
                                <div class="d-sm-none">
                                    <h2 class="h5 text-mhasibu-brown"> DEPOSITS </h2>
                                    <h3 class="mb-1"><?=number_format($model->Deposits) ?></h3>
                                </div>
                            </div>
                            <div class="col-12 col-xl-7 px-xl-0">
                                <div class="d-none d-sm-block">
                                    <h2 class="h5 text-mhasibu-brown"> DEPOSITS </h2>
                                    <h3 class="mb-1"><?=number_format($model->Deposits )?></h3>
                                </div>
                                <small> <span class="icon icon-small"><span class="fas fa"></span></span> </small> 
                                <div class="small mt-2">                               
                                    <!-- <span class="fas fa-angle-up text-success"></span>                                   
                                    <span class="text-success font-weight-bold">18.2%</span> Since last month -->
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>   
        </div>
        <div class="row">
            <div class="col-12 mb-7">
                <div class="card border-light shadow-sm">
                    <div class="card-body">
                    <h2 class="h5 mb-4">My Schemes</h2>

                        <div class="table-responsive">
                            <table class="table table-centered table-nowrap mb-0 rounded">
                                <thead class="thead-brown">
                                    <tr>
                                        <th class="border-0">Scheme</th>
                                        <th class="border-0">Balance</th>
                                        <th class="border-0">Account Status</th>
                                        <th class="border-0">Last Transaction Date</th>
                                        <th class="border-0">Action</th>

                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Item -->
                                    <?php if(isset(Yii::$app->user->identity->getMemberData()->Member_Accounts->Member_Accounts_Listpart)): ?>
                                        <?php foreach(Yii::$app->user->identity->getMemberData()->Member_Accounts->Member_Accounts_Listpart as $Scheme): ?>
                                            <?php
                                                if(strpos($Scheme->Name, 'COLLECTIONS') !== false || strpos($Scheme->Name, 'Insurance') !== false){
                                                    continue;
                                                }
                                            ?>
                                            <tr>

                                                <td class="border-1">                                             
                                                   
                                                    <div><span class="h6"><?= strtoupper($Scheme->Name) ?></span></div>
                                                
                                                </td>

                                                <td class="border-1 ">
                                                    <div><span class="h6"><?= number_format($Scheme->Balance) ?></span></div>
                                                </td>

                                                <td class="border-1">
                                                    <div><span class="h6"><?= $Scheme->Account_Status ?></span></div>
                                                </td>
                                                    <?php
                                                        $topUplink = Html::a('Top Up',['/account-deposit','Account'=> $Scheme->No, 'Key'=>$Scheme->Key ],['title'=>'Deposit Money To Account','class'=>'btn btn-secondary btn-md']);
                                                        $statementLink = Html::a('View Statement',['statement', 'Account'=> $Scheme->No,'Key'=> $Scheme->Key],['class'=>'btn btn-primary btn-md']);
                                                    ?>
                                             
                                                <td class="border-1">
                                                    <div><span class="h6"><?= $Scheme->Last_Transaction_Date ?></span></div>
                                                </td>

                                                <?php if($Scheme->Cash_Deposit_Allowed == 1): ?>
                                                        <td><span class="font-weight-normal"><?= $topUplink .' '. $statementLink ?></span></td>                                                                            
                                                    <?php else: ?>
                                                        <td><span class="font-weight-normal"><?= ' '. $statementLink ?></span></td>                                                                            
                                                <?php endif; ?>
                                                
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
        <div class="col-12 col-xl-12">
                <div class="card card-body shadow-lg mb-1">
                    <h2 class="h5 mb-4">General Information</h2>
                    <form>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <div class="form-group">
                                    <label for="first_name" style="font-weight: 700;">Full Name</label>
                                    <input class="form-control" id="first_name" type="text" readonly placeholder="<?=$model->Name ?>">
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="form-group">
                                    <label for="last_name" style="font-weight: 700;">Member No</label>
                                    <input class="form-control" id="last_name" type="text" readonly placeholder="<?=$model->No ?>" required>
                                </div>
                            </div>
                        </div>
                        <div class="row align-items-center">
                            <div class="col-md-6 mb-3">
                                <div class="form-group">
                                    <label for="birthday" style="font-weight: 700;">Mobile No</label>
                                    <input class="form-control" id="last_name" type="text" readonly placeholder="<?=$model->Phone_No ?>" required>
                                </div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <div class="form-group">
                                    <label for="email" style="font-weight: 700;">Email</label>
                                    <input class="form-control" id="last_name" type="text" readonly placeholder="<?=Yii::$app->user->identity->{'E-Mail'} ?>" required>
                                </div>
                            </div>
                          
                        </div>
                        <div class="row">
                          
                        </div>
                
                    </form>
                </div>
            </div>
    </div>
</div>
