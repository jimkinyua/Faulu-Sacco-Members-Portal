<?php

namespace app\models;

use Yii;
use yii\base\Model;


class LoanApplicationHeader extends Model
{
    public $Key;
    public $Loan_No;
    public $Member_No;
    public $Member_Name;
    public $Deposit_Savings;
    public $Mode_of_Disbursement;
    public $Interest_Calculation_Method;
    public $Loan_Product_Type;
    public $Loan_Product_Type_Name;
    public $Repayment_Frequency;
    public $Recovery_Mode;
    public $Installments;
    public $Interest;
    public $Requested_Amount;
    public $Approved_Amount;
    public $Recommended_Amount;
    public $Amount_To_Disburse;
    public $Boosting_Charges;
    public $Loans_Deposit_Purchase;
    public $Shares_Boosting_Account;
    public $Deposits_Multiplier;
    public $Disbursement_Destination;
    public $Disbursement_Account_No;
    public $Loan_Account;
    public $Batch_No;
    public $Total_TopUp;
    public $Amount_Guaranteed;
    public $Loan_No__x005B_Buyoff_x005D_;
    public $Repayment;
    public $Disbursement_Date;
    public $Repayment_Start_Date;
    public $Expected_Date_of_Completion;
    public $Appraisal_Parameter_Type;
    public $Loan_Rejection_Reason;
    public $Sectors;
    public $Sub_Sectors;
    public $Purpose_of_Loan;
    public $Remarks;
    public $Status;
    public $Loan_Principle_Repayment;
    public $Loan_Interest_Repayment;
    public $Captured_By;
    public $Responsibility_Centre;
    public $Global_Dimension_1_Code;
    public $Global_Dimension_2_Code;

    public $AgreedToTerms;
    public $isNewRecord;
    public $Portal_Status;
    public $ApplicationForm;
    public $PayslipDetails;
    public $Payslip;
    public $Application_Form;

    public function rules()
    {
        return [
            [['Loan_Product', 'Member_No', 'Sector_Code', 'Recovery_Mode', 
            'Installments', 'Subsector_Code', 'Application_Category', 'Sub_Subsector_Code', 
            'Applied_Amount', 'Disbursement_Account'], 'required'],

            [['Pay_to_Bank_Code', 'Pay_to_Branch_Code', 'Pay_to_Account_No', 'Pay_to_Account_Name'], 'required', 'when' => function ($model) {
                return $model->Mode_of_Disbursement == 'Bank';
            }, 'whenClient' => "function (attribute, value) {
                return $('#loanapplicationheader-mode_of_disbursement').val() == 'Bank';
            }"],

            ['Amount', 'number', 'min' => 0],
            ['AgreedToTerms', 'compare', 'compareValue' => 1, 'message' => 'You should accept term Our Terms and Condiitions'],

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
            'PayslipDetails'=>'Latest Payslip',
        ];
    }

    public function getGuarantors()
    {
        $service = Yii::$app->params['ServiceName']['LoanGuarantors'];
        $filter = [
            'Loan_No' => $this->Loan_No,
            'Guarantor_Type' => 'Guarantor'
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
        $service = Yii::$app->params['ServiceName']['AppraisalSalaryDetails'];
        $filter = [
            'Loan_No' => $this->Loan_No,
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
        // exit($this->Application_No);
        $service = Yii::$app->params['ServiceName']['LoanTopUp'];
        $filter = [
            'Loan_No' => $this->Loan_No,
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
