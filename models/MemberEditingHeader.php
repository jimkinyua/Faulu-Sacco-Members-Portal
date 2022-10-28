<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\helpers\FileHelper;

class MemberEditingHeader extends Model
{
    public $Key;
    public $Document_No;
    public $Protected_Account;
    public $Account_Owner;
    public $Member_No;
    public $Update_KINS;
    public $First_Name;
    public $Middle_Name;
    public $Last_Name;
    public $Full_Name;
    public $Mobile_Transacting_No;
    public $National_ID_No;
    public $Date_of_Birth;
    public $Occupation;
    public $Type_of_Residence;
    public $Marital_Status;
    public $Gender;
    public $Employer_Code;
    public $Station_Code;
    public $Designation;
    public $Payroll_No;
    public $Group_Name;
    public $Group_No;
    public $Certificate_of_Incoop;
    public $Date_of_Registration;
    public $Certificate_Expiry;
    public $_x0026_KRA_PIN;
    public $_x0026_E_Mail_Address;
    public $_x0026_Address;
    public $_x0026_County;
    public $_x0026_Sub_County;
    public $Mobile_Phone_No;
    public $Alt_Phone_No;
    public $Town_of_Residence;
    public $Estate_of_Residence;
    public $Portal_Status;
    public $Type_of_Change;
    public $Change_Type;
    public $Portal_Status95339;

    public $PassPortPhoto;
    public $Signature;
    public $SignatureFilePath;
    public $PassPortPhotoFilePath;
    public function rules()
    {
        return [
            [['Type_of_Change'], 'required'],
            [['_x0026_E_Mail_Address'], 'email'],
            [['PassPortPhoto', 'Signature'], 'file'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            '_x0026_E_Mail_Address' => 'E-Mail',
        ];
    }

    public function upload()
    {
        $model = $this;

        $PassPortPhotoRandomName = Yii::$app->security->generateRandomString(8);
        $SignatureRandomName = Yii::$app->security->generateRandomString(8);

        $PassPortPhotoPath = Yii::getAlias(env('UploadsFolder') . $PassPortPhotoRandomName . '.' . $this->PassPortPhoto->extension);
        $SignaturePath = Yii::getAlias(env('UploadsFolder') . $SignatureRandomName . '.' . $this->Signature->extension);

        // exit($SignaturePath);
        // if ($model->validate()) {
        // Check if directory exists, else create it
        if (!is_dir(dirname($PassPortPhotoPath))) {
            FileHelper::createDirectory(dirname($PassPortPhotoPath));
        }
        if (!is_dir(dirname($SignaturePath))) {
            FileHelper::createDirectory(dirname($SignaturePath));
        }

        if ($this->PassPortPhoto->saveAs($PassPortPhotoPath)) {
            $model->PassPortPhotoFilePath = $PassPortPhotoPath;
            $this->SetMemberImage(0);
        }
        if ($this->Signature->saveAs($SignaturePath)) {
            $model->SignatureFilePath = $SignaturePath;
            $this->SetSignature(2);
        }
        // } else {
        // return  $model->getErrors();
        // }
    }

    public function SetMemberImage()
    {

        $service = Yii::$app->params['ServiceName']['PortalIntegrations'];
        $data = [
            'applicationNo' => $this->Document_No,
            'imagePath' => $this->PassPortPhotoFilePath,
            'imageType' => 0,
        ];
        $res = Yii::$app->navhelper->PortalReports($service, $data, 'SetChangeRequestImage');
        return true;
    }

    public function SetSignature()
    {
        $service = Yii::$app->params['ServiceName']['PortalIntegrations'];
        $data = [
            'applicationNo' => $this->Document_No,
            'imagePath' => $this->SignatureFilePath,
            'imageType' => 3,
        ];

        $res = Yii::$app->navhelper->PortalReports($service, $data, 'SetChangeRequestImage');

        return true;
    }
}
