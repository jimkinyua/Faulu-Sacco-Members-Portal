<?php
use yii\helpers\Html;
$this->title = 'Read Document';
$this->params['breadcrumbs'][] = ['label' => 'Documents', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => 'Document Viewer', 'url' => ['read','docNo'=> $_GET['No']]];

?>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <?= Html::a('Go Back',['index','Key'=> $ApplicationData->Key ],['title'=>'Go Back','class'=>'btn btn-success btn-md']) ?> 
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










