<?php

namespace app\models;

use yii\base\InvalidArgumentException;
use yii\base\Model;
use Yii;
use app\models\User;

/**
 * Password reset form
 */
class ResetPasswordForm extends Model
{
    public $password;
    public $confirmPassword;
    public $applicantId;


    /**
     * @var \common\models\User
     */
    private $_user;


    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['password', 'confirmPassword'], 'required'],
            // ['password', 'string', 'min' => Yii::$app->params['user.passwordMinLength']],
            ['confirmPassword', 'compare', 'compareAttribute' => 'password', 'message' => 'Passwords do not match, try again'],

        ];
    }


    public function resetPassword($memberNo)
    {
        $_user = User::findById($memberNo);
        $_user->setPassword($this->password);
        $_user->removePasswordResetToken();
        $_user->generateAuthKey();
        if ($_user->save(false)) {
            return $_user;
        }
        return false;
    }
}
