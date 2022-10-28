<?php
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $user common\models\User */

$verifyLink = Yii::$app->urlManager->createAbsoluteUrl(['site/verify-email', 'token' => $user->{'Transaction OTP'}]);
?>
<div class="verify-email">

        Dear <?= $user->Name ?>,<br/>

        <p> Please Enter below verification code when prompted,  the code expires in Five (5) minutes. </p>

        <p> <b> <?= $user->{'Transaction OTP'} ?> </b> <p>

        <p>Thanks, 

        Mhasibu ICT Team</p>

</div>
