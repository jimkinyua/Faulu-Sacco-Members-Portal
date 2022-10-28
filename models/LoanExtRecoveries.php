<?php

namespace app\models;
use Yii;
use yii\base\Model;
use yii\helpers\FileHelper;


class LoanExtRecoveries extends Model
{
    public $Key;
    public $Recovery_Code;
    public $Description;
    public $Amount;
    public $Commission_Amount;
    public $Recovery_Description;
    public $Exc_Duty_Percent;
    public $Exc_Duty_Account;
    public $Application_No;
    public $Exc_Duty_Amount;
    public $mfilesURL;
    public $LocalPath;

    public $docFile;
    public $FileName;
    public $isNewRecord;
    public $FilePath;

    public function rules()
    {
        return [
            [['Recovery_Code', 'Description', 'Amount', 'docFile'], 'required'],
            ['Amount', 'number'],
            ['Amount', 'number', 'min' => 0],
            [ 'docFile', 'file','extensions' => ['pdf'], 
            'wrongExtension' => 'Only PDF files are allowed for {attribute}.',
            'wrongMimeType' => 'Only PDF files are allowed for {attribute}.',
            'skipOnEmpty'=>true,
            'mimeTypes'=>['application/pdf']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'Recovery_Code' => 'Recovery Type',
            'docFile' => 'Bank Statement For The Loan',

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
                $model->Application_No = $model->Application_No;

                $AlreadyExists = $this->IsAlreadyUploaded();
                
                if($AlreadyExists){ //Do An Update
                    // exit('hapa');
                    $model->Key = $AlreadyExists->Key;
                    $UploadToEDMSResult = Yii::$app->Mfiles->UpdateExternalRecoveryDocumentToEDMS($model);

                    if($UploadToEDMSResult['Uploaded']== false){
                        Yii::$app->session->setFlash('error',$UploadToEDMSResult['Message']);
                        return false;
                    }

                   return $this->UpdateDocumentOnNav($model, $UploadToEDMSResult['DocumentUrl']);
                }
                // Create Applicant in EDMS
                    $UploadResult = Yii::$app->Mfiles->UploadExternalRecoveryDocumentToEDMS($model);                  
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

    public function IsAlreadyUploaded(){
        if(empty($this->Key)){
            return false; //New REcord
        }
        $service = Yii::$app->params['ServiceName']['LoanExtRecoveries'];
        $result = Yii::$app->navhelper->readByKey($service, urldecode($this->Key));
        if(is_object($result)){// found
            return $result;
        }else{
            return false;
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

      public function read($DocNo)
    {
        $service = Yii::$app->params['ServiceName']['LoanApplicationAttachements'];
        $filter = [
            'DocNum' => $DocNo
        ];

        $result = Yii::$app->navhelper->getData($service,$filter);

        /*print '<pre>';
        print_r($result); exit;*/


        $path = $result[0]->FilePath;
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
        $service = Yii::$app->params['ServiceName']['LoanExtRecoveries'];
        $model->FilePath = basename($model->FilePath) ;
        $model->FileName = $model->FileName;
        // exit('h');
        // $model->Type = $model->Type;
        // $model->Member_Category = $model->Member_Category;
        $data = [
            'LocalPath' => basename($model->FilePath),
            'mfilesURL'=>$MfilesUrl,
            'FileName' => $model->FileName,
            'Application_No'=>$model->Application_No,
            'Recovery_Code'=>$model->Recovery_Code,
            'Amount'=>$model->Amount,
            'Description'=>$model->Description
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

        $service = Yii::$app->params['ServiceName']['LoanExtRecoveries'];
        $result = Yii::$app->navhelper->readByKey($service, $model->Key);
        // $model->File_path = basename($model->File_path) ;
        // $model->File_Name = $model->File_Name;
        // $model->Type = $model->Type;
        $model->Key = $model->Key;
        // $model->Member_Category = $model->Member_Category;

        
        $data = [
            'Key'=>$result->Key,
            'LocalPath' => basename($model->FilePath),
            'mfilesURL'=>$MfilesUrl,
            'FileName' => $model->FileName,
            'Application_No'=>$model->Application_No,
            'Recovery_Code'=>$model->Recovery_Code,
            'Amount'=>$model->Amount,
            'Description'=>$model->Description
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

