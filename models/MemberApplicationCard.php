<?php

namespace app\models;

use Yii;
use yii\base\Model;
use borales\extensions\phoneInput\PhoneInputValidator;



class MemberApplicationCard extends Model
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
    public $PortalStus;


    public function rules()
    {
        // today
        $date = new \DateTime();
        // 18 years ago
        $date->sub(new \DateInterval('P18Y'));
        // maximum birthday
        $max = $date->format('Y-m-d');

        $date->sub(new \DateInterval('P70Y'));
        $min = $date->format('Y-m-d');

        return [

            [[
                'P_I_N_Number',  'Marital_Status','Last_Name',
                'Gender', 'ID_No',
                'Date_of_Birth',
            ], 'required'],

            ['Date_of_Birth', 'date', 'format' => 'php:Y-m-d', 'max' => $max, 'min' => $min, 'tooSmall' => 'You must be 70 years and below', 'tooBig' => 'You Must be 18 years old or older'],


        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'Protected_Member' => 'Accept Terms and Conditions',
            'Name'=>'First Name',
            'Last_Name' => 'Last Name',
            'VAT_No' => 'Group Registration No',
            'Business_Physical_Location' => 'Physical Location',
            'Address' => 'Postal Address',
            'Address_2' => 'Altetnative Phone Number',
            'Consituencies_Code' => 'Consituency',
            'Sub_Constitueny_Code' => 'Sub Constituency',
            'Gender' => 'Gender',
            // 'Last_Name' => 'SurName',
            'Monthly_Deposit_Cont' => 'Monthly Contribution',
            'STKpushNo' => 'Mpesa Phone No',
            'Proffesional_Body' => 'Professional Body',
            'Marketing_Texts' => 'Would You Like to Receive Markerting Messages?',
            'P_I_N_Number' => 'KRA PIN',
            'National_ID_No' => 'National ID No'

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


    public function getMemberApplicationKins()
    {

        $service = Yii::$app->params['ServiceName']['MemberApplication_KINs'];
        $filter = [
            'Account_No' => $this->No,
        ];

        $result = Yii::$app->navhelper->getData($service, $filter);
        if (is_array($result)) {
            return $result;
        } else {
            return false;
        }
    }
}
