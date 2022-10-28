<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap4\ActiveForm */
/* @var $model app\models\LoginForm */

use yii\bootstrap4\ActiveForm;
use yii\bootstrap4\Html;
use yii\helpers\Url;

$this->title = 'Login';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-login">
    <h3 style="color: black;"> Create Password:</h3>

    <?php
        if(Yii::$app->session->hasFlash('success')){
            print ' <div class="alert alert-success alert-dismissable">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                        ';
            echo Yii::$app->session->getFlash('success');
            print '</div>';
        }else if(Yii::$app->session->hasFlash('error')){
            print ' <div class="alert alert-danger alert-dismissable">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                                ';
            echo Yii::$app->session->getFlash('error');
            print '</div>';
        }
    ?>


    <?php $form = ActiveForm::begin([
        'id' => 'login-form',
        'options'=>[
           'autocomplete' => 'off',
        ]
        ]); ?>


        <div class="form-group">
            
            <?= $form->field($model, 'password')->passwordInput([
                            'autofocus' => true,
                            'placeholder' => 'Enter Password'
                        ])->label(false) 
            ?>

             <?= $form->field($model, 'password_repeat')->passwordInput([
                            'autofocus' => true,
                            'placeholder' => 'Repeat Password'
                        ])->label(false) 
            ?>


        </div>

        <div class=" d-lg-grid justify-content-between  mt-1">

            <div class="form-group form-check mt-3 form-check-label">
                
                <div class="row col-lg-12">

                    <div class="col col-lg-6" >
                        <?= Html::submitButton('Create Password', ['class' => 'btn btn-primary', 'name' => 'login-button',]) ?>  
                    </div>                    
                
                </div>
                   
                
                <!-- <label class="form-check-label form-check-sign-white" for="exampleCheck1">Remember me</label> -->
            </div>
            

            

            


        </div>
        
    
        


    <?php ActiveForm::end(); ?>

    <!-- <div class="offset-lg-1" style="color:#999;">
        You may login with <strong>admin/admin</strong> or <strong>demo/demo</strong>.<br>
        To modify the username/password, please check out the code <code>app\models\User::$users</code>.
    </div> -->
</div>
