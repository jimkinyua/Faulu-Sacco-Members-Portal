<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap4\ActiveForm */
/* @var $model app\models\LoginForm */

use yii\widgets\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use borales\extensions\phoneInput\PhoneInput;


$this->title = 'Login';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-login">


    <?php $form = ActiveForm::begin(['id' => 'login-form', 'options'=> ['autocomplete' => 'off']]); ?>

        <div class="form-group">
            
            <?= $form->field($model, 'memebershipType')->dropDownList(ArrayHelper::map($MembershipTypes, 'Code', 'Name'), ['prompt' => 'Select Membership Type'])?>

            <?= $form->field($model, 'phoneNo')->widget(PhoneInput::className(), [
                                'jsOptions' => [
                                    'preferredCountries' => ['ke'],
            ]]) ?>

        </div>
        

        <div class="d-block d-sm-flex justify-content-between align-items-center mt-2">


        </div>
        
    
        

       

        <div class="form-group">
            <div class="offset-lg-1 col-lg-11">
                <?= Html::submitButton('Submit', ['class' => 'btn btn-success', 'name' => 'login-button']) ?>
            </div>
        </div>

    <?php ActiveForm::end(); ?>

    <!-- <div class="offset-lg-1" style="color:#999;">
        You may login with <strong>admin/admin</strong> or <strong>demo/demo</strong>.<br>
        To modify the username/password, please check out the code <code>app\models\User::$users</code>.
    </div> -->
</div>
