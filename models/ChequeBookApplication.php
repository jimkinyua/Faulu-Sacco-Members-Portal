<?php

namespace app\models;
use Yii;
use yii\base\Model;


class ChequeBookApplication extends Model{
    public $Key;
    public $Application_No;
    public $Application_Date;
    public $Cheque_Book_Type;
    public $Member_No;
    public $Member_Name;
    public $Account_No;
    public $Account_Name;
    public $No_of_Leafs;
    public $Charge_Code;
    public $Charge_Amount;

   
    public function rules()
    {
        return [  
            [['Cheque_Book_Type', 'No_of_Leafs',], 'required'],            
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(){
        return [
            // 'Fixed_Period_M' => 'Fixed Period in Months',
            // 'FD_Type'=>'Fixed Deposit Type',
            // 'Source_Of_Funds'=> 'Source of Funds'
        ];
    }

 
}

?>

