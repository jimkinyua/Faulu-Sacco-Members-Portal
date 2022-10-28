<?php

namespace app\models;

use Yii;
use yii\base\Model;


class MemberStatisticsFactbox extends Model
{
    public $Key;
    public $Member_No;
    public $Full_Name;
    public $National_ID_No;
    public $Mobile_Phone_No;
    public $Total_Deposits;
    public $Total_Shares;
    public $Held_Collateral;
    public $Running_Loans;
    public $Outstanding_Loans;
    public $Uncleared_Effect;
    public $Self_Guarantee;
    public $Non_Self_Guarantee;
    public $Qualified_Self_Guarantee;
    public $Qualified_Non_Self_Guarantee;
    public $Collections;
    public $Email;
    public $SASAAccount;
    public $InvestmentAccount;

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
            // 'Protected_Member' => 'Accept Terms and Conditions',
        ];
    }
}
