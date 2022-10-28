<?php

namespace app\models;
use Yii;
use yii\base\Model;


class MicrofinanceCashflow extends Model
{
    public $Key;
   public $Entry_Type;
   public $Description;
   public $Amount;
   public $Application_No;
   public $Income_Expense;

    public function rules()
    {
        return [
            [['Entry_Type', 'Description', 'Amount'], 'required'],
            ['Amount', 'number'],
            ['Amount', 'number', 'min' => 0],
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

