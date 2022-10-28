<?php

namespace app\models;
use Yii;
use yii\base\Model;
use borales\extensions\phoneInput\PhoneInputValidator;



class MpesaAccountDeposit extends Model
{
    public $AccountNo;
    public $STKpushNo;
    public $Amount;
 

    public function rules()
    {
        return [

            [['STKpushNo', 'Amount'], 'required'],
            ['Amount', 'number'],
            [['STKpushNo'], PhoneInputValidator::className()],

        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'STKpushNo'=>'Phone Number'
        ];
    }

}

?>

