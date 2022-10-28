<?php

namespace app\models;
use Yii;
use yii\base\Model;
use yii\helpers\FileHelper;
use yii\web\UploadedFile;


class DirectDebitList extends Model
{
    public $Key;
    public $Member_No;
    public $Member_Name;
    public $ID_No;
    public $Address;
    public $Mobile_No;
    public $Bank_Code;
    public $Bank_Name;
    public $Branch_Code;
    public $Branch_Name;
    public $Account_No;
    public $Amount;
    public $Loan_No;
    public $docFile;
    public $FileName;
    public $isNewRecord;
    public $FilePath;
    public $LocalPath;
    public $mfilesURL;
    public $LineNo;
    public $DocumentName;

    public function rules(){
        return [
            [['Bank_Code', 'Branch_Code', 'Account_No', 'Amount'], 'required'],
            ['Amount', 'number'],
            ['Amount', 'number', 'min' => 1],

            [ 'docFile', 'file','extensions' => ['pdf'], 
            'wrongExtension' => 'Only PDF files are allowed for {attribute}.',
            'wrongMimeType' => 'Only PDF files are allowed for {attribute}.',
            'skipOnEmpty'=>true,
            'mimeTypes'=>['application/pdf'], 'when' => function($model) {
                return $model->isNewRecord == true;
            }],

            ['docFile', 'required', 'when' => function($model) {
                return $model->isNewRecord == true;
            }],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'Bank_Code' => 'Bank Name',
            'Branch_Code' => 'Branch Name',
            'docFile' => 'Direct Debit Form',
        ];
    }

    public function upload($docId=false){
        $model = $this;

        $imageId = Yii::$app->security->generateRandomString(8);

        $imagePath = Yii::getAlias('C:\\inetpub\\wwwroot\\SaccoMembersPortal\\web\\attachements\\'.$imageId.'.'.$this->docFile->extension);
        // exit($imagePath);
        if($model->validate()){
            // Check if directory exists, else create it
            if(!is_dir(dirname($imagePath))){
                FileHelper::createDirectory(dirname($imagePath));
            }
            if($this->docFile->saveAs($imagePath)){
                $model->FilePath = $imagePath;
                $model->Loan_No = $model->Loan_No;
                $AlreadyExists = $this->IsAlreadyUploaded();
                // print '<Pre>';
                // print_r($AlreadyExists);
                // exit;

                if($AlreadyExists){ //Do An Update
                    $model->Key = $AlreadyExists->Key;
                    $model->DocumentName = $AlreadyExists->DocumentName;
                    $UploadToEDMSResult = Yii::$app->Mfiles->UpdateDirectDebitDocument($model, $AlreadyExists);

                    if($UploadToEDMSResult['Uploaded']== false){
                        Yii::$app->session->setFlash('error',$UploadToEDMSResult['Message']);
                        return false;
                    }

                   return  $SaveToNavResult = $this->UpdateDocumentOnNav($model, $UploadToEDMSResult['DocumentUrl']);
                }

                // Create Applicant in EDMS
                $UploadResult = Yii::$app->Mfiles->UploadDirectDebitDocument($model);                  
                if($UploadResult['Uploaded'] == false){
                        Yii::$app->session->setFlash('error',$UploadResult['Message']);
                        return false;
                }

                //Post to Nav
               return $this->SaveDocumentToNav($model, $UploadResult['DocumentUrl']);
            }
        }else{
            // return false;
            print '<Pre>';
            // print_r($model->getErrorSummary());
            exit;
            
        }
    }


    public function getPath($DocNo=''){
        if(!$DocNo){
            return false;
        }
        $service = Yii::$app->params['ServiceName']['ESS_Files'];
        $filter = [
            'Docnum' => $DocNo
        ];

        $result = Yii::$app->navhelper->getData($service,$filter);
            // print '<Pre>';
            // print_r($model->getErrorSummary());
            // exit;
        if(is_array($result)) {
            return basename($result[0]->File_Path);
        }else{
            return false;
        }

    }

    public function IsAlreadyUploaded(){
        if(empty($this->Key)){
            return false; //New REcord
        }
        $service = Yii::$app->params['ServiceName']['DirectDebitList'];
        $result = Yii::$app->navhelper->readByKey($service, urldecode($this->Key));
        // print '<pre>';
        //  print_r($result); exit;

        if(is_object($result)){// found
            return $result;
        }else{
            return false;
        }

    }


      public function read($DocNo)
    {
        $service = Yii::$app->params['ServiceName']['DirectDebitList'];
        $filter = [
            'LineNo' => $DocNo
        ];

        $result = Yii::$app->navhelper->getData($service,$filter);

        // print '<pre>';
        // print_r($result); exit;


        $path = $result[0]->LocalPath;
        $imagePath = Yii::getAlias('C:\\inetpub\\wwwroot\\SaccoMembersPortal\\web\\attachements\\'. $path);


        if(is_file($imagePath))
        {
            $binary = file_get_contents($imagePath);
            $content = chunk_split(base64_encode($binary));
            return $content;
        }
    }

    public function updateAttachement($docId=false){
        $model = $this;

        $imageId = Yii::$app->security->generateRandomString(8);

        $imagePath = Yii::getAlias('C:\\inetpub\\wwwroot\\SaccoMembersPortal\\web\\attachements\\'.$imageId.'.'.$this->docFile->extension);
        // exit($imagePath);
        if($model->validate()){
            // Check if directory exists, else create it
            if(!is_dir(dirname($imagePath))){
                FileHelper::createDirectory(dirname($imagePath));
            }

            if($this->docFile->saveAs($imagePath)){
                $model->FilePath = $imagePath;
                $model->DocNum = $model->DocNum;
               // Create Applicant in EDMS
               $CreateApplicantResult = Yii::$app->Mfiles->CreateMember($model->DocNum);
            
                if($CreateApplicantResult['Exists']== true || $CreateApplicantResult['Created']== true){
                     $UploadResult = Yii::$app->Mfiles->UpdateApplicantDocument($model);                  
                     if($UploadResult['Uploaded'] == false){
                              Yii::$app->session->setFlash('error',$UploadResult['Message']);
                              return false;
                     }
                }
                //Post to Nav
                $SaveToNavResult = $this->UpdateDocumentOnNav($model, $UploadResult['DocumentUrl']);
            }
        }else{
            // return false;
            print '<Pre>';
            // print_r($model->getErrorSummary());
            exit;
            
        }
    }

    public function SaveDocumentToNav($model, $MfilesUrl){
        $service = Yii::$app->params['ServiceName']['DirectDebitList'];
        $model->FilePath = basename($model->FilePath) ;
        $model->FileName = $model->FileName;
        // exit('h');
        // $model->Type = $model->Type;
        // $model->Member_Category = $model->Member_Category;

        
        $data = [
            'LocalPath' => basename($model->FilePath),
            'mfilesURL'=>$MfilesUrl,
            'Member_No' => $model->Member_No,
            'Loan_No'=>$model->Loan_No,
            'Bank_Code'=>$model->Bank_Code,
            'Account_No'=>$model->Account_No,
            'Amount'=>$model->Amount,
            'Branch_Code'=>$model->Branch_Code,
            'DocumentName'=>$this->DocumentName
        ];
         
        $result = Yii::$app->navhelper->postData($service, $data);
        // echo '<pre>';
        // print_r($result);
        // exit;       
        if(is_string($result)){
                Yii::$app->session->setFlash('error',$result);
                return false;
        }else{
            return true;
        }
    }

    public function UpdateDocumentOnNav($model, $MfilesUrl){

        $service = Yii::$app->params['ServiceName']['DirectDebitList'];
        $result = Yii::$app->navhelper->readByKey($service, $model->Key);        
        $data = [
            'Key'=>$result->Key,
            'LocalPath' => basename($model->FilePath),
            'mfilesURL'=>$MfilesUrl,
            'Member_No' => $model->Member_No,
            'Loan_No'=>$model->Loan_No,
            'Bank_Code'=>$model->Bank_Code,
            'Account_No'=>$model->Account_No,
            'Amount'=>$model->Amount,
            'Branch_Code'=>$model->Branch_Code
        ];
         
        $Updateresult = Yii::$app->navhelper->updateData($service,$data);
        
        if(is_string($Updateresult)){
                Yii::$app->session->setFlash('error',$Updateresult);
                return false;
        }else{
            Yii::$app->session->setFlash('success', 'File Uploaded Successfully.');
            //;
            return true;
        }
    }

}

