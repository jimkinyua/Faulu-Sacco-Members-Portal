<?php

namespace app\models;
use Yii;
use yii\base\Model;


class MpesaDeposit extends Model
{
    public $Application_No;
    public $Mobile_Phone_No;
    public $Amount;
 

    public function rules()
    {
        return [

            [['Application_No', 'Mobile_Phone_No', 'Amount'], 'required'],
            ['Amount', 'number']
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            
        ];
    }

}

?>

