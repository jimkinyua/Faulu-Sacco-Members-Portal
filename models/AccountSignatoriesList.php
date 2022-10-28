<?php

namespace app\models;
use Yii;
use yii\base\Model;
use borales\extensions\phoneInput\PhoneInputValidator;


class AccountSignatoriesList extends Model
{
    public $ID_No;
    public $Application_No;
    public $First_Name;
    public $Middle_Name;
    public $Last_Name;
    public $Gender;
    public $Date_of_Birth;
    public $Member_No_If_Member;
    public $Must_Be_Present;
    public $Must_Sign;
    public $Archived;
    public $isSignatoryMember;
    public $PhoneNo;
    public $Email;
    public $KRA_Pin;
    public $Key;


    public function rules()
    {
        return [

            [['Type', 'First_Name', 'ID_No' ,'Last_Name', 'Gender', 'Date_of_Birth',
            'KRA_Pin'=>'KRA_Pin', 'Email'=>'Email', 'PhoneNo', 'KRA_Pin', 'Email',
            'Must_Be_Present', 'isSignatoryMember', 'Must_Sign'], 
            'required'],
            ['Email', 'email'],

            ['Member_No_If_Member', 'required', 'when' => function ($model) {
                return $model->isSignatoryMember == 'Yes';
            }, 'whenClient' => "function (attribute, value) {
                return $('#accountsignatorieslist-issignatorymember').val() == 'Yes';
            }"],

            [['PhoneNo'], 'string'],
            [['PhoneNo'], PhoneInputValidator::className()],

        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'Member_No_If_Member'=>'Member No',
            'ID_No'=>'National Id ',
        ];
    }

}

?>

