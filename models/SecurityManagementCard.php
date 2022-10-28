<?php

namespace app\models;
use Yii;
use yii\base\Model;


class SecurityManagementCard extends Model{
    public $Key;
    public $Document_No;
    public $Member_No;
    public $Member_Name;
    public $Loan_No;
    public $Type;
    public $Approval_Status;
    public $Outstanding_Guarantee;
    public $Created_On;
    public $Created_By;
    public $Last_Updated_By;
    public $Last_Updated_On;
    public $Processed_On;
    public $Processed_By;
    public $Processed;
    public $Portal_Status;
   
    public function rules()
    {
        return [  
            [['Loan_No',], 'required'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(){
        return [
            // 'Type' => 'Action Type',
        ];
    }

    public function getLines(){
        $service = Yii::$app->params['ServiceName']['SecurityLines'];
        $filter = [
            'Document_No' => $this->Document_No,
            // 'Type'=>'Guarantor'
        ];
        $guarantors = Yii::$app->navhelper->getData($service,$filter);
        if(is_object($guarantors)){
            return [];
        }
        return $guarantors;
    }

    
    public function getReplacements(){
        $service = Yii::$app->params['ServiceName']['SecurityReplacements'];
        $filter = [
            'Document_No' => $this->Document_No,
            // 'Type'=>'Guarantor'
        ];
        $guarantors = Yii::$app->navhelper->getData($service,$filter);
        if(is_object($guarantors)){
            return [];
        }
        return $guarantors;
    }

 
}

?>

