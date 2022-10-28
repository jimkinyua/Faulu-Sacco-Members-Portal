<?php

namespace app\models;

use Yii;
use yii\base\Model;


class LoanInternalDeductions extends Model
{
    public $Key;
    public $Loan_Top_Up;
    public $Client_Code;
    public $Loan_Type;
    public $Principle_Top_Up;
    public $Interest_Top_Up;
    public $Total_Top_Up;
    public $Monthly_Repayment;
    public $Interest_Paid;
    public $Outstanding_Balance;
    public $Interest_Rate;
    public $ID_NO;
    public $Commision;
    public $One_Month_Interest;
    public $Insurance_rebate;
    public $Loan_Account;
    public $Additional_Top_Up_Commission;

    public function rules()
    {
        return [
            [['Loan_Top_Up'], 'required'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'Loan_Top_Up' => 'Loan To Bridge',
        ];
    }
}
