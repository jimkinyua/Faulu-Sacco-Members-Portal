<?php

namespace app\models;
use Yii;
use yii\base\Model;


class MemberStatistics extends Model
{
    public $Key;
    public $No;
    public $Name;
    public $Phone_No;
    public $Staff_No;
    public $Employer_Name;
    public $Share_Capital;
    public $Deposits;
    public $Savings;
    public $Loans;
    public $UBF;
    public $Unallocated;
    public $Running_Fixed_Deposits;
    public $Running_Standing_Orders;
    public $Running_Loans;
    public $Cheques_On_Hand;
    public $Loans_Lookup;
    public $Holiday_Scheme;
    public $Child_Scheme;
    public $Outstanging_Arrears;

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

