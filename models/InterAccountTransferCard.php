<?php

namespace app\models;

use Yii;
use yii\base\Model;


class InterAccountTransferCard extends Model
{
    public $Key;
    public $Transaction_Type;
    public $Transaction_No;
    public $Amount;
    public $Posting_Description;
    public $Source_Type;
    public $Member_No;
    public $Member_Name;
    public $Source_No;
    public $Source_Name;
    public $Destination_Type;
    public $Destination_Member_No;
    public $Destination_Member_Name;
    public $Destination_No;
    public $Destination_Name;
    public $Initiated_By;
    public $Initiated_On;
    public $Received_By;
    public $Received_On;
    public $Action_ID;
    public $Current_Level;
    public $Approval_Loop;

    public function rules()
    {
        return [

            [['Destination_Member_No', 'Amount'], 'required'],
            ['Amount', 'number', 'min' => 1,]
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'Destination_Member_No' => 'Member To Transfer Share Capital To (Member No)'
        ];
    }
}
