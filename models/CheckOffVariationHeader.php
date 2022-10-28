<?php

namespace app\models;

use Yii;
use yii\base\Model;


class CheckOffVariationHeader extends Model
{
    public $Key;
    public $Document_No;
    public $Member_No;
    public $Member_Name;
    public $Effective_Date;
    public $Created_By;
    public $Created_On;
    public $Status;
    public $isNewRecord;
    public $Portal_Status;

    public function rules()
    {
        return [
            [['Effective_Date'], 'required'],

            ['Account_No', 'required', 'when' => function ($model) {
                return $model->Source_Type == 'Bank';
            }, 'whenClient' => "function (attribute, value) {
                return $('#fixeddepositcard-source_type').val() == 'Bank';
            }"],


        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'Fixed_Period_M' => 'Fixed Period in Months',
            'FD_Type' => 'Fixed Deposit Type',
            'Source_Of_Funds' => 'Source of Funds'
        ];
    }

    public function getCheckOffVariationLines()
    {
        $service = Yii::$app->params['ServiceName']['CheckoffVariationLines'];
        $filter = [
            'Document_No' => $this->Document_No,
        ];
        $CheckoffVariationLines = Yii::$app->navhelper->getData($service, $filter);
        if (is_object($CheckoffVariationLines)) {
            return [];
        }

        return $CheckoffVariationLines;
    }
}
