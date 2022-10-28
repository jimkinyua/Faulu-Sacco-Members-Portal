<?php

namespace app\models;
use Yii;
use yii\base\Model;
use yii\helpers\FileHelper;


class LoanAppSecurities extends Model
{
   public $Type;
   public $Key;
   public $Code;
   public $Staff_No;
   public $Description;
   public $Phone_No;
   public $Account_Types;
   public $Value;
   public $Max_Guaranteed_Amount;
   public $Amount_Guaranteed;
   public $Amount_Released;
   public $Outstanding_Guarantee;
   public $Guarantor_Notified;
   public $Released;
   public $Guarantees;
   public $Max_Allowed_Guarantee;
   public $Application_No;
   public $Deposits;
   public $LocalURL;
   public $mfilesURL;


   public $docFile;
   public $FileName;
   public $isNewRecord;
   public $FilePath;

    public function rules()
    {
        return [

            [['Code', 'docFile'], 'required'],
            ['Amount_Guaranteed', 'number'],
            ['docFile', 'file','extensions' => ['pdf'], 
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
            'Code' => 'Security',
            'docFile'=>'Security Attachment',
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
                    $model->Key = $AlreadyExists->Key;
                    $UploadToEDMSResult = Yii::$app->Mfiles->UpdateSecurityOnDocumentOnEDMS($model);

                    if($UploadToEDMSResult['Uploaded']== false){
                        Yii::$app->session->setFlash('error',$UploadToEDMSResult['Message']);
                        return false;
                    }

                   return $this->UpdateDocumentOnNav($model, $UploadToEDMSResult['DocumentUrl']);
                }

                // Create Applicant in EDMS
                    $UploadResult = Yii::$app->Mfiles->UploadSecurityDocumentToEDMS($model);                  
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
            print_r($model->getErrors());
            exit;
            
        }
    }

    public function IsAlreadyUploaded(){
        if(empty($this->Key)){
            return false; //New REcord
        }
        $service = Yii::$app->params['ServiceName']['LoanAppSecurities'];
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
        $service = Yii::$app->params['ServiceName']['LoanAppSecurities'];
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

      public function read($Key){
          
        $service = Yii::$app->params['ServiceName']['LoanAppSecurities'];

        $result = Yii::$app->navhelper->readByKey($service, urldecode($Key));

        $path = $result->LocalURL;
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
        $service = Yii::$app->params['ServiceName']['LoanAppSecurities'];
        $model->FilePath = basename($model->FilePath) ;
        $model->FileName = $model->FileName;
        
        // exit('h');
        // $model->Type = $model->Type;
        // $model->Member_Category = $model->Member_Category;

        
        $data = [
            'LocalURL' => basename($model->FilePath),
            'Code'=>$model->Code,
            'mfilesURL'=>$MfilesUrl,
            'Type'=>$model->Type,
            'Application_No'=>$model->Application_No,
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

        $service = Yii::$app->params['ServiceName']['ESS_Files'];
        $result = Yii::$app->navhelper->readByKey($service, $model->Key);
        $model->File_path = basename($model->File_path) ;
        $model->File_Name = $model->File_Name;
        $model->Type = $model->Type;
        $model->Key = $model->Key;
        $model->Member_Category = $model->Member_Category;

        
        $data = [
            'File_Path' => basename($model->File_path),
            'MfilesUrl'=>$MfilesUrl,
            'File_Name' => $model->File_Name,
            'Type'=>$model->Type,
            'Key'=>$result->Key,
            'Docnum'=>$model->Docnum,
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

?>

