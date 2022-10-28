<?php

namespace app\models;
use Yii;
use yii\base\Model;


class AccountActivationRequest extends Model
{
    public $Key;
    public $Document_Noh;
    public $Member_No;
    public $Member_Name;
    public $Account_No;
    public $Balance_at_Reactivation;
    public $Last_Transaction_Date;
    public $Posting_Date;
    public $Charge_Code;
    public $Account_Name;
    public $Created_On;
    public $Created_By;
    public $Posted;
    public $Approval_Status;
    public $Action_ID;

    public function rules()
    {
        return [

            [['Member_No'], 'required'],
            // ['Quoted_Amount', 'number']
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            // 'Speed_Process'=>'Would You Like Your Exit Application To Be Processed Much Faster? (Attracts a Fee)'
        ];
    }

}