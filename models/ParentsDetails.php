<?php

namespace app\models;
use Yii;
use yii\base\Model;
use borales\extensions\phoneInput\PhoneInputValidator;


class ParentsDetails extends Model
{
    public $Key;
    public $First_Name;
    public $Other_Names;
    public $Surname;
    public $National_ID_No;
    public $Membership_No;
    public $Mobile_Phone;
    public $Application_No;
    public $Member_Category;

    public function rules()
    {
        return [
            [['First_Name', 'Surname', 'Other_Names' ,'National_ID_No', 'Mobile_Phone', 'Application_No', 'Membership_No'], 'required'],
            // ['Applied_Amount', 'number']
            [['Mobile_Phone'], PhoneInputValidator::className()],
            ['National_ID_No', 'string', 'max'=>'8'],


        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            // 'Protected_Member' => 'Accept Terms and Conditions',
        ];
    }

}

