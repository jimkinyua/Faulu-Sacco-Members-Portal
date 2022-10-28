<?php
$webroot = Yii::getAlias(@$webroot);
$absoluteUrl = \yii\helpers\Url::home(true);
$this->title = 'Loan Appraisal Report';
?>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Loan Appraisal Report</h3>

                </div>
                <div class="card-body">
                    <!--<iframe src="data:application/pdf;base64,<?/*= $content; */?>" height="950px" width="100%"></iframe>-->
                    <?php
                    if($report){ ?>
                        <iframe src="data:application/pdf;base64,<?= $content; ?>" height="950px" width="100%"></iframe>
                   <?php } ?>



                </div>
            </div>
        </div>
    </div>

<?php
$script  = <<<JS
        let currentTabId = 0// default

$('.ErrorPage').hide()
$('.submitButton').show();
$('.ButtonPreloader').hide();

const Tabs = [];

const DisableTabs = (TabIds)=>{
    console.log(TabIds)
    // if(element.id < ){

    // }
    TabIds.forEach((elementId, index)=>{
        console.log( parseInt(currentTabId))
        if( parseInt(elementId.id) < parseInt(currentTabId)){
            return true;
        }else{
            elementId.href = "javascript:void(0)";
        }
    })
}

$('.breadcrumbb').find('a').each((index, element)=>{
if(element.className == 'active'){ //Don't Disble Current Tab
    currentTabId = element.id;
}
Tabs.push(element);
})

DisableTabs(Tabs)

$('#confrimation-form').on('beforeSubmit', function () {
    $('.ButtonPreloader').show();
    $('.submitButton').hide();
    if( parseInt($('#NumberOfWitnesses').val()) <= 0){
        alert('Kindly Add A Witness')
        $('.ButtonPreloader').hide();
        $('.submitButton').show();
    }else if(parseInt($('#NumberOfWitnesses').val()) > 1){
        alert('You Can Add a Maximum of 1 Witness')
        $('.ButtonPreloader').hide();
        $('.submitButton').show();
    }
    
    else{
        window.location.replace('/loan/send-for-approval?Key='+$('#onllineguarantorrequests-loanformkey').val());
    }

    return false; // prevent default form submission
})
JS;

$this->registerJs($script, yii\web\View::POS_READY);










