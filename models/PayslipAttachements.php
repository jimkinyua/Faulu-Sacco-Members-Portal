<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\helpers\FileHelper;
use yii\web\UploadedFile;

class PayslipAttachements extends Model
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
    public $MemberNo;
    public $isSelfEmployed;

    //Files
    public $PayslipOne;
    public $PayslipTwo;
    public $Letter;
    public $CertifiedBankStatements;

    public $PayslipOnePath;
    public $PayslipTwoPath;
    public $LetterPath;
    public $CertifiedBankStatementsPath;

    public $uploadFilesArray;

    public $LetterAttached;
    public $PayslipOneAttached;
    public $PayslipTwoattached;
    public $CertifiedBankStatementsAttached;

    public $DocumentsAttached;

    public function rules()
    {
        return [
            // [['attachmentfile'],'file','maxFiles'=> Yii::$app->params['LeavemaxUploadFiles']],
            [['PayslipTwo',], 'required', 'when' => function ($model) {
                return $model->isSelfEmployed == 0  && $model->PayslipTwoattached == 0;
            }, 'whenClient' => "function (attribute, value) {
                return $('#payslipattachements-isselfemployed').val() == '0' && $('#payslipattachements-paysliptwoattached').val() == '0'
            }"],

            [['PayslipOne',], 'required', 'when' => function ($model) {
                return $model->isSelfEmployed == 0 && $model->PayslipOneAttached == 0;
            }, 'whenClient' => "function (attribute, value) {
                return $('#payslipattachements-isselfemployed').val() == '0' && $('#payslipattachements-paysliponeattached').val() == '0';
            }"],

            [['Letter',], 'required', 'when' => function ($model) {
                return $model->isSelfEmployed == 1 && $model->LetterAttached == 0;
            }, 'whenClient' => "function (attribute, value) {
                return $('#payslipattachements-isselfemployed').val() == '1' && $('#payslipattachements-letterattached').val() == '0';
            }"],

            [['CertifiedBankStatements',], 'required', 'when' => function ($model) {
                return $model->isSelfEmployed == 1 && $model->CertifiedBankStatementsAttached == 0;
            }, 'whenClient' => "function (attribute, value) {
                return $('#payslipattachements-isselfemployed').val() == '1' && $('#payslipattachements-certifiedbankstatementsattached').val() == '0';
            }"],



            [['PayslipOne', 'PayslipTwo', 'Letter', 'CertifiedBankStatements'], 'file', 'maxSize' => 1024 * 1024 * 2],
            [
                ['PayslipOne', 'PayslipTwo', 'Letter', 'CertifiedBankStatements'], 'file', 'extensions' => ['pdf'],
                'wrongExtension' => 'Only PDF files are allowed for {attribute}.',
                'wrongMimeType' => 'Only PDF files are allowed for {attribute}.',
                'skipOnEmpty' => true,
                'mimeTypes' => ['application/pdf']
            ],
        ];
    }

    public function attributeLabels()
    {
        return [
            'docFile' => 'File to Upload',
            'File_Name' => 'File Description',
            'PayslipOne' => 'Most Recent Payslip',
            'PayslipTwo' => 'Previous Month Payslip',
        ];
    }

    public function upload()
    {
        $PayslipOneRandomName = Yii::$app->security->generateRandomString(8);
        $PayslipTwoRandomName = Yii::$app->security->generateRandomString(8);
        $LetterPathRandomName = Yii::$app->security->generateRandomString(8);
        $CertifiedBankStatementsPathRandomName = Yii::$app->security->generateRandomString(8);
        if (is_object($this->PayslipOne)) {
            $this->PayslipOnePath = Yii::getAlias(env('UploadsFolder') . $this->MemberNo . '_' . $this->DocNum . '_' . 'Previous Payslip' . '.' . $this->PayslipOne->extension);
            if (!is_dir(dirname($this->PayslipOnePath))) {
                FileHelper::createDirectory(dirname($this->PayslipOnePath));
            }
        }

        if (is_object($this->PayslipTwo)) {
            $this->PayslipTwoPath = Yii::getAlias(env('UploadsFolder') . $this->MemberNo . '_' . $this->DocNum . '_' . 'Recent Payslip' . '.' . $this->PayslipTwo->extension);
            if (!is_dir(dirname($this->PayslipTwoPath))) {
                FileHelper::createDirectory(dirname($this->PayslipTwoPath));
            }
        }

        if (is_object($this->Letter)) {
            $this->LetterPath = Yii::getAlias(env('UploadsFolder') . $this->MemberNo . '_' . $this->DocNum . '_' . 'Letter' . '.' . $this->Letter->extension);
            if (!is_dir(dirname($this->LetterPath))) {
                FileHelper::createDirectory(dirname($this->LetterPath));
            }
        }

        if (is_object($this->CertifiedBankStatements)) {
            $this->CertifiedBankStatementsPath = Yii::getAlias(env('UploadsFolder') . $this->MemberNo . '_' . $this->DocNum . '_' . 'Statement' . '.'  . $this->CertifiedBankStatements->extension);
            if (!is_dir(dirname($this->CertifiedBankStatementsPath))) {
                FileHelper::createDirectory(dirname($this->CertifiedBankStatementsPath));
            }
        }

        return $this->uploadEverythingToNav();
    }

    function uploadEverythingToNav()
    {
        $PayslipOneResult = $this->uploadPayslipOne();
        if ($PayslipOneResult === true) {
            $uploadPayslipTwoResult = $this->uploadPayslipTwo();
            if ($uploadPayslipTwoResult === true) {
                $uploadLetterResult = $this->uploadLetter();
                if ($uploadLetterResult === true) {
                    $uploadCertifiedBankStatements = $this->uploadCertifiedBankStatements();
                    if ($uploadCertifiedBankStatements === true) {
                        return true;
                    } else {
                        return $uploadCertifiedBankStatements;
                    }
                } else {
                    return $uploadLetterResult;
                }
            } else {
                return $uploadPayslipTwoResult;
            }
        } else {
            return $PayslipOneResult;
        }
    }

    public function uploadPayslipOne()
    {
        if (empty($this->PayslipOne)) { // Don't Upload
            return true;
        }
        if ($this->PayslipOne->saveAs($this->PayslipOnePath)) {
            $FileName = 'PayslipOne';
            $AlreadyExists = $this->IsAlreadyUploaded($FileName);
            if ($AlreadyExists) { //Do An Update
                $this->Key = $AlreadyExists[0]->Key;
                return $this->UpdateDocumentOnNav($FileName, $this->PayslipOnePath);
            }
            //Post to Nav
            return $this->SaveDocumentToNav($FileName, $this->PayslipOnePath);
        }
    }

    public function uploadPayslipTwo()
    {
        if (empty($this->PayslipTwo)) { // Don't Upload
            return true;
        }
        if ($this->PayslipTwo->saveAs($this->PayslipTwoPath)) {
            $FileName = 'PayslipTwo';
            $AlreadyExists = $this->IsAlreadyUploaded($FileName);
            if ($AlreadyExists) { //Do An Update
                $this->Key = $AlreadyExists[0]->Key;
                return $this->UpdateDocumentOnNav($FileName, $this->PayslipTwoPath);
            }
            //Post to Nav
            return $this->SaveDocumentToNav($FileName, $this->PayslipTwoPath);
        }
    }

    public function uploadLetter()
    {
        if (empty($this->Letter)) { // Don't Upload
            return true;
        }
        if ($this->Letter->saveAs($this->LetterPath)) {
            $FileName = 'Letter';
            $AlreadyExists = $this->IsAlreadyUploaded($FileName);
            if ($AlreadyExists) { //Do An Update
                $this->Key = $AlreadyExists[0]->Key;
                return $this->UpdateDocumentOnNav($FileName, $this->LetterPath);
            }
            //Post to Nav
            return $this->SaveDocumentToNav($FileName, $this->LetterPath);
        }
    }

    public function uploadCertifiedBankStatements()
    {
        if (empty($this->CertifiedBankStatements)) { // Don't Upload
            return true;
        }
        if ($this->CertifiedBankStatements->saveAs($this->CertifiedBankStatementsPath)) {
            $FileName = 'CertifiedBankStatements';
            $AlreadyExists = $this->IsAlreadyUploaded($FileName);
            if ($AlreadyExists) { //Do An Update
                $this->Key = $AlreadyExists[0]->Key;
                return $this->UpdateDocumentOnNav($FileName, $this->CertifiedBankStatementsPath);
            }
            //Post to Nav
            return $this->SaveDocumentToNav($FileName, $this->CertifiedBankStatementsPath);
        }
    }

    public function IsAlreadyUploaded($FileName)
    {

        $service = Yii::$app->params['ServiceName']['DocumentUploads'];
        $filter = [
            'Parent_Type' => 'Loan_Application',
            'Document_No' => $FileName,
            'Document_Type' => $this->Type,
            'Parent_No' => $this->DocNum,
        ];
        $result = Yii::$app->navhelper->getData($service, $filter);
        // print '<pre>';
        // print_r($result);
        // exit;

        if (is_array($result)) {
            return $result;
        } else {
            return false;
        }
    }


    public function updateAttachement($docId = false)
    {
        $model = $this;

        $imageId = Yii::$app->security->generateRandomString(8);

        $imagePath = Yii::getAlias(env('UploadsFolder') . $imageId . '.' . $this->docFile->extension);
        // exit($imagePath);
        if ($model->validate()) {
            // Check if directory exists, else create it
            if (!is_dir(dirname($imagePath))) {
                FileHelper::createDirectory(dirname($imagePath));
            }

            if ($this->docFile->saveAs($imagePath)) {
                $model->FilePath = $imagePath;
                $model->DocNum = $model->DocNum;
                // Create Applicant in EDMS
                $CreateApplicantResult = Yii::$app->Mfiles->CreateMember($model->DocNum);

                if ($CreateApplicantResult['Exists'] == true || $CreateApplicantResult['Created'] == true) {
                    $UploadResult = Yii::$app->Mfiles->UpdateApplicantDocument($model);
                    if ($UploadResult['Uploaded'] == false) {
                        Yii::$app->session->setFlash('error', $UploadResult['Message']);
                        return false;
                    }
                }
                //Post to Nav
                $SaveToNavResult = $this->UpdateDocumentOnNav($model, $UploadResult['DocumentUrl']);
            }
        } else {
            // return false;
            print '<Pre>';
            // print_r($model->getErrorSummary());
            exit;
        }
    }

    public function SaveDocumentToNav($documentName, $documentPath)
    {
        $service = Yii::$app->params['ServiceName']['DocumentUploads'];
        $data = [
            'Parent_Type' => 'Loan_Application',
            'Document_No' => $documentName,
            'Document_Type' => $this->Type,
            'Parent_No' => $this->DocNum,
            'URL' => 'file://' . $documentPath,
        ];

        $result = Yii::$app->navhelper->postData($service, $data);


        if (is_string($result)) {
            Yii::$app->session->setFlash('error', $result);
            return $result;
        } else {
            Yii::$app->session->setFlash('success', 'File Uploaded Successfully.');
            //;
            return true;
        }
    }

    public function UpdateDocumentOnNav($documentName, $documentPath)
    {

        $service = Yii::$app->params['ServiceName']['DocumentUploads'];

        $data = [
            'Key' => $this->Key,
            'Parent_Type' => 'Loan_Application',
            'Document_No' => $documentName,
            'Document_Type' => $this->Type,
            'Parent_No' => $this->DocNum,
            'URL' => $documentPath,
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
            'DocNum' => $DocNo
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

    public function read($Key)
    {
        $service = Yii::$app->params['ServiceName']['DocumentUploads'];


        $result = Yii::$app->navhelper->readByKey($service, urldecode(Yii::$app->request->get('Key')));

        /*print '<pre>';
        print_r($result); exit;*/


        $path = $result->URL;
        $imagePath = Yii::getAlias($path);


        if (is_file($imagePath)) {
            $binary = file_get_contents($imagePath);
            $content = chunk_split(base64_encode($binary));
            return $content;
        }
    }


    public function getAttachments($DocNo = '')
    {

        $service = Yii::$app->params['ServiceName']['DocumentUploads'];
        $filter = [
            'Parent_No' => $DocNo,
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

    public function getLoanAttachments()
    {
        $service = Yii::$app->params['ServiceName']['DocumentUploads'];
        $filter = [
            'Parent_No' => $this->DocNum,
        ];
        $result = Yii::$app->navhelper->getData($service, $filter);
        // print '<pre>';
        // print_r($result);
        // exit;
        if (is_array($result)) {
            return $result;
        } else {
            return false;
        }
    }
}
