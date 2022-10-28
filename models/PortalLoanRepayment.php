<?php

namespace app\models;

use Yii;
use yii\base\Model;
use borales\extensions\phoneInput\PhoneInputValidator;

/**
 * ContactForm is the model behind the contact form.
 */
class PortalLoanRepayment extends Model
{
    public $LoanNo;
    public $Loan_Product;
    public $MemberNo;
    public $Date;
    public $ReferenceNo;
    public $Amount;
    public $Source;
    public $AccountNo;
    public $PhoneNo;
    public $LoanAmount;
    public $KeyWord;
    public $RefrenceNo;



    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['LoanNo', 'MemberNo', 'ReferenceNo', 'Amount', 'Source'], 'required'],
            ['Amount', 'number', 'min' => 1],

            ['PhoneNo', 'required', 'when' => function ($model) {
                return $model->Source == 'MPESA';
            }, 'whenClient' => "function (attribute, value) {
                return $('#portalloanrepayment-source').val() == 'MPESA';
            }"],

            ['AccountNo', 'required', 'when' => function ($model) {
                return $model->Source == 'FOSA';
            }, 'whenClient' => "function (attribute, value) {
                return $('#portalloanrepayment-source').val() == 'FOSA';
            }"],

            ['Amount', 'number', 'max'=>150000, 'when' => function ($model) {
                return $model->Amount == 150000;
            }, 'whenClient' => "function (attribute, value) {
                return $('#portalloanrepayment-amount').val() > 150000;
            }"],


            [['phoneNo'], PhoneInputValidator::className()],

        ];
    }

    /**
     * @return array customized attribute labels
     */
    public function attributeLabels()
    {
        return [
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
            ->setFrom(['jimkinyua25@gmail.com' =>'Memeber Testing'])
            ->setTo($this->email)
            ->setSubject('Account registration at ' . Yii::$app->name)
            ->send();
    }

    public function signup()
    {
        if (!$this->validate()) {
            return null;
        }
        
        $user = new User();

        // $user->phoneNo = $this->phoneNo;
        // $user->idNo = $this->idNo;
        // $user->lastName = $this->lastName;
        $user->memebershipType = $this->memebershipType;
        // $user->firstName = $this->firstName;
        // $user->kraPinNo = $this->kraPinNo;
        $user->email = $this->email;
        $user->setPassword($this->password);
        $user->generateAuthKey();
        $user->generateEmailVerificationToken();
        return $user->save() && $this->sendEmail($user);

    }

}