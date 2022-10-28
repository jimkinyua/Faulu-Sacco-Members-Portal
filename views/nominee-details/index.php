<?php
$this->title = 'Nominees';
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


    <!--THE STEPS THING--->
        <div class="row">
            <div class="col-md-12">
                <?= $this->render('..\profile\_steps', ['model'=>$Applicant]) ?>
            </div>
        </div>

    <!--END THE STEPS THING--->
    <?php if($Applicant->Portal_Status == 'New'): ?>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <?= \yii\helpers\Html::a('Add Nominee',['create','Key'=> $Applicant->Key],['class' => 'create btn btn-info btn-md mr-2 ']) ?>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>


    <div class="card-body">
        <table class="table table-bordered dt-responsive table-hover" id="leaves">
        </table>
    </div>

<input type="hidden" name="absolute" value="<?= Yii::$app->recruitment->absoluteUrl() ?>">
<input type="hidden" name="DocNum" value="<?=$Applicant->Application_No ?>">

<?php

$script = <<<JS

    $(function(){
        
        var absolute = $('input[name=absolute]').val();
        var Docnum = $('input[name=DocNum]').val();

         /*Data Tables*/
         
        // $.fn.dataTable.ext.errMode = 'throw';
        
          $('#leaves').DataTable({
           
            //serverSide: true,  
            ajax: absolute+'nominee-details/getnominees?AppNo='+Docnum,
            paging: true,
            responsive:true,
            columns: [
                { title: '#', data: 'index'},
                { title: 'Relationship' ,data: 'Relationship'},
                { title: 'FullName' ,data: 'FullName'},
                { title: 'National ID No' ,data: 'National_ID_No'},
                { title: 'Email' ,data: 'Email'},
                { title: 'Percent Allocation' ,data: 'Percent_Allocation'},  
                { title: 'Remove' ,data: 'Remove'},             
                { title: 'Edit' ,data: 'Update_Action'},
                
            ] ,                              
           language: {
                "zeroRecords": "No Nominees to Show.."
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








