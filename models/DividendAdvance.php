<?php

namespace app\models;
use Yii;
use yii\base\Model;
use yii\helpers\FileHelper;


class DividendAdvance extends Model{

    public $Key;
    public $Period_Code;
    public $Member_No;
    public $Member_Name;
    public $Total_Savings;
    public $Qualified_Amount;
    public $Rounded_Amount;
    public $Principle_Amount;
    public $Maximum_Amount;
    public $Loan_Product;
    public $Total_Interest_Earned;
    public $Current_Status;
    public $Loan_Number;
    public $Created_By;
    public $Created_On;
    public $Last_Updated_By;
    public $Last_Updated_On;
    public $Arrears_Amount;
    public $Rejection_Reason;
    public $AppliedAmount;

    public function rules()
    {
        return [

            [['AppliedAmount'], 'required'],
            ['AppliedAmount', 'number'],
            ['AppliedAmount', 'number', 'min'=>'0'],

        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'AppliedAmount' => 'How Much Are You Applying For?',
            // 'docFile'=>'Security Attachment',
        ];
    }



}

?>

