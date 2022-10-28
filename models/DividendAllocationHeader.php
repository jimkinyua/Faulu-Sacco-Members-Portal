<?php

namespace app\models;
use Yii;
use yii\base\Model;
use yii\helpers\FileHelper;
use borales\extensions\phoneInput\PhoneInputValidator;



class DividendAllocationHeader extends Model{

    public $Key;
    public $Dividend_Code;
    public $Member_No;
    public $Member_Name;
    public $Net_Amount;
    public $Allocation_Type;
    public $Allocation_Account;
    public $MPESA_No;
    public $Bank_Code;
    public $Bank_Name;
    public $Branch_Code;
    public $Branch_Name;
    public $Account_No;
    public $Account_Name;
    public $Submitted;
    public $Created_On;
    public $Created_By;

    public function rules()
    {
        return [

            [['Allocation_Type'], 'required'],

            [['Bank_Code','Branch_Code','Account_No'], 'required', 'when' => function ($model) {
                return $model->Allocation_Type == 'Bank_Payment';
            }, 'whenClient' => "function (attribute, value) {
                return $('#dividendallocationheader-allocation_type').val() == 'Bank_Payment';
            }"],


            [['MPESA_No',], 'required', 'when' => function ($model) {
                return $model->Allocation_Type == 'MPESA_Payment';
            }, 'whenClient' => "function (attribute, value) {
                return $('#dividendallocationheader-allocation_type').val() == 'MPESA_Payment';
            }"],

            
            [['Allocation_Account',], 'required', 'when' => function ($model) {
                return $model->Allocation_Type == 'Loan_Payment';
            }, 'whenClient' => "function (attribute, value) {
                return $('#dividendallocationheader-allocation_type').val() == 'Loan_Payment';
            }"],

            // [['MPESA_No',], PhoneInputValidator::className()],


        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'Net_Amount' => 'Dividend Amount',
            'Allocation_Type'=>'How Would You Like Your Dividends to Be Processed',
            'Bank_Code'=>'Bank Name',
            'Branch_Code'=>'Branch Name',
            'MPESA_No'=>'MPESA Phone Number',
            'Allocation_Account'=>'Loan To Be Payed'

        ];
    }



}

?>

