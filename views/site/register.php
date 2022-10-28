<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap4\ActiveForm */
/* @var $model app\models\LoginForm */

use yii\widgets\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use borales\extensions\phoneInput\PhoneInput;



$this->title = 'Member Registration';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-login">
    <h1 class="text-center"><?= Html::encode($this->title) ?></h1>

    <p class="text-center">Please fill out the following fields to Register:</p>

    <?php $form = ActiveForm::begin(['options' => ['autocomplete' => 'off'],]); ?>

    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'memebershipType')->dropDownList(ArrayHelper::map($MembershipTypes, 'Code', 'Name'), ['prompt' => '--Select MemberShip Type--']) ?>
            <?= $form->field($model, 'email')->textInput(['type' => 'email']) ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'phoneNo')->widget(PhoneInput::className(), [
                'jsOptions' => [
                    'preferredCountries' => ['ke'],
                ]
            ]) ?>
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
            <?= Html::submitButton('Validate', ['class' => 'btn btn-primary', 'name' => 'login-button']) ?>
        </div>

    </div>

    <?php ActiveForm::end(); ?>

    <!-- <div class="offset-lg-1" style="color:#999;">
        You may login with <strong>admin/admin</strong> or <strong>demo/demo</strong>.<br>
        To modify the username/password, please check out the code <code>app\models\User::$users</code>.
    </div> -->
</div>