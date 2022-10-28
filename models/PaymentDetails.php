<?php

namespace app\models;

use Yii;
use yii\base\Model;


class PaymentDetails extends Model
{
    public $Key;
    public $Application_No;
    public $Application_Date;
    public $Posting_Date;
    public $Prorated_Days;
    public $Sales_Person;
    public $Sales_Person_Name;
    public $Member_No;
    public $Witness;
    public $Member_Name;
    public $Global_Dimension_1_Code;
    public $Global_Dimension_2_Code;
    public $Product_Code;
    public $Applied_Amount;
    public $Recommended_Amount;
    public $Approved_Amount;
    public $Recovery_Mode;
    public $Sector_Code;
    public $Sub_Sector_Code;
    public $Sub_Susector_Code;
    public $Product_Description;
    public $Grace_Period;
    public $Repayment_Start_Date;
    public $Loan_Period;
    public $Repayment_End_Date;
    public $Mode_of_Disbursement;
    public $Disbursement_Account;
    public $Interest_Repayment_Method;
    public $Interest_Rate;
    public $Rate_Type;
    public $Total_Securities;
    public $Insurance_Amount;
    public $New_Monthly_Installment;
    public $Principle_Repayment;
    public $Interest_Repayment;
    public $Total_Repayment;
    public $Loan_Account;
    public $Approval_Status;
    public $Pay_to_Bank_Code;
    public $Pay_to_Branch_Code;
    public $Pay_to_Account_No;
    public $Pay_to_Account_Name;
    public $Total_Collateral;
    public $Member_Deposits;
    public $Expected_Amount;
    public $Maximum_Repayment_Period;
    public $DepositsToDate;
    public $RMFToDate;

    public $AgreedToTerms;
    public $isNewRecord;
    public $Portal_Status;
    public function rules()
    {
        return [
            [['New_Monthly_Installment'], 'required'],
            ['Pay_to_Account_No', 'number'],
            ['New_Monthly_Installment', 'number', 'min' => $this->New_Monthly_Installment],


            [['Pay_to_Bank_Code', 'Pay_to_Branch_Code', 'Pay_to_Account_No', 'Pay_to_Account_Name'], 'required', 'when' => function ($model) {
                return $model->Mode_of_Disbursement == 'Bank';
            }, 'whenClient' => "function (attribute, value) {
                return $('#loanapplicationheader-mode_of_disbursement').val() == 'Bank';
            }"],

        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'Product_Code' => 'Select Loan Type',
            'Applied_Amount' => 'Loan Amount',
            'Loan_Period' => 'Repayment Period',
            'Sector_Code' => 'Select Loan Purpose and Sector',
            'New_Monthly_Installment' => 'Monthly Share Contribution'
        ];
    }

    public function getGuarantors()
    {
        $service = Yii::$app->params['ServiceName']['OnllineGuarantorRequests'];
        $filter = [
            'Loan_No' => $this->Application_No,
            'Request_Type' => 'Guarantor'
        ];

        $guarantors = Yii::$app->navhelper->getData($service, $filter);
        if (is_object($guarantors)) {
            return [];
        }
        return $guarantors;
    }

    public function getWitness()
    {
        $service = Yii::$app->params['ServiceName']['OnllineGuarantorRequests'];
        $filter = [
            'Loan_No' => $this->Application_No,
            'Request_Type' => 'Witness'
        ];
        $guarantors = Yii::$app->navhelper->getData($service, $filter);
        if (is_object($guarantors)) {
            return [];
        }
        return $guarantors;
    }

    public function getLoanSecurities()
    {
        $service = Yii::$app->params['ServiceName']['LoanAppSecurities'];
        $filter = [
            'Application_No' => $this->Application_No,
            'Type' => 'Security'
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

    public function getLoanSecuritiesAttachements()
    {
        $service = Yii::$app->params['ServiceName']['LoanApplicationAttachements'];
        $filter = [
            'DocNum' => $this->Application_No,
            'Type' => 'PayslipInformation' || 'BankStatements'
        ];

        $guarantors = Yii::$app->navhelper->getData($service, $filter);
        // echo '<pre>';
        // print_r($filter);
        // exit;
        if (is_object($guarantors)) {
            return [];
        }
        return $guarantors;
    }

    public function getPayslipInformation()
    {
        $service = Yii::$app->params['ServiceName']['LoanAppraisalParameters'];
        $filter = [
            'Loan_No' => $this->Application_No,
            // 'Type'=>'Guarantor'
        ];
        $guarantors = Yii::$app->navhelper->getData($service, $filter);
        if (is_object($guarantors)) {
            return [];
        }
        return $guarantors;
    }

    public function getPayslipAttachements()
    {
        $service = Yii::$app->params['ServiceName']['LoanApplicationAttachements'];
        $filter = [
            'DocNum' => $this->Application_No,
            'Type' => 'PayslipInformation'
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

    public function getBankAttachements()
    {
        $service = Yii::$app->params['ServiceName']['LoanApplicationAttachements'];
        $filter = [
            'DocNum' => $this->Application_No,
            'Type' => 'BankStatements'
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

    public function getDirectDebitInformation()
    {
        $service = Yii::$app->params['ServiceName']['DirectDebitList'];
        $filter = [
            'Loan_No' => $this->Application_No,
        ];
        $guarantors = Yii::$app->navhelper->getData($service, $filter);
        if (is_object($guarantors)) {
            return [];
        }
        return $guarantors;
    }

    public function getCashAnalysisInformation()
    {
        $service = Yii::$app->params['ServiceName']['MicrofinanceCashflow'];
        $filter = [
            'Application_No' => $this->Application_No,
        ];
        $AnalysisInformation = Yii::$app->navhelper->getData($service, $filter);
        if (is_object($AnalysisInformation)) {
            return [];
        }
        return $AnalysisInformation;
    }

    public function getInternalRecoveryInformation()
    {
        $service = Yii::$app->params['ServiceName']['LoanRecoveries'];
        $filter = [
            'Loan_No' => $this->Application_No,
        ];
        $LoanInternalDeductions = Yii::$app->navhelper->getData($service, $filter);
        if (is_object($LoanInternalDeductions)) {
            return [];
        }
        return $LoanInternalDeductions;
    }

    public function getExternalRecoveryInformation()
    {
        $service = Yii::$app->params['ServiceName']['LoanExtRecoveries'];
        $filter = [
            'Application_No' => $this->Application_No,
        ];
        $LoanExtRecoveries = Yii::$app->navhelper->getData($service, $filter);
        if (is_object($LoanExtRecoveries)) {
            return [];
        }
        return $LoanExtRecoveries;
    }

    public function getCRBClearanceCertificates()
    {
        $service = Yii::$app->params['ServiceName']['CRBClearanceCeriticates'];
        $filter = [
            'LoanNo' => $this->Application_No,
        ];
        $CRBClearanceCeriticates = Yii::$app->navhelper->getData($service, $filter);
        if (is_object($CRBClearanceCeriticates)) {
            return [];
        }
        return $CRBClearanceCeriticates;
    }
}
