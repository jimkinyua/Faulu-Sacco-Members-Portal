<?php

namespace app\models;

use Yii;
use yii\base\Model;


class AcceptRejectGuarantorshipForm extends Model
{
    public $Key;
    public $Loan_No;
    public $Guarantor_Type;
    public $Member_No;
    public $Account_No;
    public $Collateral_Reg_No;
    public $Loan_Type;
    public $Loanee_Name;
    public $Name;
    public $Available_Shares;
    public $Amount_Guaranteed;
    public $Deposits_Shares;
    public $Outstanding_Balance;
    public $New_Member_No;
    public $Substituted;
    public $Total_Guaranteed_Amount;

    public $Accepted;
    public $Status;
    public $Available_Deposits;
    public $Member_Name;
    public $Requested_Amount;
    public $Installments;
    public $Loan_Product_Type_Name;
    public $Repayment_Start_Date;
    public $Amount_Accepted;
    public $AcceptTerms;
    public $Repayment;
    public $Rejection_Reason;

    public function rules()
    {
        return [
            [['Member_No', 'Amount_Accepted', 'AcceptTerms'], 'required'],
            ['Amount_Accepted', 'required', 'when' => function ($model) {
                return $model->Accepted == true;
            }],
            ['Amount_Accepted', 'number', 'max' => $this->Available_Deposits],
            ['Amount_Accepted', 'number', 'min' => 1,],
            //'max'=>abs(Yii::$app->user->identity->getMemberStatistics()->Savings)

            ['Rejection_Reason', 'required', 'when' => function ($model) {
                return $model->Accepted == false;
            }],

            ['Rejection_Reason', 'string', 'min' => '10'],
            ['AcceptTerms', 'compare', 'compareValue' => 1, 'message' => 'You should accept term Our Terms and Conditions'],


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
            'AccountTy3pe' => 'Source of Funds',
            'Available_Deposits' => 'Free Shares'
        ];
    }
}
