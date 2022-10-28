<?php

namespace app\models;
use Yii;
use yii\base\Model;
use borales\extensions\phoneInput\PhoneInputValidator;


class ApplicationSubscriptions extends Model
{
    public $Key;
    public $Product_Type;
    public $Product_Name;
    public $No;
    public $Monthly_Contribution;
    public $Loan_Disbursement_Ac;

    public $isNewRecord;

   //Other Accounts
   public $ATM;
   public $Mobile;
   public $FOSA;

    public function rules()
    {
        return [

            [['Monthly_Contribution',], 'required'],
          

        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'Account_Type' => 'Product ',
        ];
    }

}
