<?php
$this->title = 'Pending Loan Applications';
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
        
        var absolute = $('input[name=absolute]').val();
         /*Data Tables*/
         
        // $.fn.dataTable.ext.errMode = 'throw';
        
    
          $('#leaves').DataTable({
           
            //serverSide: true,  
            ajax: absolute+'loan/get-pending-loans',
            paging: true,
            responsive:true,
            columns: [
                { title: '#', data: 'index'},
                { title: 'Loan Type' ,data: 'Loan_Product'},
                { title: 'Application Date' ,data: 'Application_Date'},   
                { title: 'Applied Amount' ,data: 'Applied_Amount'},     
                { title: 'Approved Amount' ,data: 'Approved_Amount'},                                 
                { title: 'Status' ,data: 'Status'},
                
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
