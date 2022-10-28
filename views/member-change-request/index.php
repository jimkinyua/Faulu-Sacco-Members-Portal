<?php
$this->title = 'My Change Request Applications';
?>

<div class="row">
    <div class="col-md-12">
        <?php

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
        ?>
    </div>
</div>

<!--END THE STEPS THING--->

<div class="row">

    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
                <div>
                    <h1> <?= $this->title ?></h1>

                </div>
                <?= \yii\helpers\Html::a('Create Change Request', ['create'], ['class' => 'btn btn-success btn-md mr-2', 'data' => [
                    'confirm' => 'Are you sure you want to Create a Change Request?',
                    'method' => 'get',
                ],]) ?>
            </div>

        </div>
    </div>
</div>

<div class="card-body">
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
            ajax: absolute+'member-change-request/get-change-request',
            paging: true,
            responsive:true,
            columns: [
                { title: '#', data: 'index'},
                { title: 'First Name' ,data: 'First_Name'},
                { title: 'Type of Change' ,data: 'Type_of_Change'},
                { title: 'Middle_Name' ,data: 'Middle_Name'},
                { title: 'Last Name' ,data: 'Last_Name'},
                { title: 'National No' ,data: 'National_ID_No'}, 
                { title: 'Status' ,data: 'Portal_Status'},   
                { title: 'Edit' ,data: 'Update_Action'},
                
            ] ,                              
           language: {
                "zeroRecords": "No Change Requests to Show.."
            },
            
            order : [[ 0, "asc" ]]
            
           
       });
        
       //Hidding some 
       var table = $('#leaves').DataTable();
    //   table.columns([0]).visible(false);
    
    /*End Data tables*/
            $('#leaves').on('click','.update', function(e){
                 e.preventDefault();
                var url = $(this).attr('href');
                console.log('clicking...');
                $('.modal').modal('show')
                                .find('.modal-body')
                                .load(url); 
    
            });
            
            
           //Add an experience
        
         $('.create').on('click',function(e){
            e.preventDefault();
            var url = $(this).attr('href');
            console.log('clicking...');
            $('.modal').modal('show')
                            .find('.modal-body')
                            .load(url); 
    
         });
        
        /*Handle dismissal eveent of modal */
        $('.modal').on('hidden.bs.modal',function(){
            var reld = location.reload(true);
            setTimeout(reld,1000);
        });
    });
        
JS;

$this->registerJs($script);
