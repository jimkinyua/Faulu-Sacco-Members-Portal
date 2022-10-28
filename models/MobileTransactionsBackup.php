<?php

namespace app\models;
use Yii;
use yii\base\Model;


class MobileTransactionsBackup extends Model{
    public $Key;
    public $Account_No;
    public $Account_Name;
    public $Document_No;
    public $Document_Date;
    public $Transaction_Time;
    public $Transaction_Type;
    public $Telephone_Number;
    public $Posted;
    public $Date_Posted;
    public $Account_2;
    public $Loan_No;
    public $Status;
    public $Comments;
    public $Amount;
    public $Charge;
    public $Description;
    public $Entry;
    public $Client;
    public $Posting_Date;
    public $Keyword;

    public function rules()
    {
        return [
            // [['Entry_Type', 'Description', 'Amount'], 'required'],
            // ['Amount', 'number'],
            // ['Amount', 'number', 'min' => 0],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'Entry_Type' => 'Type of Transaction',
        ];
    }

}

