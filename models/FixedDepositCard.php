<?php

namespace app\models;

use Yii;
use yii\base\Model;


class FixedDepositCard extends Model
{
    public $Key;
    public $FD_No;
    public $Posting_Date;
    public $Member_No;
    public $Member_Name;
    public $Marturity_Instructions;
    public $Rate;
    public $FD_Type;
    public $FD_Description;
    public $Funds_Source;
    public $Source_Account;
    public $Source_Balance;
    public $Amount;
    public $Start_Date;
    public $Period;
    public $End_Date;
    public $Total_Interest_Payable;
    public $Total_Interest_Accrued;
    public $Total_Interest_Balance;

    public $Status;
    public function rules()
    {
        return [
            [['FD_Type', 'Amount', 'Interest_Rate', 'Period', 'Start_Date', 'End_Date', 'Marturity_Instructions', 'Source_Account'], 'required'],
            [['Amount',], 'number', 'min' => 10000,],
            // [['Period'], 'number', 'min' => 3],
            ['Account_No', 'required', 'when' => function ($model) {
                return $model->Source_Type == 'Bank';
            }, 'whenClient' => "function (attribute, value) {
                return $('#fixeddepositcard-source_type').val() == 'Bank';
            }"],

            ['Source_Of_Funds', 'required', 'when' => function ($model) {
                return $model->Source_Type == 'Bank';
            }, 'whenClient' => "function (attribute, value) {
                return $('#fixeddepositcard-source_type').val() == 'Bank';
            }"],


        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'Period' => 'Fixed Period in Months',
            // 'FD_Type'=>'Fixed Deposit Type',
            'Source_Balance' => 'Account Balance'
        ];
    }
}
