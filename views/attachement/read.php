<?php
/**
 * Created by PhpStorm.
 * User: HP ELITEBOOK 840 G5
 * Date: 2/26/2020
 * Time: 10:59 PM
 */



/* @var $this yii\web\View */

$this->title = 'Read Document';
$this->params['breadcrumbs'][] = ['label' => 'Documents', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => 'Document Viewer', 'url' => ['read','docNo'=> $_GET['Key']]];

?>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Document View</h3>

                </div>
                <div class="card-body">

                                      

                        <iframe src="data:application/pdf;base64,<?= $content; ?>" height="950px" width="100%"></iframe>
                  



                </div>
            </div>
        </div>
    </div>

<?php
$script  = <<<JS
   
JS;

$this->registerJs($script, yii\web\View::POS_READY);










