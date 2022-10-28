<?php

namespace app\models;
use Yii;
use yii\base\Model;


class AccountClosureCard extends Model
{
    public $Key;
    public $Document_No;
    public $Member_No;
    public $Closure_Type;
    public $Member_Name;
    public $Account_No;
    public $Account_Name;
    public $Book_Balance;
    public $Available_Balance;
    public $Available_Amount;
    public $Closing_Reason;
    public $Balance_Option;
    public $Destination_Account_No;
    public $Destination_Account_Name;
    public $Speed_Process;
    public $Charges;
    public $Charge_Amount;
    public $Total_Assets;
    public $Total_Liabilities;
    public $Total_Guarantee;
    public $Net_Payout;
    public $Created_By;
    public $Created_On;
    public $Approval_Status;

    public function rules()
    {
        return [

            [['Account_No', 'Speed_Process', 'Closing_Reason'], 'required'],
            // ['Quoted_Amount', 'number']
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'Speed_Process'=>'Would You Like Your Account Closure Application To Be Processed Much Faster? (Attracts a Fee)'
        ];
    }

}