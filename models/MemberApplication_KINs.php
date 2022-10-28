<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\helpers\FileHelper;
use yii\web\UploadedFile;
use borales\extensions\phoneInput\PhoneInputValidator;


class MemberApplication_KINs extends Model
{
    public $Key;
    public $Name;
    public $Relationship;
    public $Beneficiary;
    public $Specify_If_Others;
    public $Date_of_Birth;
    public $ID_No;
    public $Allocation;
    public $Address;
    public $Telephone;
    public $Email;
    public $Fax;
    public $BBF_Entitlement_Code;
    public $BBF_Entitlement;
    public $Account_No;

    public $Member_Category;

    public $docFile;
    public $FileName;
    public $isNewRecord;
    public $FilePath;
    public $LocalPath;
    public $mfilesURL;
    public $Type;

    public $uploadFilesArray;

    public $IdentificationDocument;
    public $PassportSizePhoto;

    public $IdentificationDocumentPath;
    public $PassportSizePhotoPath;


    public function rules()
    {
        return [

            [['Kin_Type', 'Name', 'Allocation',  'Date_of_Birth', 'KIN_ID'], 'required'],
            [['Phone_No'], PhoneInputValidator::className()],
            ['Allocation', 'number', 'max' => 100],

            [['IdentificationDocument', 'PassportSizePhoto',], 'file', 'maxSize' => 1024 * 1024 * 2],

            [
                ['PassportSizePhoto', 'IdentificationDocument'], 'file', 'extensions' => ['jpg'],
                'wrongMimeType' => 'Only PNG and JPG files are allowed for {attribute}.',

            ],

            [['IdentificationDocument', 'PassportSizePhoto'], 'required', 'when' => function ($model) {
                return  $model->isNewRecord == 1;
            }, 'whenClient' => "function (attribute, value) {
                return $('#memberapplication_kins-isnewrecord').val() == '1';
            }"],

        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'DOB' => 'Date of Birth',
            'Type' => 'Relationship',
            'KIN_ID' => 'ID or Birth Certificate incase of a minor'
        ];
    }

    public function upload()
    {

        $IdentificationDocumentName = Yii::$app->security->generateRandomString(8);
        $PassportSizePhotoName = Yii::$app->security->generateRandomString(8);


        if (is_object($this->PassportSizePhoto)) {
            $this->PassportSizePhotoPath = env('UploadsFolder') . $PassportSizePhotoName . '.' . $this->PassportSizePhoto->extension;
            if (!is_dir(dirname($this->PassportSizePhotoPath))) {
                FileHelper::createDirectory(dirname($this->PassportSizePhotoPath));
            }
        }


        if (is_object($this->IdentificationDocument)) {
            $this->IdentificationDocumentPath = env('UploadsFolder') . $IdentificationDocumentName . '.' . $this->IdentificationDocument->extension;
            if (!is_dir(dirname($this->IdentificationDocumentPath))) {
                FileHelper::createDirectory(dirname($this->IdentificationDocumentPath));
            }
        }

        return $this->uploadEverythingToNav();
    }

    public function SetMemberImage($imageType, $Path)
    {

        $service = Yii::$app->params['ServiceName']['PortalIntegrations'];

        $data = [
            'sourceCode' => $this->Source_Code,
            'imagePath' => $Path,
            'imageType' => $imageType,
            'kinType' => $this->Kin_Type,
            'kINID' => $this->KIN_ID,
            'responseCode' => '',
            'responseMessage' => ''
        ];
        // echo '<pre>';
        // print_r($data);
        // exit;

        return Yii::$app->navhelper->PortalReports($service, $data, 'SetMemberKINImage');

        return true;
    }

    function uploadEverythingToNav()
    {
        $uploadIdentificationDocumentResult = $this->uploadIdentificationDocument();
        if ($uploadIdentificationDocumentResult === true) {
            //Set the Image
            $this->SetMemberImage(0, $this->IdentificationDocumentPath);
            // exit;
            $uplodIdentification = $this->uploadKinPassportPhoto();
            if ($uplodIdentification === true) {
                $this->SetMemberImage(1, $this->PassportSizePhotoPath);
                return true; //Terminate
            } else {
                return $uplodIdentification;
            }
        } else {
            return $uploadIdentificationDocumentResult;
        }
        return true;
    }

    public function uploadIdentificationDocument()
    {
        if (empty($this->IdentificationDocument)) { // Don't Upload
            return true;
        }
        if ($this->IdentificationDocument->saveAs($this->IdentificationDocumentPath)) {
            $FileName = 'IdentificationDocument';
            $this->Type = 'Kin Identification Document';
            $AlreadyExists = $this->IsAlreadyUploaded($FileName);
            if ($AlreadyExists) { //Do An Update
                $this->Key = $AlreadyExists[0]->Key;
                return $this->UpdateDocumentOnNav($FileName, $this->IdentificationDocumentPath);
            }
            //Post to Nav
            return $this->SaveDocumentToNav($FileName, $this->IdentificationDocumentPath);
        }
    }

    public function uploadKinPassportPhoto()
    {
        if (empty($this->PassportSizePhoto)) { // Don't Upload
            return true;
        }
        if ($this->PassportSizePhoto->saveAs($this->PassportSizePhotoPath)) {
            $FileName = 'PassportSizePhoto';
            $this->Type = 'Kin Passport Photo';
            $AlreadyExists = $this->IsAlreadyUploaded($FileName);
            if ($AlreadyExists) { //Do An Update
                $this->Key = $AlreadyExists[0]->Key;
                return $this->UpdateDocumentOnNav($FileName, $this->PassportSizePhotoPath);
            }
            //Post to Nav
            return $this->SaveDocumentToNav($FileName, $this->PassportSizePhotoPath);
        }
    }

    public function UpdateDocumentOnNav($FileName, $Url)
    {

        $service = Yii::$app->params['ServiceName']['DocumentUploads'];
        $result = Yii::$app->navhelper->readByKey($service, $this->Key);
        $data = [
            'Key' => $result->Key,
            'Parent_Type' => 'Member_Application',
            'Document_No' => $FileName,
            'Document_Type' => $this->Type,
            'Parent_No' => $this->Source_Code,
            'URL' => 'file://'.$Url,
        ];

        $Updateresult = Yii::$app->navhelper->updateData($service, $data);

        if (is_string($Updateresult)) {
            Yii::$app->session->setFlash('error', $Updateresult);
            return false;
        } else {
            // Yii::$app->session->setFlash('success', 'File Uploaded Successfully.');
            //;
            return true;
        }
    }

    public function IsAlreadyUploaded($FileName)
    {

        $service = Yii::$app->params['ServiceName']['DocumentUploads'];
        $filter = [
            'Parent_Type' => 'Member_Application',
            'Document_No' => $FileName,
            'Document_Type' => $this->Type,
            'Parent_No' => $this->Source_Code,
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

    public function SaveDocumentToNav($documentName, $documentPath)
    {
        $service = Yii::$app->params['ServiceName']['DocumentUploads'];
        $data = [
            'Parent_Type' => 'Member_Application',
            'Document_No' => $documentName,
            'Document_Type' => $this->Type,
            'Parent_No' => $this->Source_Code,
            'URL' => 'file://'.$documentPath,
        ];

        $result = Yii::$app->navhelper->postData($service, $data);

        if (is_string($result)) {
            Yii::$app->session->setFlash('error', $result);
            return $result;
        } else {
            // Yii::$app->session->setFlash('success', 'File Uploaded Successfully.');
            //;
            return true;
        }
    }

    public function getKinAttachments()
    {
        // $service = Yii::$app->params['ServiceName']['DocumentUploads'];
        // $filter = [
        //     'Parent_No' => $this->Source_Code,
        // ];
        // $result = Yii::$app->navhelper->getData($service, $filter);
        // // print '<pre>';
        // // print_r($result);
        // // exit;
        // if (is_array($result)) {
        //     return $result;
        // } else {
            return false;
        // }
    }
}
