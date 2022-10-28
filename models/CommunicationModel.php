<?php

namespace app\models;

use Yii;
use yii\base\Model;
use borales\extensions\phoneInput\PhoneInputValidator;



class CommunicationModel extends Model
{
    public $Key;
    public $No;
    public $Name;
    public $First_Name;
    public $Second_Name;
    public $Last_Name;
    public $Date_of_Birth;
    public $ID_No;
    public $Passport_No;
    public $Member_Category;
    public $Station_Department;
    public $Group_Account_No;
    public $Current_Address;
    public $Home_Address;
    public $Mobile_Phone_No;
    public $P_I_N_Number;
    public $E_Mail;
    public $Gender;
    public $Marital_Status;
    public $Box_No;
    public $Post_Code;
    public $City;
    public $Nationality;
    public $Bank_Code;
    public $Branch_Code;
    public $Bank_Account_No;
    public $Remarks;
    public $Recruited_by_Type;
    public $Recruited_By;
    public $Pay_Point;
    public $Employer_Code;
    public $Designation;
    public $Employer_Name;
    public $Payroll_No;
    public $Terms_of_Employment;
    public $Phone_No;
    public $Created_By;
    public $Status;
    public $Responsibility_Center;
    public $Global_Dimension_1_Code;
    public $Global_Dimension_2_Code;
    PUBLIC $PortalStus;
    public function rules()
    {
        return [

            [[
                'E_Mail', 'Current_Address',  'Mobile_Phone_No',
            ], 'required'],
            [['Mobile_Phone_No',], 'required', 'message' => 'Mobile Transacting No Cannot be empty'],
            [['Mobile_Phone_No'], PhoneInputValidator::className()],

        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            '_x0026_E_Mail_Address' => 'Email Address',
            '_x0026_Address' => 'Address',
            '_x0026_County' => 'County',
            'Alt_Phone_No' => 'Alternative Phone Number',
            'Mobile_Transacting_No' => 'Mobile Transacting No (Ensure it is a Safaricom Number)',
            'Marketing_Texts' => 'Would You like to Receive Marketing Texts ',
        ];
    }


    public function getSubscriptions()
    {
        $service = Yii::$app->params['ServiceName']['ApplicationSubscriptions'];
        $filter = [
            'Source_Code' => $this->Application_No,
        ];
        $ApplicationSubscriptions = Yii::$app->navhelper->getData($service, $filter);
        if (is_object($ApplicationSubscriptions)) {
            return [];
        }
        return $ApplicationSubscriptions;
    }


    public function getAttachments()
    {

        $service = Yii::$app->params['ServiceName']['DocumentUploads'];
        $filter = [
            'Parent_No' => $this->Application_No,
        ];

        $result = Yii::$app->navhelper->getData($service, $filter);
        if (is_array($result)) {
            return $result;
        } else {
            return false;
        }
    }
}
