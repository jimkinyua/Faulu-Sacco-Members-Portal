<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\helpers\FileHelper;
use yii\web\UploadedFile;
use borales\extensions\phoneInput\PhoneInputValidator;


class MemberEditingKINs extends Model
{
    public $Key;
    public $Source_Code;
    public $Kin_Type;
    public $KIN_ID;
    public $Name;
    public $Date_of_Birth;
    public $Phone_No;
    public $Allocation;
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
            $this->PassportSizePhotoPath = Yii::getAlias('C:\\inetpub\\wwwroot\\SaccoMembersPortal\\web\\attachements\\' . $PassportSizePhotoName . '.' . $this->PassportSizePhoto->extension);
            if (!is_dir(dirname($this->PassportSizePhotoPath))) {
                FileHelper::createDirectory(dirname($this->PassportSizePhotoPath));
            }
        }


        if (is_object($this->IdentificationDocument)) {
            $this->IdentificationDocumentPath = Yii::getAlias('C:\\inetpub\\wwwroot\\SaccoMembersPortal\\web\\attachements\\' . $IdentificationDocumentName . '.' . $this->IdentificationDocument->extension);
            if (!is_dir(dirname($this->IdentificationDocumentPath))) {
                FileHelper::createDirectory(dirname($this->IdentificationDocumentPath));
            }
        }

        return $this->uploadEverythingToNav();
    }
    function uploadEverythingToNav()
    {
        $uploadIdentificationDocumentResult = $this->uploadIdentificationDocument();
        if ($uploadIdentificationDocumentResult === true) {
            $uplodIdentification = $this->uploadKinPassportPhoto();
            if ($uplodIdentification === true) {
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
            'URL' => $Url,
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
            'URL' => $documentPath,
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
        $service = Yii::$app->params['ServiceName']['DocumentUploads'];
        $filter = [
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
}
