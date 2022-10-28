<?php

namespace app\models;

use Yii;
use yii\base\Model;

/**
 * ContactForm is the model behind the contact form.
 */
class MemberTypeAndPhoneNo extends Model
{
    public $phoneNo;
    public $memebershipType;
    public $agreeToTerms;
    public $_user;
    public $IDnumber;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['memebershipType', 'phoneNo'], 'required'],
            // verifyCode needs to be entered correctly
            // ['verifyCode', 'captcha'],
            // Passwords should match
            // ['confirmPassword','compare','compareAttribute'=>'password','message'=>'Passwords do not match, try again'],

        ];
    }

    /**
     * @return array customized attribute labels
     */
    public function attributeLabels()
    {
        return [
            'verifyCode' => 'Verification Code',
        ];
    }

    /**
     * Sends confirmation email to user
     * @param User $user user model to with email should be send
     * @return bool whether the email was sent
     */
    protected function sendEmail($user)
    {
        return Yii::$app
            ->mailer
            ->compose(
                ['html' => 'emailVerify-html', 'text' => 'emailVerify-text'],
                ['user' => $user]
            )
            ->setFrom(['jimkinyua25@gmail.com' => 'Memeber Testing'])
            ->setTo($this->email)
            ->setSubject('Account registration at ' . Yii::$app->name)
            ->send();
    }

    public function IfUserExists()
    {
        $user = new User();
        $_user = $user::findByUsername($this->IDnumber);
        if ($user) {
            return $_user;
        } else {
            return false;
        }
    }
}
