<?php
$this->title = 'Business Attachements';
use yii\widgets\ActiveForm;
use yii\helpers\Html;
// echo '<pre>';
// print_r($MyAttachedDocs);
// exit;
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
            <?= $this->render('..\loan\LoanSteps', ['model'=>$LoanModel]) ?>
            <div class="card">
                <div class="card-header">
                    <div class="alert " role="alert" style="background-color: darkgoldenrod;">
                    <h5 style="color: white;"> Provide to us with the following attachments. </h5>
                </div>
            </div>
        <!-- </div> -->

<?php $form = ActiveForm::begin(['id' => 'biodataForm','options' => ['enctype' => 'multipart/form-data']]); ?>
    <div class="row">
        <div class="col-md-12">
            <h5> <strong>(Ensure all your files have different names). For large file(s) compress using <a href="https://www.ilovepdf.com/compress_pdf" target="_blank" >PDF COMPRESSOR</a></strong></h5>
                <table class="table">
                        <tr>
                            <th>Attach Documents That Apply To Your Scenario</th>
                            <th></th>
                            <th>Current Document</th>
                        </tr>
                        <?php foreach($RequiredAttachements as $RequiredAttachement): ?>
                                <tr>
                                    <td> <?= $RequiredAttachement['Description'] ?> </td>
                                        <?php
                                            $required = '';
                                             if($MyAttachedDocs){
                                                foreach($MyAttachedDocs as $MyAttachedDocument){
                                                    if($MyAttachedDocument->FileName ==$RequiredAttachement['Description']) $required = '';
                                                  }
                                             }
                                           
                                            // echo '<pre>';
                                            // print_r($required);
                                            // exit;
                                           // if($requiredDoc->Mandatory && !@$MyDocs[$requiredDoc->DocumentID]) $rrrq = 'required = "required"';
                                        ?>
                                    <td>
                                        <input type="file" accept=".jpg,.png,.pdf,.xlsx,.docx,.xls"  name="<?=@$RequiredAttachement['Description']?>"  <?=$required ?>> 
                                    </td>
                                     <td>
                                        <table class="table table-condensed" style="margin: 0px;">
                                            <?php if($MyAttachedDocs): ?>
                                                <?php foreach($MyAttachedDocs as $MyAttachedDocument):?>
                                                    <?php if($MyAttachedDocument->FileName ==$RequiredAttachement['Description']): ?>
                                                        <tr>
                                                            <td><?=Html::a($MyAttachedDocument->FileName,['read','Key'=> $MyAttachedDocument->Key ]);?></td>
                                                            <!-- <td width="15%"><?=Html::a('Delete', ['delete-application-file', 'filename' => $MyAttachedDocument->Key], ['class' => 'btn btn-sm btn-danger']);?></td> -->
                                                        </tr>
                                                    <?php endif; ?>
                                                
                                                <?php endforeach; ?>
                                            <?php endif; ?>                                          
                                        </table>
                                    </td>
                                </tr>
                        <?php endforeach; ?>

                    </table>
                  
                <?= Html::submitButton('Upload Files', ['class' => 'btn btn-success']) ?>

            
        </div>
    </div>
    
<?php ActiveForm::end(); ?>
    <?php

$script = <<<JS
        
JS;

$this->registerJs($script);
