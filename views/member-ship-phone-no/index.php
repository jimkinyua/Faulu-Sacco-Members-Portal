<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap4\ActiveForm */
/* @var $model app\models\LoginForm */

use yii\bootstrap4\ActiveForm;
use yii\bootstrap4\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;


$this->title = 'Member Registration';
$this->params['breadcrumbs'][] = $this->title;
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
<div class="site-login">
    <h1><?= Html::encode($this->title) ?></h1>

    <p>Please fill out the following fields to Register:</p>

    <?php $form = ActiveForm::begin([
        'id' => 'login-form',
        // 'layout' => 'horizontal',
        'class'=>'mt-5',
        'fieldConfig' => [
            // 'template' => "{label}\n{input}\n{error}",
            // 'labelOptions' => ['class' => 'col-lg-1 col-form-label mr-lg-3'],
            'inputOptions' => ['class' => 'form-control col-lg-12'],
            // 'errorOptions' => ['class' => 'invalid-feedback'],
        ],

        ]); ?>

      
        
            <div class="row"> 
                <div class="col-md-6">
                    <div class="form-group">
                        
         
                        <?= $form->field($model, 'phoneNo')->textInput([
                        'placeholder' => 'Phone No'
                        ])->label(false) ?>


                        <?= $form->field($model, 'memebershipType')->dropDownList(ArrayHelper::map($MembershipTypes, 'Code', 'Name'), ['prompt' => 'Select Memebership Type'])->label(false) 
                            ?>



                    </div>
                </div>
          
            </div>
            

        
      

        <div class="d-block d-sm-flex justify-content-between align-items-center mt-2">

            <div class="form-group form-check mt-3 form-check-label form-check-sign-white">
                <?= $form->field($model, 'agreeToTerms')->checkbox([
                    // 'template' => "<div class=\"form-check-label form-check-sign-white\">{input} {label}</div>\n<div class=\"col-lg-8\">{error}</div>",
                ]) ?>
                <!-- <label class="form-check-label form-check-sign-white" for="exampleCheck1">Remember me</label> -->
            </div>


        </div>
        
    
        

       

        <div class="form-group">
            <div class="offset-lg-1 col-lg-11">
                <?= Html::submitButton('Register', ['class' => 'btn btn-primary', 'name' => 'login-button']) ?>
            </div>
         
        </div>

    <?php ActiveForm::end(); ?>

    <!-- <div class="offset-lg-1" style="color:#999;">
        You may login with <strong>admin/admin</strong> or <strong>demo/demo</strong>.<br>
        To modify the username/password, please check out the code <code>app\models\User::$users</code>.
    </div> -->
</div>
