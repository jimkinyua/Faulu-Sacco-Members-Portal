<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\bootstrap4\Html;
use yii\helpers\FileHelper;
use yii\web\UploadedFile;

class Attachement extends Model
{

    /**
     * @var UploadedFile
     */
    public $Line_No;
    public $Name;
    public $File_path;
    public $Key;
    public $attachmentfile;
    public $docFile;
    public $File_Name;
    public $File_Path;
    public $Type;
    public $Docnum;
    public $Member_Category;
    public $MfilesUrl;
    public $isNewRecord;
    public $AttachementID;

    public $ApplicationForm;
    public $PayslipDetails;

    public $payslipPath;
    public $ApplicationFormPath;

    public function rules()
    {
        return [
            // [['ApplicationForm','PayslipDetails'],'file','maxFiles'=> Yii::$app->params['LeavemaxUploadFiles']],
            // [['PayslipDetails', 'ApplicationForm'], 'required'],
            // [['PayslipDetails', 'ApplicationForm'], 'file', 'maxSize' => '5120000'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'PayslipDetails' => 'Latest Payslip',
            'ApplicationForm' => 'Application Form',
        ];
    }

    public function upload()
    {
        $model = $this;

        $RandomString = Yii::$app->security->generateRandomString(8);
        //  echo '<pre>';
        // print_r(env('Signed_Loan_Applications_Folder') . $this->Docnum . '.' . $this->PayslipDetails->extension);
        // exit;

        if($this->PayslipDetails){
            $this->payslipPath = env('Signed_Loan_Applications_Folder') .'_' .$this->Docnum . '.' . $this->PayslipDetails->extension;
        }
        if($this->ApplicationForm){
            $this->ApplicationFormPath = env('Loan_Application_Payslips') .'_'. $this->Docnum . '.' . $this->ApplicationForm->extension;
        }

        
        // exit($imagePath);
        // if ($model->validate()) {
            // Check if directory exists, else create it
            if (!is_dir(dirname($this->payslipPath))) {
                FileHelper::createDirectory(dirname($this->payslipPath));
            }
            // if (!is_dir(dirname($this->ApplicationFormPath))) {
            //     FileHelper::createDirectory(dirname($this->ApplicationFormPath));
            // }

            if ($this->PayslipDetails->saveAs($this->payslipPath) ) {
                $model->Docnum = $model->Docnum;
                return $this->SaveDocumentToNav($model);
            // }
        } else {
            return  $model->getErrors();
        }
    }

    public function SetMemberImage($imageType)
    {

        $service = Yii::$app->params['ServiceName']['PortalIntegrations'];
        $Type = '';
        switch ($imageType) {
            case 4: //Back of Id
                $Type = 2;
                break;
            case 3: //Front of Id
                $Type = 1;
                break;

            case 2: // Siganture
                $Type = 3;
                break;

            case 1: // Picture of Applicant
                $Type = 0;
                break;
        }
        $data = [
            'applicationNo' => $this->Docnum,
            'imagePath' => $this->File_path,
            'imageType' => $Type,
        ];

        $res = Yii::$app->navhelper->PortalReports($service, $data, 'SetMemberImage');

        return true;
    }


    public function updateAttachement($docId = false)
    {
        $model = $this;

        $imageId = Yii::$app->security->generateRandomString(8);

        $imagePath = Yii::getAlias('C:\\inetpub\\wwwroot\\SaccoMembersPortal\\web\\attachements\\' . $imageId . '.' . $this->docFile->extension);
        // exit($imagePath);
        if ($model->validate()) {
            // Check if directory exists, else create it
            if (!is_dir(dirname($imagePath))) {
                FileHelper::createDirectory(dirname($imagePath));
            }

            if ($this->docFile->saveAs($imagePath)) {
                $model->File_path = $imagePath;
                $model->Docnum = $model->Docnum;
                $UploadResult = Yii::$app->Mfiles->UpdateApplicantDocument($model);

                if ($UploadResult['Uploaded'] == false) {
                    Yii::$app->session->setFlash('error', $UploadResult['Message']);
                    return false;
                }
                $AlreadyExists = $this->IsAlreadyUploaded($model->DocNum, $model->FileName);

                if ($AlreadyExists) { //Do An Update
                    $model->Key = $AlreadyExists[0]->Key;
                    return  $SaveToNavResult = $this->UpdateDocumentOnNav($model, 'Not Yet Intergrated');
                }
                return $this->SaveDocumentToNav($model, $UploadResult['DocumentUrl']);
            }
        } else {
            // return false;
            print '<Pre>';
            // print_r($model->getErrorSummary());
            exit;
        }
    }

    public function IsAlreadyUploaded($DocNo, $FileName)
    {

        $service = Yii::$app->params['ServiceName']['ESS_Files'];
        $filter = [
            'Docnum' => $DocNo,
            'File_Name' => $FileName,
        ];



        $result = Yii::$app->navhelper->getData($service, $filter);
        // print '<pre>';
        //  print_r($result); exit;

        if (is_array($result)) {
            return $result;
        } else {
            return false;
        }
    }

    public function SaveDocumentToNav($model)
    {
        $service = Yii::$app->params['ServiceName']['LoanApplicationCard'];

        $data = [  
            'Payslip' => $this->payslipPath,
            'Application_Form' => $this->ApplicationFormPath,
            'Key' => $this->Key,
        ];

        $result = Yii::$app->navhelper->updateData($service, $data);


        if (is_string($result)) {
            Yii::$app->session->setFlash('error', $result);
            return false;
        } else {
            Yii::$app->session->setFlash('success', 'File Uploaded Successfully.');
            //;
            return true;
        }
    }

    public function UpdateDocumentOnNav($model, $MfilesUrl)
    {

        $service = Yii::$app->params['ServiceName']['ESS_Files'];
        $result = Yii::$app->navhelper->readByKey($service, $model->Key);
        $model->File_path = basename($model->File_path);
        $model->File_Name = $model->File_Name;
        $model->Type = $model->Type;
        $model->Key = $model->Key;
        $model->Member_Category = $model->Member_Category;


        $data = [
            'File_Path' => basename($model->File_path),
            'MfilesUrl' => $MfilesUrl,
            'File_Name' => $model->File_Name,
            'Type' => $model->Type,
            'Key' => $result->Key,
            'Docnum' => $model->Docnum,
        ];

        $Updateresult = Yii::$app->navhelper->updateData($service, $data);

        if (is_string($Updateresult)) {
            Yii::$app->session->setFlash('error', $Updateresult);
            return false;
        } else {
            Yii::$app->session->setFlash('success', 'File Uploaded Successfully.');
            //;
            return true;
        }
    }




    public function getPath($DocNo = '')
    {
        if (!$DocNo) {
            return false;
        }
        $service = Yii::$app->params['ServiceName']['ESS_Files'];
        $filter = [
            'Docnum' => $DocNo
        ];

        $result = Yii::$app->navhelper->getData($service, $filter);
        // print '<Pre>';
        // print_r($model->getErrorSummary());
        // exit;
        if (is_array($result)) {
            return basename($result[0]->File_Path);
        } else {
            return false;
        }
    }

    public function read($Url)
    {
        $service = Yii::$app->params['ServiceName']['ESS_Files'];



        if (is_file($Url)) {
            $binary = file_get_contents($Url);
            $content = chunk_split(base64_encode($binary));
            return $content;
        }
    }

    public function getAttachments($DocNo)
    {

        $service = Yii::$app->params['ServiceName']['ESS_Files'];
        $filter = [
            'Docnum' => $DocNo,
            // 'File_Name'=>'SignedApplicationForm',
        ];

        $result = Yii::$app->navhelper->getData($service, $filter);
        if (is_array($result)) {
            return $result;
        } else {
            return false;
        }
    }

    public function getSignedAppilicationAttachment()
    {

        $service = Yii::$app->params['ServiceName']['ESS_Files'];
        $filter = [
            'Docnum' => $this->Docnum,
            'File_Name' => 'Signed Application Form',
        ];


        $result = Yii::$app->navhelper->getData($service, $filter);

        if (is_array($result)) {
            return $result;
        } else {
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
