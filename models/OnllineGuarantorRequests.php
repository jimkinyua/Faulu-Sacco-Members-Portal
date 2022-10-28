<?php

namespace app\models;

use Yii;
use yii\base\Model;


class OnllineGuarantorRequests extends Model
{
    public $Key;
    public $Loan_No;
    public $Guarantor_Type;
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
    public $Status;
    public $Member_No;
    public $loanFormKey;

    public function rules()
    {
        return [

            [['Member_No', 'Amount_Guaranteed'], 'required'],
            ['Amount_Guaranteed', 'number', 'min' => 1],
            // array('Member_No', 'IfMemberExists'),

        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'ID_No' => 'Member NUmber',
            'Total_Guaranteed_Amount' => 'Amount To Guarantee'
        ];
    }

    public function getApplicantData()
    {
    }

    public function IfMemberExists($attribute, $params)
    {
        $user = new User();
        $service = Yii::$app->params['ServiceName']['MemberApplicationCard'];
        $Member = \Yii::$app->navhelper->findOneRecord($service, 'No', $this->Member_No);
        if (empty($Member)) {
            $this->addError('Member_No', 'The Member Does Not Exist');
        }
    }
}
