<?php

namespace app\models;

use Exception;
use Yii;
use yii\base\InvalidArgumentException;
use yii\base\Model;

/**
 * LoginForm is the model behind the login form.
 *
 * @property-read User|null $user This property is read-only.
 *
 */
class LoginForm extends Model
{
    public $memberNo;
    public $password;
    public $IDnumber;
    public $token_created_at;
    public $token_expires_at;
    public $rememberMe = false;
    public $verification_token;

    private $_user = false;


    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            // username and password are both required
            [['IDnumber'], 'required'],
            // rememberMe must be a boolean value
            // ['rememberMe', 'boolean'],
            // password is validated by validatePassword()
            ['password', 'validatePassword'],
            [['password', 'IDnumber'], 'trim'],

        ];
    }

    public function attributeLabels()
    {
        return [
            'IDnumber' => 'ID Number',
        ];
    }

    /**
     * Validates the password.
     * This method serves as the inline validation for password.
     *
     * @param string $attribute the attribute currently being validated
     * @param array $params the additional name-value pairs given in the rule
     */
    public function validatePassword($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $user = $this->getUser();

            if (!$user || !$user->validatePassword($this->password)) {
                $this->addError($attribute, 'Incorrect Password.');
            }
        }
    }

    /**
     * Logs in a user using the provided username and password.
     * @return bool whether the user is logged in successfully
     */
    public function login()
    {
        if ($this->validate()) {
            return Yii::$app->user->login($this->getUser(), $this->rememberMe ? 3600 * 24 * 30 : 0);
        }
        return false;
    }

    public function isFirstLogin()
    {
        try {
            // if ($this->validate()) {
            $user = $this->getUser();
            if ($user) {
                if (empty($user->password_hash)) {
                    $this->setFirstApplicantDetailsToSession($user);
                    return true;
                }
            }
            return false;
            // }
        } catch (InvalidArgumentException $e) { // Means, This is  first time login user. Return true instead of throwing the error.
            $user = $this->getUser();
            $this->setFirstApplicantDetailsToSession($user);
            return true;
        }
        return false;
    }

    private function setFirstApplicantDetailsToSession($userDetails)
    {
        Yii::$app->session->set('userDetails', $userDetails);
        Yii::$app->session->set('firstTimeLogin', true);
    }

    /**
     * Finds user by [[username]]
     *
     * @return User|null
     */

    public function getUser()
    {
        if ($this->_user === false) {
            $this->_user = User::findByUsername($this->IDnumber);
            if ($this->_user) {
                return $this->_user;
            } else {
                $this->addError('IDnumber', 'Incorrect Id Number');
                return false;
            }
        }
        return $this->_user;
    }
}
