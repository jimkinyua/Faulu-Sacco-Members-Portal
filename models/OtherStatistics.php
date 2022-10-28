<?php

namespace app\models;

use Yii;
use yii\base\Model;


class OtherStatistics extends Model
{
    public $Key;
    public $No;
    public $Old_Member_No;
    public $Salutation;
    public $Name;
    public $Comment;
    public $Registration_Date;
    public $ID_No;
    public $Current_Address;
    public $Passport_No;
    public $PIN_No;
    public $Date_of_Birth;
    public $Birth_Certificate_No;
    public $Gender;
    public $txtMarital;
    public $Member_Segment;
    public $Membership_Type;
    public $Responsibility_Center;
    public $Employer_Code;
    public $Payroll_Staff_No;
    public $Status;
    public $Blocked;
    public $Member_Category;
    public $Status_Change_Statistics;
    public $Change_Log;
    public $Last_Date_Modified;
    public $Created_By;
    public $Nationality;
    public $County;
    public $Box_No;
    public $Post_Code;
    public $City;
    public $State;
    public $Zip_Code;
    public $State_ID;
    public $Designation;
    public $Phone_No;
    public $Mobile_Phone_No;
    public $MPESA_Mobile_No;
    public $E_Mail;
    public $Group_Type;
    public $Group_Account_No;
    public $Group_Account;
    public $Company_Registration_No;
    public $Date_of_Business_Reg;
    public $Business_Group_Location;
    public $Plot_Bldg_Street_Road;
    public $Single_Party_Multiple;
    public $Bank_Code;
    public $Branch_Code;
    public $Bank_Account_No;

    public function rules()
    {
        return [];
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
