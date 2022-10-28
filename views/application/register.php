<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap4\ActiveForm */
/* @var $model app\models\LoginForm */

use yii\bootstrap4\ActiveForm;
use yii\bootstrap4\Html;
use borales\extensions\phoneInput\PhoneInput;

$webroot = Yii::getAlias(@$webroot);
$absoluteUrl = \yii\helpers\Url::home(true);

$this->title = 'Member Registration';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-login">
    <h1 class="text-center"><?= Html::encode($this->title) ?></h1>

    <p class="text-center">Please fill out the following fields to Register:</p>

    <?php $form = ActiveForm::begin(['options' => ['autocomplete' => 'off', 'id' => 'RegistrationForm'],]); ?>

    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'NationalID')->textInput([]) ?>
            <?= $form->field($model, 'email')->textInput(['type' => 'email']) ?>

        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'firstName')->textInput([]) ?>

            <?= $form->field($model, 'phoneNo')->widget(PhoneInput::className(), [
                'jsOptions' => [
                    'preferredCountries' => ['ke'],
                ]
            ]) ?>

        </div>
    </div>
    <div class="row">
        <div class="col-6">
            <?= $form->field($model, 'IdentificationType')->radioList(
                [
                    0 => 'Kenyan Citizen(with Kenyan ID card)',
                    1 => 'Foreign Resident (with Alien ID card)',
                ]
            )
            ?>
        </div>
    </div>



    <div class="d-block d-sm-flex justify-content-between align-items-center mt-2">



    </div>


    <div class="form-group">
        <div class="offset-lg-1 col-lg-11">
            <img class="loader-element ButtonPreloader" src="<?= $webroot ?>/html&css/assets/img/brand/preloader-spinner.gif" height="50" alt="Preloader">
            <?= Html::submitButton('Validate', ['class' => 'btn btn-primary submitButton', 'name' => 'login-button']) ?>
        </div>

    </div>

    <?php ActiveForm::end(); ?>

    <!-- <div class="offset-lg-1" style="color:#999;">
        You may login with <strong>admin/admin</strong> or <strong>demo/demo</strong>.<br>
        To modify the username/password, please check out the code <code>app\models\User::$users</code>.
    </div> -->
</div>


<?php

$script = <<<JS

    $('.ErrorPage').hide()
    $('.submitButton').show();
    $('.ButtonPreloader').hide();

    $('#RegistrationForm').on('beforeSubmit', function () {
        // $('.ButtonPreloader').show();
        // $('.submitButton').hide();

        var yiiform = $(this);
        $.ajax({
                type: yiiform.attr('method'),
                url: yiiform.attr('action'),
                data: yiiform.serializeArray(),
            }
        )
        .done(function(data) {
            if(data.success) {
                // data is saved
                $('.ErrorPage').text('');
                $('.ErrorPage').hide();
            } else if (data.validation) {
                // server validation failed
                yiiform.yiiActiveForm('updateMessages', data.validation, true); // renders validation messages at appropriate places
                // anchor.href = "javascript:void(0)";
                $('.ErrorPage').text(data.validation);
                // $('.ErrorPage').show();
                // $('.submitButton').show();
                // $('.ButtonPreloader').hide();
                $('.breadcrumbb').find('a').each((index, element)=>{
                    if(element.className == 'active'){ //Don't Disble Current Tab
                        return true;
                    }
                    Tabs.push(element);
                })
                DisableTabs(Tabs)

            }
            
            else if (data.error) {
                swal("Error", data.error, "dnger");
                $('.ErrorPage').text(data.error);
                // $('.ErrorPage').show();
                // $('.submitButton').show();
                // $('.ButtonPreloader').hide();
                // anchor.href = "javascript:void(0)";
                $('.breadcrumbb').find('a').each((index, element)=>{
                    if(element.className == 'active'){ //Don't Disble Current Tab
                        return true;
                    }
                    Tabs.push(element);
                })
                DisableTabs(Tabs)

            }

            else {
                // incorrect server response
            }
        })
        .fail(function () {
            // request failed
        })

        return false; // prevent default form submission
    })
        
JS;

$this->registerJs($script);
