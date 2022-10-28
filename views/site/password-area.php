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
    <h3 style="color: black;">Enter Your Password</h3>


    <?php $form = ActiveForm::begin([
        'id' => 'login-form',
        'options'=>[
           'autocomplete' => 'off',
        ]
        ]); ?>

        <div class="form-group">
            
            <?= $form->field($model, 'password')->passwordInput([
                            'autofocus' => true,
                            'placeholder' => ''
                        ])->label(false) 
            ?>


        </div>

        <div class=" d-lg-grid justify-content-between  mt-1">

            <div class="form-group form-check mt-3 form-check-label">
                
                <div class="row col-lg-12">

                    <div class="col col-lg-6" >
                        <?= Html::submitButton('Next', ['class' => 'btn btn-primary', 'name' => 'login-button',]) ?>  
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
