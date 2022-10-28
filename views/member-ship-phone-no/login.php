<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap4\ActiveForm */
/* @var $model app\models\LoginForm */

use yii\bootstrap4\ActiveForm;
use yii\bootstrap4\Html;

$this->title = 'Login';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-login">
    <h1><?= Html::encode($this->title) ?></h1>

    <p>Please fill out the following fields to login:</p>

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

        <div class="form-group">
            
            <?= $form->field($model, 'email')->textInput([
                            'autofocus' => true,
                            'placeholder' => 'Enter email'
                        ])->label(false) ?>


            <?= $form->field($model, 'password')->passwordInput([ 
                'placeholder' => 'Enter Your Password'
                ])->label(false) ?>
                

        </div>

        <div class="d-block d-sm-flex justify-content-between align-items-center mt-2">

            <div class="form-group form-check mt-3 form-check-label form-check-sign-white">
                <?= $form->field($model, 'rememberMe')->checkbox([
                    // 'template' => "<div class=\"form-check-label form-check-sign-white\">{input} {label}</div>\n<div class=\"col-lg-8\">{error}</div>",
                ]) ?>
                <!-- <label class="form-check-label form-check-sign-white" for="exampleCheck1">Remember me</label> -->
            </div>

            <div><a href="./forgot-password.html" class="small text-right">Forgot password?</a></div>

        </div>
        
    
        

       

        <div class="form-group">
            <div class="offset-lg-1 col-lg-11">
                <?= Html::submitButton('Login', ['class' => 'btn btn-primary', 'name' => 'login-button']) ?>
            </div>
        </div>

    <?php ActiveForm::end(); ?>

    <!-- <div class="offset-lg-1" style="color:#999;">
        You may login with <strong>admin/admin</strong> or <strong>demo/demo</strong>.<br>
        To modify the username/password, please check out the code <code>app\models\User::$users</code>.
    </div> -->
</div>
