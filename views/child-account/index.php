<?php
$this->title = 'Child Accounts';

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

<div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <?= \yii\helpers\Html::a('Apply For Child Account',['create'],['class' => 'btn btn-primary btn-md mr-2', 'data' => [
                    'confirm' => 'Are you sure you want Create a Child Account?',
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
            ajax: absolute+'child-account/get-child-accounts',
            paging: true,
            responsive:true,
            columns: [
                { title: '#', data: 'index'},
                { title: 'First Name' ,data: 'First_Name'},
                { title: 'Last Name' ,data: 'Last_Name'},   
                { title: 'Gender' ,data: 'Gender'}, 
                { title: 'Application Status' ,data: 'Status'},                      
                { title: 'Edit' ,data: 'Update_Action'},
                
            ] ,                              
           language: {
                "zeroRecords": "No Child Applications to Show.."
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
