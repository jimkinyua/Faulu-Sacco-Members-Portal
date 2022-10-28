<?php

namespace app\models;

use Yii;
use yii\base\Model;

/**
 * ContactForm is the model behind the contact form.
 */
class MemberStatement extends Model
{
    public $startDate;
    public $endDate;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['startDate', 'endDate'], 'required'],
            [['startDate', 'endDate'], 'date', 'min' => time(), 'minString' => date('d-m-Y'), 'format' => 'php:d-m-Y']

        ];
    }

    /**
     * @return array customized attribute labels
     */
    public function attributeLabels()
    {
        return [
            // 'verifyCode' => 'Verification Code',
        ];
    }


}
