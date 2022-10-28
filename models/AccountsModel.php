<?php

namespace app\models;

use Yii;
use yii\base\Model;
use borales\extensions\phoneInput\PhoneInputValidator;



class AccountsModel extends Model
{
    public $Key;
    public $Application_No;
    public $Protected_Account;
    public $Member_Category;
    public $Global_Dimension_1_Code;
    public $Global_Dimension_2_Code;
    public $Recruited_By;
    public $Sales_Person;
    public $Nationality;
    public $Mobile_Transacting_No;
    public $ATM;
    public $Mobile;
    public $Portal;
    public $FOSA;
    public $Marketing_Texts;
    public $Subscription_Start_Date;
    public $Portal_Status;
    public $Rejection_Comments;
    public $First_Name;
    public $Middle_Name;
    public $Las_Name;
    public $Full_Name;
    public $National_ID_No;
    public $KRA_PIN;
    public $Date_of_Birth;
    public $Occupation;
    public $Type_of_Residence;
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
    public $_x0026_E_Mail_Address;
    public $_x0026_Address;
    public $_x0026_County;
    public $_x0026_Sub_County;
    public $Mobile_Phone_No;
    public $Alt_Phone_No;
    public $Town_of_Residence;
    public $Estate_of_Residence;
    public $Created_By;
    public $Created_On;
    public $KRA_PIN_No;
    public $Approval_Status;
    public $ApplicationForm;
    public $STKpushNo;
    public $AcceptTerms;



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
            'ATM' => 'Activate ATM Account',
            'Mobile' => 'Activate Mobile Account',
            'FOSA' => 'Activate FOSA Account',
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
