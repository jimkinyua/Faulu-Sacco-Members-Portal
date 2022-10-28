<?php

namespace app\models;

use Yii;
use yii\base\Model;
use borales\extensions\phoneInput\PhoneInputValidator;


/**
 * ContactForm is the model behind the contact form.
 */
class ForgotMemberNoForm extends Model
{
    public $memebershipType;
    public $phoneNo;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['phoneNo','memebershipType'], 'required'],
            // [['phoneNo'], 'string', 'max' => 10, 'min' => 10],
            [['phoneNo'],'trim'],
        ];
    }

  

    /**
     * @return array customized attribute labels
     */
    public function attributeLabels()
    {
        return [
            // 'verifyCode' => 'Verification Code',
        ];
    }


    public function IfUserExists(){        
        $user = new User();
        $_user = $user::findByMemberTypeAndPhoneNo($this->memebershipType, $this->phoneNo);
   
        if($user){
            return $_user;
        }
        return false;
    }

}
