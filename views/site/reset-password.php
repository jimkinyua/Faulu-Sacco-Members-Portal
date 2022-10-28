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
    <h3 style="color: black;"> Reset Password:</h3>

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



    <div class=" d-lg-grid justify-content-between  mt-1">

        <div class="form-group form-check mt-3 form-check-label">

            <div class="row col-lg-12">

                <div class="col col-lg-6">
                </div>

            </div>


            <!-- <label class="form-check-label form-check-sign-white" for="exampleCheck1">Remember me</label> -->
        </div>







    </div>

    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title" id="from-actions-bottom-right">Reset Password</h4>
                <a class="heading-elements-toggle"><i class="la la-ellipsis-v font-medium-3"></i></a>

            </div>
            <div class="card-content collpase show">
                <div class="card-body">

                    <?php $form = ActiveForm::begin([
                        'id' => 'login-form',
                        'class' => 'form',
                        'options' => [
                            'autocomplete' => 'off',
                        ]
                    ]); ?>

                    <div class="form-body">
                        <!-- <h4 class="form-section"><i class="la la-eye"></i> About User</h4> -->
                        <div class="row">
                            <div class="form-group col-md-6 mb-2">
                                <label for="userinput1">New Password</label>
                                <?= $form->field($model, 'password')->passwordInput([
                                    'autofocus' => true,
                                    'placeholder' => 'Enter New Password'
                                ])->label(false)
                                ?>
                            </div>
                            <div class="form-group col-md-6 mb-2">
                                <label for="userinput2">Repeat Password</label>
                                <?= $form->field($model, 'confirmPassword')->passwordInput([
                                    'autofocus' => true,
                                    'placeholder' => 'Repeat Password'
                                ])->label(false)
                                ?>
                            </div>
                        </div>

                    </div>

                    <div class="form-actions text-right">
                        <?= Html::submitButton('Reset Password', ['class' => 'btn btn-warning', 'name' => 'login-button',]) ?>
                    </div>
                    </form>

                </div>
            </div>
        </div>
    </div>





    <?php ActiveForm::end(); ?>

    <!-- <div class="offset-lg-1" style="color:#999;">
        You may login with <strong>admin/admin</strong> or <strong>demo/demo</strong>.<br>
        To modify the username/password, please check out the code <code>app\models\User::$users</code>.
    </div> -->
</div>