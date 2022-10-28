<?php

namespace app\models;

use Yii;
use yii\base\Model;


class GuarantorManagementCard extends Model
{
    public $Key;
    public $Document_No;
    public $Member_No;
    public $Member_Name;
    public $Loan_No;
    public $Type;
    public $Approval_Status;
    public $Loan_Balance;
    public $Member_Deposit;
    public $Outstanding_Guarantee;
    public $Created_On;
    public $Created_By;
    public $Last_Updated_By;
    public $Last_Updated_On;
    public $Processed_On;
    public $Processed_By;
    public $Portal_Status;
    public $Processed;

    public function rules()
    {
        return [
            [['Loan_No',], 'required'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            // 'Type' => 'Action Type',
        ];
    }

    public function getLines()
    {
        $service = Yii::$app->params['ServiceName']['GuarantorLines'];
        $filter = [
            'Document_No' => $this->Document_No,
            // 'Type' => 'Guarantor'
        ];
        $guarantors = Yii::$app->navhelper->getData($service, $filter);
        // echo '<pre>';
        // print_r($guarantors);
        // exit;
        if (is_object($guarantors)) {
            return [];
        }
        return $guarantors;
    }


    public function getReplacements()
    {
        $service = Yii::$app->params['ServiceName']['OnlineGuarantorSubRequests'];
        $filter = [
            'Document_No' => $this->Document_No,
            // 'Type'=>'Guarantor'
        ];
        $guarantors = Yii::$app->navhelper->getData($service, $filter);
        // echo '<pre>';
        // print_r($guarantors);
        // exit;
        if (is_object($guarantors)) {
            return [];
        }
        return $guarantors;
    }
}
