<?php

namespace app\models;

use Yii;
use yii\base\Model;


class LoanCalculatorHeader extends Model
{
    public $Key;
    public $Document_No;
    public $Member_No;
    public $Loan_Product;
    public $Product_Description;
    public $Principal_Amount;
    public $Interest_Rate;
    public $Installments_Months;
    public $Repayment_Start_Date;
    public $Current_Deposits;
    public $Current_DepositsX4;
    public $Ouststanding_Loans;
    public $Deposit_Appraisal;
    public $Basic_Pay;
    public $Other_Allowances;
    public $Overtime_Allowances;
    public $Sacco_Dividend;
    public $Total_Deductions;
    public $Cleared_Effects;
    public $Adjusted_Net_Income;
    public $OneThird_Basic;
    public $Amount_Available;
    public $Created_By;
    public $Created_On;

    public function rules()
    {
        return [
            [[
                'Basic_Pay', 'Other_Allowances', 'Overtime_Allowances',
                'Total_Deductions', 'Cleared_Effects'
            ], 'required'],

            [['Basic_Pay', 'Other_Allowances', 'Overtime_Allowances', 'Sacco_Dividend', 'Total_Deductions'], 'number', 'min' => 1],


        ];
    }

    public function attributeLabels()
    {
        return [
            'Principle_Amount' => 'Principal Amount',
            'Monthly_Installment' => 'Monthly Instalment',
            'Max_Installments' => 'Max Instalments',
            'Repayment_Installments' => 'Repayment Instalments'
        ];
    }
}
