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
    <h5 style="color: black;" class="text-center"> Log in to start your session:</h5>

    <?php
    if (Yii::$app->session->hasFlash('success')) {
        print ' <div class="alert alert-success alert-dismissable">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                        ';
        echo Yii::$app->session->getFlash('success');
        print '</div>';
    } else if (Yii::$app->session->hasFlash('error')) {
        print ' <div class="alert alert-danger alert-dismissable">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                                ';
        echo Yii::$app->session->getFlash('error');
        print '</div>';
    }
    ?>


    <?php $form = ActiveForm::begin([
        'id' => 'login-form',
        'options' => [
            'autocomplete' => 'off',
            'class' => 'mt-4'
        ]
    ]); ?>


    <div class="form-group">
        <?= $form->field($model, 'IDnumber')->textInput(['autofocus' => true, 'class' => 'form-control']) ?>
        <div class="d-block d-sm-flex justify-content-between align-items-center mt-1">
            <div>
                <a href="<?= Url::to(['/site/request-password-reset'])  ?>" class="small text-right">Forgot password?</a>
            </div>
        </div>
    </div>
    <div class="form-group">
        <!-- <?= $form->field($model, 'password')->passwordInput([]) ?> -->
        <div class="d-block d-sm-flex justify-content-between align-items-center mt-1">
            <div>
                <?= Html::submitButton('Login', ['class' => 'btn btn-danger text-left', 'name' => 'login-button',]) ?>

            </div>
            <div>
                <a href="<?= Url::to('application/register',  $schema = true) ?>" class='btn btn-default text-right' ,> Become a Member</a>
            </div>

        </div>
    </div>


    <?php ActiveForm::end(); ?>





    <!-- <div class="offset-lg-1" style="color:#999;">
        You may login with <strong>admin/admin</strong> or <strong>demo/demo</strong>.<br>
        To modify the username/password, please check out the code <code>app\models\User::$users</code>.
    </div> -->
</div>