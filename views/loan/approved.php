<?php
$this->title = 'Disbursed Loans';
?>


<div class="card-body">
    <h1> <?= $this->title ?></h1>
    <table class="table table-bordered dt-responsive table-hover" id="leaves">
    </table>
</div>
<input type="hidden" name="absolute" value="<?= Yii::$app->recruitment->absoluteUrl() ?>">

<?php

$script = <<<JS

    $(function(){


          $('#leaves').on('click','.create', function(e){
                 e.preventDefault();
                var url = $(this).attr('href');
                console.log('clicking...');
                $('.modal').modal('show')
                                .find('.modal-body')
                                .load(url); 
    
            });
        
        var absolute = $('input[name=absolute]').val();
         /*Data Tables*/
         
        // $.fn.dataTable.ext.errMode = 'throw';
        
    
          $('#leaves').DataTable({
           
            //serverSide: true,  
            ajax: absolute+'loan/get-approved-loans',
            paging: true,
            responsive:true,
            columns: [
                { title: '#', data: 'index'},
                { title: 'Loan Type' ,data: 'Loan_Product'},
                // { title: 'Applied Amount' ,data: 'Applied_Amount'},
                { title: 'Loan Amount' ,data: 'LoanAmount'}, 
                { title: 'Application Date' ,data: 'Application_Date'},   
                // { title: 'Repayment Start Date' ,data: 'Repayment_Start_Date'}, 
                // { title: 'Repayment End Date' ,data: 'Repayment_End_Date'}, 
                { title: 'Installments' ,data: 'Installments'}, 
                { title: 'Principal Balance' ,data: 'Principle_Balance'}, 
                { title: 'Interest Balance' ,data: 'Loan_Interest_Repayment'},   
   
                // { title: ' ' ,data: 'Update_Action'},
                
            ] ,                              
           language: {
                "zeroRecords": "No Loans to Show.."
            },
            
            order : [[ 0, "asc" ]]
    
       });
        
       //Hidding some 
       var table = $('#leaves').DataTable();
    //   table.columns([0]).visible(false);
    
    /*End Data tables*/

    

    });
        
JS;

$this->registerJs($script);
