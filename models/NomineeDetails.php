<?php

namespace app\models;
use Yii;
use yii\base\Model;
use borales\extensions\phoneInput\PhoneInputValidator;


class NomineeDetails extends Model{
    public $FullName;
    public $Key;
    public $National_ID_No;
    public $Email;
    public $Relationship;
    public $Percent_Allocation;
    public $Application_No;
    public $Member_Category;
    public $Phone_No;
    public $Type;

    public function rules()
    {
        return [
            [['FullName', 'National_ID_No', 'Email' ,'Relationship','Type', 'Percent_Allocation', 'Phone_No', 'Application_No' ], 'required'],
            ['Percent_Allocation', 'number', 'max' => 100, 'min' => 1],
            ['Email', 'email'],
            ['National_ID_No', 'string','min' => 8 , 'max' => 8],
            [['Phone_No'], PhoneInputValidator::className()],

        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'National_ID_No'=> 'Birth Certificate No or National ID No',
            'Type'=> 'Idenitification Type'
        ];
    }

}

?>

