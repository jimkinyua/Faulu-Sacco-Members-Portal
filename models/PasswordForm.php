<?php

namespace app\models;

use Yii;
use yii\base\Model;

/**
 * LoginForm is the model behind the login form.
 *
 * @property-read User|null $user This property is read-only.
 *
 */
class PasswordForm extends Model
{
    public $password;


    private $_user = false;


    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            // username and password are both required
            [['password'], 'required'],

        ];
    }

    public function attributeLabels()
    {
        return [
            // 'IDnumber'=>'This Field',
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
                $this->addError($attribute, 'Incorrect username or password.');
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
            return Yii::$app->user->login($this->getUser(), $this->rememberMe ? 3600*24*30 : 0);
        }
        return false;
    }

    public function checkPassword($memberNo){
        $user = User::findOne([
            'Member No_' => $memberNo,
        ]);

        

        if (!$user) {
            return false;
        }


        if ($user->validatePassword($this->password)) {
            // all good, send OTP
            return true;
        } else {
            // wrong password
            return $this->addError('password', 'Incorrect Password.');
            return false;
        }

    }

    /**
     * Finds user by [[username]]
     *
     * @return User|null
     */

    public function getUser(){
        if ($this->validate()) {
            if ($this->_user === false) {
                $this->_user = User::findByMemberNoAndIdNumber(strtoupper($this->memberNo), $this->IDnumber);
            }
            if($this->_user){
                return$this->_user ;
            }
            return false;
        }

    }
}
