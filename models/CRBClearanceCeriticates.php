<?php
namespace app\models;
use Yii;
use yii\base\Model;
use yii\helpers\FileHelper;
use yii\web\UploadedFile;

class CRBClearanceCeriticates extends Model
{

    /**
     * @var UploadedFile
     */
    public $File_path;
    public $Key;
    public $File_Name;
    public $File_Path;
    public $MfilesUrl;
    public $isNewRecord;
    public $LineNo;
    public $MemberNo;
    public $LoanNo;
    public $MfilesURL;
    public $LocalPath;
    public $docFile;
    public $Description;


    public function rules()
    {
        return [
            // [['attachmentfile'],'file','maxFiles'=> Yii::$app->params['LeavemaxUploadFiles']],
            [['docFile'],'required'],
            [['docFile'],'file','maxSize' => '5120000'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'docFile' => 'File to Upload',
            'File_Name' => 'File Description',
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
                $model->LocalPath = $imagePath;
                $model->LoanNo = $model->LoanNo;
                $AlreadyExists = $this->IsAlreadyUploaded();
                // print '<Pre>';
                // print_r($AlreadyExists);
                // exit;

                if($AlreadyExists){ //Do An Update
                    $model->Key = $AlreadyExists->Key;
                    $model->DocumentName = $AlreadyExists->DocumentName;
                    $UploadToEDMSResult = Yii::$app->Mfiles->UpdateCRBDocumentOnEDMS($model);

                    if($UploadToEDMSResult['Uploaded']== false){
                        Yii::$app->session->setFlash('error',$UploadToEDMSResult['Message']);
                        return false;
                    }

                   return  $SaveToNavResult = $this->UpdateDocumentOnNav($model, $UploadToEDMSResult['DocumentUrl']);
                }

               // Create Applicant in EDMS
                     $UploadResult = Yii::$app->Mfiles->UploadCRBDocument($model);                  
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
        $service = Yii::$app->params['ServiceName']['CRBClearanceCeriticates'];
        $result = Yii::$app->navhelper->readByKey($service, urldecode($this->Key));
        // print '<pre>';
        //  print_r($result); exit;

        if(is_object($result)){// found
            return $result;
        }else{
            return false;
        }

    }

    
    public function updateAttachement($docId=false)
    {
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
                $model->File_path = $imagePath;
                $model->Docnum = $model->Docnum;
               // Create Applicant in EDMS
               $CreateApplicantResult = Yii::$app->Mfiles->CreateMember($model->Docnum);
            
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

    public function SaveDocumentToNav($model, $MfilesUrl=''){
        $service = Yii::$app->params['ServiceName']['CRBClearanceCeriticates'];
        $model->LocalPath = basename($model->LocalPath) ;
        $model->LoanNo = $model->LoanNo;
        $model->MemberNo = $model->MemberNo;

        
        $data = [
            'LocalPath' => basename($model->LocalPath),
            'MfilesURL'=>$MfilesUrl,
            'LoanNo' => $model->LoanNo,
            'MemberNo'=>$model->MemberNo,
        ];
         
        $result = Yii::$app->navhelper->postData($service, $data);

        
        if(is_string($result)){
                Yii::$app->session->setFlash('error',$result);
                return false;
        }else{
            Yii::$app->session->setFlash('success', 'File Uploaded Successfully.');
            //;
            return true;
        }
    }

    public function UpdateDocumentOnNav($model, $MfilesUrl){

        $service = Yii::$app->params['ServiceName']['CRBClearanceCeriticates'];
        $result = Yii::$app->navhelper->readByKey($service, $model->Key);
        $model->File_path = basename($model->File_path) ;
        $model->File_Name = $model->File_Name;
        $model->Type = $model->Type;
        $model->Key = $model->Key;
        $model->Member_Category = $model->Member_Category;

        
        $data = [
            'File_Path' => basename($model->File_path),
            'MfilesURL'=>$MfilesUrl,
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
        $service = Yii::$app->params['ServiceName']['CRBClearanceCeriticates'];
        $filter = [
            'LineNo' => $DocNo
        ];

        $result = Yii::$app->navhelper->getData($service,$filter);

        /*print '<pre>';
        print_r($result); exit;*/


        $path = $result[0]->LocalPath;
        $imagePath = Yii::getAlias('C:\\inetpub\\wwwroot\\SaccoMembersPortal\\web\\attachements\\'. $path);


        if(is_file($imagePath))
        {
            $binary = file_get_contents($imagePath);
            $content = chunk_split(base64_encode($binary));
            return $content;
        }
    }

    public function getAttachments($DocNo)
    {

        $service = Yii::$app->params['ServiceName']['ESS_Files'];
        $filter = [
            'Docnum' => $DocNo,
        ];
      
        $result = Yii::$app->navhelper->getData($service,$filter);
        if(is_array($result)){
            return $result;
        }else{
            return false;
        }

    }

    public function getFileProperties($binary)
    {
        $bin  = base64_decode($binary);
        $props =  getImageSizeFromString($bin);
        return $props['mime'];
    }
}