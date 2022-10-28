<?php

namespace app\models;

use Yii;
use yii\base\Model;


class AcceptRejectGuarantorshipFormSub extends Model
{
    public $Key;
    public $Type;
    public $Replace_With;
    public $Guarantor_Names;
    public $Guarantor_Value;
    public $Max_Guarantee;
    public $Document_No;
    public $Status;
    public $AppliedAmount;
    public $Loan_No;
    public $Applicant;
    public $GuaranteedAmount;
    public $Rejection_Reason;
    public $Accepted;
    public $PhoneNo;
    public $Member_No;
    public $Account_Type;
    public $Guarantor_No;
    public $Replace_With_Name;
    public $Loan_Balance;
    public $Amount;
    public $Accepted_Amount;
    public $Requested_On;
    public $Responded_On;
    public $Outstanding_Guarantee;

    public $Code;
    public $Description;
    public $Value;
    public $Amount_Guaranteed;
    public $Security_Code;
    public $DocumentSubmitted;

    public function rules()
    {
        return [
            [['Member_No', 'Account_Type'], 'required'],
            ['GuaranteedAmount', 'required', 'when' => function ($model) {
                return $model->Accepted == true;
            }],
            ['GuaranteedAmount', 'number'],
            ['GuaranteedAmount', 'number', 'min' => 1,],
            //'max'=>abs(Yii::$app->user->identity->getMemberStatistics()->Savings)

            ['Rejection_Reason', 'required', 'when' => function ($model) {
                return $model->Accepted == false;
            }],

            ['Rejection_Reason', 'string', 'min' => '10'],

        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'Member_No' => 'Member Number',
            'Loan_Principal' => 'Amount To Guarantee',
            'Account_Type' => 'Source of Funds'

        ];
    }
}
