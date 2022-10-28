<?php

namespace app\models;

use Yii;
use yii\base\Model;
use borales\extensions\phoneInput\PhoneInputValidator;


/**
 * ApplicantLogin is the model behind the Applicant login form.
 *
 * @property-read User|null $user This property is read-only.
 *
 */
class ApplicantLogin extends Model
{
    public $memebershipType;
    public $phoneNo;
    public $rememberMe = false;


    private $_user = false;


    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            // username and password are both required
            [['memebershipType', 'phoneNo'], 'required'],
            [['phoneNo'], 'string'],
            [['phoneNo'], PhoneInputValidator::className()],
        ];
    }

    public function attributeLabels()
    {
        return [
            'memebershipType'=>'Membership Type',
            // 'ID_No'=>'National Id ',
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
            return Yii::$app->applicant->login($this->getUser(), $this->rememberMe ? 3600*24*30 : 0);
        }
        return false;
    }

    /**
     * Finds user by [[username]]
     *
     * @return User|null
     */

    public function getUser(){
        if ($this->validate()) {
            if ($this->_user === false) {
                $this->_user = ApplicantUser::findByMemberTypeAndPhoneNo($this->memebershipType, $this->phoneNo);
            }
            if($this->_user){
                return$this->_user ;
            }
            return false;
        }

    }

    public function getApplicant(){
        if ($this->validate()) {
            if ($this->_user === false) {
                $this->_user = ApplicantUser::findApplicant($this->memebershipType, $this->phoneNo);
                // echo '<pre>';
                // print_r($this->memebershipType);
                // exit;
            }
            if($this->_user){
                return$this->_user ;
            }
            return false;
        }

    }
}
