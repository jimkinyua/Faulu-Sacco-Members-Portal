<?php
namespace app\models;
use Yii;
use yii\base\Model;
use yii\helpers\FileHelper;
use yii\web\UploadedFile;

class GroupSigatoriesAttachements extends Model
{

    /**
     * @var UploadedFile
     */
    public $Key;
    public $DocNum;
    public $FilePath;
    public $FileName;
    public $MfilesUrl;
    public $Member_Category;
    public $isNewRecord;
    public $docFile;
    public $Type;
    public $IdentificationNo;

    public $uploadFilesArray;


    public function rules()
    {
        return [
            // [['attachmentfile'],'file','maxFiles'=> Yii::$app->params['LeavemaxUploadFiles']],
            [['docFile'],'required'],
            // [['docFile'],'file','maxSize' => '5120000'],
            // [ 'docFile', 'file','extensions' => ['pdf'], 
            // 'wrongExtension' => 'Only PDF files are allowed for {attribute}.',
            // 'wrongMimeType' => 'Only PDF files are allowed for {attribute}.',
            // 'skipOnEmpty'=>false,
            // 'mimeTypes'=>['application/pdf']],
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
                $model->FilePath = $imagePath;
                $model->IdentificationNo = $model->IdentificationNo;
                $AlreadyExists = $this->IsAlreadyUploaded($model->IdentificationNo, $model->FileName, $model->DocNum);

                if($AlreadyExists){ //Do An Update
                    exit('japa');

                    $model->Key = $AlreadyExists[0]->Key;
                    $UploadToEDMSResult = Yii::$app->Mfiles->UpdateSignatoryDocumentToEDMS($model);
                   return  $SaveToNavResult = $this->UpdateDocumentOnNav($model, 'Not Yet Intergrated');
                }

                // exit('Huko');


                $UploadToEDMSResult = Yii::$app->Mfiles->UploadSignatoryDocumentToEDMS($model);
                if($UploadToEDMSResult['Uploaded']== false){
                    Yii::$app->session->setFlash('error',$UploadToEDMSResult['Message']);
                    return false;
                }else{
                    //Post to Nav
                   return $this->SaveDocumentToNav($model, $UploadToEDMSResult['DocumentUrl']);//$UploadToEDMSResult['DocumentUrl']
                }
            }
        }else{
            // return false;
            print '<Pre>';
            print_r($model->getErrors());
            exit;
            
        }
    }

    public function CreateSignatoryOnEDMS($KinModel){
        $CreateApplicantResult = Yii::$app->Mfiles->CreateSignatory($KinModel);
        if($CreateApplicantResult['Exists']== true || $CreateApplicantResult['Created']== true){
            return true;
        }
        return false;

    }

    public function UpdateSignatoryDetailsOnEDMS($KinModel){
        $CreateApplicantResult = Yii::$app->Mfiles->UpdateSignatory($KinModel);
        if($CreateApplicantResult['Exists']== true || $CreateApplicantResult['Created']== true){
            return true;
        }
        return false;

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
        $service = Yii::$app->params['ServiceName']['GroupSignatoriesAttachements'];
        $model->FilePath = basename($model->FilePath) ;
        $model->FileName = $model->FileName;
        // $model->Type = $model->Type;
        // $model->Member_Category = $model->Member_Category;

        
        $data = [
            'FilePath' => basename($model->FilePath),
            'MfilesURL'=>$MfilesUrl,
            'FileName' => $model->FileName,
            'DocNum'=>$model->DocNum,
            'SignatoryID'=>$model->IdentificationNo
        ];

      
         
        $result = Yii::$app->navhelper->postData($service, $data);
        // echo '<pre>';
        // print_r($result);
        // exit;

        
        if(is_string($result)){
                Yii::$app->session->setFlash('error',$result);
                return $this->redirect(Yii::$app->request->referrer);
        }else{
            Yii::$app->session->setFlash('success', 'File Uploaded Successfully.');
            //;
            return true;
        }
    }

    public function UpdateDocumentOnNav($model, $MfilesUrl){

        $service = Yii::$app->params['ServiceName']['GroupSignatoriesAttachements'];
        //  print '<Pre>';
        //     print_r($model);
        //     exit;

        $result = Yii::$app->navhelper->readByKey($service, $model->Key);
        // $model->File_path = basename($model->File_path) ;
        // $model->File_Name = $model->File_Name;
        // $model->Type = $model->Type;
        // $model->Key = $model->Key;
        // $model->Member_Category = $model->Member_Category;

        
        $data = [
            'Key'=>$result->Key,
            'FilePath' => basename($model->FilePath),
            'MfilesURL'=>$MfilesUrl,
            'FileName' => $model->FileName,
            'DocNum'=>$model->DocNum,
            'Identification'=>$model->IdentificationNo
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

    public function read($Key){
        $service = Yii::$app->params['ServiceName']['GroupSignatoriesAttachements'];
       

        $result = Yii::$app->navhelper->readByKey($service, urldecode(Yii::$app->request->get('Key')));

        // print '<pre>';
        // print_r($result); exit;


        $path = $result->FilePath;
        $imagePath = Yii::getAlias('C:\\inetpub\\wwwroot\\SaccoMembersPortal\\web\\attachements\\'. $path);

       
        if(is_file($imagePath))
        {
            $binary = file_get_contents($imagePath);
            $content = chunk_split(base64_encode($binary));
            return $content;
        }
    }

    public function getAttachments($DocNo, $ID_No)
    {

        $service = Yii::$app->params['ServiceName']['GroupSignatoriesAttachements'];
        $filter = [
            'DocNum' => $DocNo,
            // 'SignatoryID'=>$ID_No
        ];      
        $result = Yii::$app->navhelper->getData($service,$filter);
        // echo '<pre>';
        // print_r($filter);
        // exit;

        if(is_array($result)){
            return $result;
        }else{
            return false;
        }

    }

    public function IsAlreadyUploaded($IdentificationNo, $FileName, $DocNum){

        $service = Yii::$app->params['ServiceName']['GroupSignatoriesAttachements'];
        $filter = [
            'SignatoryID' => $IdentificationNo,
            'FileName' => $FileName,
            'DocNum' => $DocNum,

        ];
    
         

        $result = Yii::$app->navhelper->getData($service,$filter);
        // print '<pre>';
        //  print_r($result); exit;

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