<?php

use yii\widgets\ActiveForm;
use yii\helpers\Html;
use kartik\depdrop\DepDrop;
use yii\helpers\Url;

$this->title = 'Downloads';
// echo '<pre>';
// print_r($Documents);
// exit;
?>

<?php $form = ActiveForm::begin(); ?>
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


    <div class="col-md-12">

        <div class="card card-success ">
         
            <div class="card-body">
            <div class="table-responsive">
                            <table class="table table-centered table-nowrap mb-0 rounded">
                                <thead class="thead-brown">
                                    <tr>
                                        <th >Document Name</th>
                                        <th > </th>
                               
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Item -->
                                    <?php if($Documents): ?>
                                        <?php rsort($Documents) ?>
                                        <?php foreach($Documents as $Document): ?>
                                            <?php
                                                $link = Html::a('Download',['read','No'=> $Document['Name'] ],['title'=>'Read File','class'=>'btn btn-primary btn-md', 'target'=>'_blank']);
                                            ?>
                                            <tr>

                                                <td class="">                                             
                                                   
                                                    <div><span class="h6"><?= strtoupper($Document['Name']) ?></span></div>
                                                
                                                </td>

                                                <td class="">
                                                    <div><span class="h6"><?= $link ?></span></div>
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

        <?php ActiveForm::end(); ?>


