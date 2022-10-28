
<div class="row">
    <div class="col-12 mb-7">
        <div class="card border-light shadow-sm">
            <div class="card-body">
            <h2 class="h5 mb-4">My Loan Arrears</h2>

                <div class="table-responsive">
                    <table class="table table-centered table-nowrap mb-0 rounded">
                        <thead class="thead-brown">
                            <tr>
                                <th class="border-0">Loan No</th>
                                <th class="border-0">Application Date</th>
                                <th class="border-0">Product Description</th>
                                <th class="border-0">Approved Amount</th>
                                <th class="border-0">Repayment Start Date</th>
                                <th class="border-0">Repayment End Date</th>
                                <th class="border-0">Loan Balance</th>
                                <th class="border-0">Total Arrears</th>

                            </tr>
                        </thead>
                        <tbody>
                            <!-- Item -->
                            <?php if(is_array($LoansInAreas)): ?>
                                <?php foreach($LoansInAreas as $LoansInArea): ?>
                                    <tr>



                                        <td class="border-1">                                             
                                            
                                            <div><span class="h6"><?= strtoupper(@$LoansInArea['Application_No']) ?></span></div>
                                        
                                        </td>

                                        <td class="border-1 ">
                                            <div><span class="h6"><?=  date_format( date_create(@$LoansInArea['Application_Date']), 'jS F Y')  ?></span></div>
                                        </td>

                                        <td class="border-1">
                                            <div><span class="h6"><?= @$LoansInArea['Product_Description'] ?></span></div>
                                        </td>

                                        <td class="border-1">
                                            <div><span class="h6"><?= number_format(@$LoansInArea['Approved_Amount']) ?></span></div>
                                        </td>

                                        

                                        <td class="border-1">
                                            <div><span class="h6"><?= date_format( date_create(@$LoansInArea['Repayment_Start_Date']), 'jS F Y');  ?></span></div>
                                        </td>

                                        <td class="border-1">
                                            <div><span class="h6"><?= date_format( date_create(@$LoansInArea['Repayment_End_Date']), 'jS F Y');  ?></span></div>
                                        </td>

                                        <td class="border-1">
                                            <div><span class="h6"><?= number_format(@$LoansInArea['Loan_Balance']);  ?></span></div>
                                        </td>

                                        
                                        <td class="border-1">
                                            <div><span class="h6" style="color: red;font-weight: bold;font-size: x-large;"><?=  number_format(@$LoansInArea['Total_Arrears' ]); ?></span></div>
                                        </td>

                                    

                                        
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