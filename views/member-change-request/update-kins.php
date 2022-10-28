<?php

use yii\bootstrap4\Html;
use yii\helpers\Url;

$KinPercentageTotal = 0;
// echo '<pre>';
// print_r($model);
// exit;

?>


<div class="col-md-12">
    <div class="card">
        <div class="card-header">
            <h4 class="card-title" id="from-actions-bottom-right">Employment Details</h4>
            <a class="heading-elements-toggle"><i class="la la-ellipsis-v font-medium-3"></i></a>

            <input type="hidden" name="absolute" value="<?= Yii::$app->recruitment->absoluteUrl() ?>">
            <input type="hidden" name="DocNum" value="<?= $model->Document_No ?>">
        </div>
        <div class="card-content collpase show">
            <div class="card-body">
                <div class="form-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-body">
                                    <?= \yii\helpers\Html::a('Add Kin', Url::to(['member-change-request/create-kin', 'Key' => $model->Key]), ['class' => 'create btn btn-info btn-md mr-2 ']) ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- <h4 class="form-section"><i class="la la-eye"></i> About User</h4> -->
                    <div class="row">
                        <div class="form-group col-md-12 mb-2">
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
                                            <td><span class="font-weight-normal"> <?= $Kin->Kin_Type ?><span></td>
                                            <td><span class="font-weight-normal"> <?= $Kin->Date_of_Birth ?><span></td>
                                            <?php
                                            $KinPercentageTotal += $Kin->Allocation;
                                            $updateLink = Html::a('Update Details', Url::to(['member-change-request/update-kin', 'Key' => urlencode($Kin->Key)]), ['class' => 'update btn btn-info btn-sm']);
                                            $deletelink = Html::a('Remove Kin', Url::to(['member-change-request/delete-kin', 'Key' => urlencode($Kin->Key)]), ['class' => 'btn btn-danger btn-sm']);
                                            ?>
                                            <td><span class="font-weight-normal"><?= $updateLink . ' ' . $deletelink ?></span></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>
                <input type="hidden" name="TotalPercentage" value="<?= $KinPercentageTotal ?>" id="TotalPercentage">


                <div class="form-actions text-left">
                    <div class="form-actions text-left">
                        <?= Html::submitButton('Submit For Approval', ['class' => 'btn btn-success SubmitButton', 'name' => 'login-button',]) ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php

$script = <<<JS

$('.SubmitButton').on('click', function () {
    $(this).html('Validating. Please Wait...');
    // $('.submitButton').hide();

    let TotalPercentage = $('#TotalPercentage').val()
        // alert(TotalPercentage)

    if(TotalPercentage == 100){
        var yiiform = $(this);
        $.ajax({
                type: yiiform.attr('method'),
                url: yiiform.attr('action'),
                data: yiiform.serializeArray(),
            }
        )
            .done(function(data) {
                if(data.success) {
                    // data is saved
                    $('.ErrorPage').text('');
                    $('.ErrorPage').hide();
                } else if (data.validation) {
                    // server validation failed
                    yiiform.yiiActiveForm('updateMessages', data.validation, true); // renders validation messages at appropriate places
                    // anchor.href = "javascript:void(0)";
                    $('.ErrorPage').text(data.validation);
                    $('.ErrorPage').show();
                    $('.submitButton').show();
                    $('.ButtonPreloader').hide();
                    $('.breadcrumbb').find('a').each((index, element)=>{
                        if(element.className == 'active'){ //Don't Disble Current Tab
                            return true;
                        }
                        Tabs.push(element);
                    })
                    // DisableTabs(Tabs)

                }
                
                else if (data.error) {
                    // server validation failed
                    $('.ErrorPage').text(data.error);
                    $('.ErrorPage').show();
                    $('.submitButton').show();
                    $('.ButtonPreloader').hide();
                    // anchor.href = "javascript:void(0)";
                    $('.breadcrumbb').find('a').each((index, element)=>{
                        if(element.className == 'active'){ //Don't Disble Current Tab
                            return true;
                        }
                        Tabs.push(element);
                    })
                    DisableTabs(Tabs)

                }

                else {
                    // incorrect server response
                }
            })
            .fail(function () {
                // request failed
            })
    }else{
        alert('The Allocation Should Add up to  100% , The total is '+TotalPercentage)
    }

    return false; // prevent default form submission
})

    $(function(){
        
        var absolute = $('input[name=absolute]').val();
        var Docnum = $('input[name=DocNum]').val();        
            $('table').on('click','.update', function(e){
                 e.preventDefault();
                var url = $(this).attr('href');
                console.log('clicking...');
                $('.modal').modal('show')
                                .find('.modal-body')
                                .load(url); 
    
            });
            
                    
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
