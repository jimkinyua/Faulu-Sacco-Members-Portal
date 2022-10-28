<?php

use yii\bootstrap4\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;


$webroot = Yii::getAlias(@$webroot);
$absoluteUrl = \yii\helpers\Url::home(true);
$Kins = $Applicant->getMemberApplicationKins();
$KinPercentageTotal = 0;

// echo '<pre>';
// print_r($Kins);
// exit;

$this->title = 'Next of Kins';
?>


<!--THE STEPS THING--->
<div class="row">
    <div class="col-md-12">
        <?= $this->render('..\profile\_steps', ['model' => $Applicant]) ?>
    </div>
</div>

<!--END THE STEPS THING--->


<div class="col-md-12">
    <?php
    if (Yii::$app->session->hasFlash('error')) {
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

<div class="card card-success ">
    <input type="hidden" name="absolute" value="<?= Yii::$app->recruitment->absoluteUrl() ?>">
    <input type="hidden" name="DocNum" value="<?= $Applicant->No ?>">
</div>
<?php if (is_array($Kins)) : ?>
    <table class="table-sm">
        <thead>
            <tr>
                <th class="border-2">Name</th>
                <th class="border-2">Allocation</th>
                <th class="border-2"> Kin Type </th>
                <th class="border-2"> Date of Birth</th>
                <th class="border-2">Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($Kins as $Kin) : ?>
                <tr>
                    <td><span class="font-weight-normal"> <?= $Kin->Name ?><span></td>
                    <td><span class="font-weight-normal"> <?= $Kin->Allocation ?><span></td>
                    <td><span class="font-weight-normal"> <?= $Kin->Relationship ?><span></td>
                    <td><span class="font-weight-normal"> <?= $Kin->Date_of_Birth ?><span></td>
                    <?php
                    $KinPercentageTotal += $Kin->Allocation;
                    $updateLink = Html::a('Update', Url::to(['kin/update', 'Key' => urlencode($Kin->Key)]), ['class' => 'update btn btn-info btn-sm']);
                    $deletelink = Html::a('Delete', Url::to(['kin/delete', 'Key' => urlencode($Kin->Key)]), ['class' => 'btn btn-danger btn-sm']);
                    ?>

                    <td><span class="font-weight-normal"><?= $updateLink . ' ' . $deletelink ?></span></td>



                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>
<input type="hidden" name="TotalPercentage" value="<?= $KinPercentageTotal ?>" id="TotalPercentage">




<?= $this->render('./_form', ['model' => $model, 'attachments' => $attachments, 'RelationshipTypes'=>$RelationshipTypes]) ?>

<div class="text-left">
    <?= Html::a('Previous Page', Url::to(['employment-details/index', 'Key' => $model->Key]), ['class' => 'btn btn-info mr-1',]) ?>
</div>

<div class="text-right">
    <div class="form-group">
        <img class="loader-element ButtonPreloader" src="<?= $webroot ?>/html&css/assets/img/brand/preloader-spinner.gif" height="50" alt="Faulu logo">
        <?= Html::Button('Next Page', ['class' => 'btn btn-info NextPage']) ?>
    </div>

</div>

<?php

$script = <<<JS
$('.ButtonPreloader').hide();
$('.submitButton').show();
// table = $('#leaves').DataTable()
    // console.log(table.column( 3 ).data().sum());

$('.NextPage').on('click', function () {
    $(this).html('Validating. Please Wait...');
    // $('.submitButton').hide();

    let TotalPercentage = $('#TotalPercentage').val()
        console.log(TotalPercentage)

    if(TotalPercentage == 100){
        window.location.replace('/subscriptions/index?Key='+$('#memberapplication_kins-key').val());
    }else{
        alert('The Allocation Should Add up to  100% , The total is '+TotalPercentage)
        $('.ButtonPreloader').hide();
        $('.submitButton').show();
        $(this).html('Next Page');

    }

    return false; // prevent default form submission
})

    $(function(){
        
        var absolute = $('input[name=absolute]').val();
        var Docnum = $('input[name=DocNum]').val();
         /*Data Tables*/
         
        // $.fn.dataTable.ext.errMode = 'throw';
        
    
          $('#leaves').DataTable({
           
            //serverSide: true,  
            ajax: absolute+'kin/getkins?AppNo='+Docnum,
            paging: true,
            responsive:true,
            columns: [
                { title: '#', data: 'index'},
                { title: 'Relationship' ,data: 'Type'},
                { title: 'Full Names' ,data: 'Name'},
                { title: 'Date of Birth' ,data: 'DOB'},
                { title: 'Phone No' ,data: 'Phone_No'},
                // { title: 'Remove' ,data: 'Remove'},               
                { title: 'Allocation' ,data: 'Allocation'},             
                { title: 'Edit' ,data: 'Update_Action'},
                
            ] ,   
             drawCallback: function () {
                var sum = $('#leaves').DataTable().column(5).data().sum();
                $('#TotalPercentage').val(sum)
                console.log($('#TotalPercentage').val())

            },                           
           language: {
                "zeroRecords": "No Next of Kins to Show.."
            },
            
            order : [[ 0, "asc" ]]
            
           
       });
        
       //Hidding some 
       var table = $('#leaves').DataTable();

    
    /*End Data tables*/
            $('table').on('click','.update', function(e){
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
