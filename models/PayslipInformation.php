<?php

namespace app\models;
use Yii;
use yii\base\Model;


class PayslipInformation extends Model
{
    public $Key;
    public $Appraisal_Code;
    public $Parameter_Description;
    public $Parameter_Value;
    public $Type;
    public $Class;
    public $Amount;

    public function rules()
    {
        return [
            [['Parameter_Value'], 'required'],
            ['Parameter_Value', 'number'],
            ['Parameter_Value', 'number', 'min' => 0],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'Parameter_Value' => 'Amount',
        ];
    }

}

