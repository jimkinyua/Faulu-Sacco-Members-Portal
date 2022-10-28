<?php

namespace app\models;

use Yii;
use yii\base\Model;
use borales\extensions\phoneInput\PhoneInputValidator;


class MemberProfileCard extends Model
{
    public $Key;
    public $Application_No;
    public $Member_Category;
    public $Global_Dimension_1_Code;
    public $Global_Dimension_2_Code;
    public $Nationality;
    public $Sales_Person;
    public $Mobile_Transacting_No;
    public $ATM;
    public $Mobile;
    public $Portal;
    public $First_Name;
    public $Middle_Name;
    public $Last_Name;
    public $National_ID_No;
    public $Date_of_Birth;
    public $Marital_Status;
    public $Gender;
    public $Employer_Code;
    public $Station_Code;
    public $Designation;
    public $Payroll_No;
    public $Group_Name;
    public $Group_No;
    public $Certificate_of_Incoop;
    public $Date_of_Registration;
    public $Certificate_Expiry;
    public $_x0026_KRA_PIN;
    public $_x0026_E_Mail_Address;
    public $_x0026_Address;
    public $_x0026_County;
    public $_x0026_Sub_County;
    public $Town_of_Residence;
    public $Estate_of_Residence;
    public $Pin_Number;
    public $Phone_No;
    public $E_Mail;
    public $SMS_Notification_Number;

    public $Full_Name;
    public $Occupation;
    public $Mobile_Phone_No;
    public $Alt_Phone_No;
    public $E_Mail_Address;
    public $Type_of_Residence;

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
            'No' => 'Member No',
            'Consituencies_Code' => 'Constituency Code',
            'National_ID_No' => 'National ID No /Passport No / Group Registration No',
            'E_Mail' => 'E-Mail'
        ];
    }

    public function getMemberAccounts()
    {
        $service = Yii::$app->params['ServiceName']['MemberAccountsListpart'];
        $filter = [
            'No' => $this->No,
        ];

        $result = Yii::$app->navhelper->getData($service, $filter);
        return $result;
    }


    public function getNextOfKins()
    {
        $service = Yii::$app->params['ServiceName']['MembersKINs'];
        $filter = [
            'App_No' => $this->No,
        ];

        $result = Yii::$app->navhelper->getData($service, $filter);
        return $result;
    }
}
