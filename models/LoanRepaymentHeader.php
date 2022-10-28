<?php

namespace app\models;
use Yii;
use yii\base\Model;


class LoanRepaymentHeader extends Model
{
    public $Key;
    public $Document_No;
    public $Member_No;
    public $Member_Name;
    public $Loan_No;
    public $Loan_Description;
    public $Loan_Balance;
    public $Guarantore_Recoveries;
    public $Payment_Method;
    public $Source_Account;
    public $Teller_No;
    public $Created_By;
    public $Created_On;
    public $Book_Balance;
    public $Minimum_Balance;
    public $Available_Balance;
    public $Payment_Amount;
    public $Transacted_By_Name;
    public $Transacted_By_ID_No;
    public $Posting_Date;
    public $Posted_By;
    public $Charges;
    public $Charge_Amount;
    public $Outstanding_Interest;
    public $Total_Allocated_Amount;
    public $Pro_Rated_Intrest;
    
    public function rules()
    {
        return [
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

?>

