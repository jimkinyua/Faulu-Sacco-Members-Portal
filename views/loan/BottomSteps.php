<?php

use yii\bootstrap4\ActiveForm;
use yii\bootstrap4\Html;
$webroot = Yii::getAlias(@$webroot);
$absoluteUrl = \yii\helpers\Url::home(true);
?>

<?php ActiveForm::end(); ?>