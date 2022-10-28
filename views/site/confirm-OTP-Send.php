<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap4\ActiveForm */
/* @var $model app\models\LoginForm */

use yii\bootstrap4\ActiveForm;
use yii\bootstrap4\Html;
use yii\helpers\Url;

$this->title = 'Login';
$this->params['breadcrumbs'][] = $this->title;
$data =       Yii::$app->session->get('OauthData');
// echo '<pre>';
// print_r($data);
// exit;


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
        <div class="d-block d-sm-flex justify-content-between align-items-center mt-1">
            <div>
                <p>
                    We'll send a code to <b> <u style="color: red;"> <?= $model->MaskedPhone ?> </u> </b> to sign you in.
                </p>
            </div>

        </div>
    </div>



    <div class="form-group">
        <div class="d-block d-sm-flex justify-content-between align-items-center mt-1">
            <?php if ($data && empty($data->password_hash)) : ?>
            <?php else : ?>
                <div>
                    <a href="<?= Url::to(['/site/login-with-password'])  ?>" class="small text-left">Use Password Instead</a>
                </div>
            <?php endif; ?>

        </div>
    </div>

    <div class="form-group">
        <div class="d-block d-sm-flex justify-content-between align-items-center mt-1">

            <div>
                <a href="<?= Url::to('site/login',  $schema = true) ?>" class='btn btn-success text-right' ,> Go Back </a>
            </div>

            <div>
                <?= Html::submitButton('Send Code', ['class' => 'btn btn-default text-right', 'name' => 'login-button',]) ?>

            </div>

        </div>


    </div>


    <?php ActiveForm::end(); ?>





    <!-- <div class="offset-lg-1" style="color:#999;">
        You may login with <strong>admin/admin</strong> or <strong>demo/demo</strong>.<br>
        To modify the username/password, please check out the code <code>app\models\User::$users</code>.
    </div> -->
</div>