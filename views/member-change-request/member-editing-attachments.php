<?php 
use yii\helpers\Html;
?>

<div class="row">
    <div class="col-md-12">
        <h5>Documents <strong>(Ensure all your files have different names). For large file(s) compress using <a href="https://www.ilovepdf.com/compress_pdf" target="_blank" >PDF COMPRESSOR</a></strong></h5>
            <table class="table">
                    <tr>
                        <th>Attachments</th>
                        <th></th>
                        <th>Current Document</th>
                    </tr>
                    <?php foreach($RequiredAttachements as $RequiredAttachement): ?>
                            <tr>
                                <td> <?= $RequiredAttachement['Description'] ?> </td>
                                    <?php
                                        $required = 'required';
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
                

        
    </div>
</div>