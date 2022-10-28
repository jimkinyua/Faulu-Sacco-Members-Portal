<?php

namespace app\models;

use Yii;
use yii\base\Model;


class StandingOrderCard extends Model
{
    public $Key;
    public $Document_No;
    public $STO_Type;
    public $Standing_Order_Class;
    public $Salary_Based;
    public $Member_No;
    public $Member_Name;
    public $Amount_Type;
    public $Amount;
    public $Account_No;
    public $Start_Date;
    public $Period;
    public $End_Date;
    public $Destination_Member_No;
    public $Destination_Account;
    public $Destination_Name;
    public $EFT_Account_Name;
    public $EFT_Bank_Name;
    public $EFT_Brannch_Code;
    public $EFT_Swift_Code;
    public $EFT_Transfer_Account_No;
    public $Posting_Description;
    public $Run_From_Day;
    public $Approval_Status;
    public $Running;

    public function rules()
    {
        return [
            [[
                'STO_Type', 'Standing_Order_Class', 'Amount_Type',
                'Start_Date', 'Period',
            ], 'required'],


            [['Destination_Member_No', 'Destination_Account'], 'required', 'when' => function ($model) {
                return $model->Standing_Order_Class == 'Internal';
            }, 'whenClient' => "function (attribute, value) {
                return $('#standingordercard-standing_order_class').val() == 'Internal';
            }"],

            [['Amount'], 'required', 'when' => function ($model) {
                return $model->Amount_Type != 'Sweep';
            }, 'whenClient' => "function (attribute, value) {
                return $('#standingordercard-amount_type').val() != 'Sweep';
            }"],

            [['Amount'], 'number',  'min' => 1, 'when' => function ($model) {
                return $model->Amount_Type != 'Sweep';
            }, 'whenClient' => "function (attribute, value) {
                return $('#standingordercard-amount_type').val() != 'Sweep';
            }"],


            [['EFT_Bank_Name', 'EFT_Account_Name', 'EFT_Brannch_Code', 'EFT_Transfer_Account_No'], 'required', 'when' => function ($model) {
                return $model->Standing_Order_Class == 'External';
            }, 'whenClient' => "function (attribute, value) {
                return $('#standingordercard-standing_order_class').val() == 'External';
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
            'Account_No' => 'Source Account',
            'Source_Of_Funds' => 'Source of Funds',
            'STO_Type' => 'Type Standing Order'
        ];
    }
}
