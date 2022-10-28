<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\helpers\FileHelper;


class MemberWithdrawalCard extends Model
{
    public $Key;
    public $Document_No;
    public $Posting_Date;
    public $Member_No;
    public $Member_Name;
    public $Instant;
    public $Charge_Code;
    public $Withdrawal_Type;
    public $Withdrawal_Reason;
    public $Total_Assets;
    public $Liabilities;
    public $Guarantees;
    public $Accrued_Interest;
    public $Net_Amount;

    public $uploadFilesArray;
    public $docFile;
    public $FileName;
    public $FilePath;

    public function rules()
    {
        return [

            [['Withdrawal_Reason', 'Withdrawal_Type', 'Instant'], 'required'],
            // ['Quoted_Amount', 'number']
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'Instant' => 'Would You Like Your Exit Application To Be Processed Much Faster? (Attracts a Fee)'
        ];
    }


    public function upload()
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
                $model->FilePath = $imagePath;
                $AlreadyExists = $this->IsAlreadyUploaded();
                if ($AlreadyExists) { //Do An Update
                    $model->Key = $AlreadyExists[0]->Key;
                    $UploadToEDMSResult = Yii::$app->Mfiles->UpdateBusinessDocumentToEDMS($model, $AlreadyExists);

                    if ($UploadToEDMSResult['Uploaded'] == false) {
                        Yii::$app->session->setFlash('error', $UploadToEDMSResult['Message']);
                        return false;
                    }

                    return $this->UpdateDocumentOnNav($model, $UploadToEDMSResult['DocumentUrl']);
                }

                // Create Applicant in EDMS
                $UploadResult = Yii::$app->Mfiles->UploadBusinessDocumentToEDMS($model);
                if ($UploadResult['Uploaded'] == false) {
                    Yii::$app->session->setFlash('error', $UploadResult['Message']);
                    return false;
                }
                //Post to Nav
                return $this->SaveDocumentToNav($model, $UploadResult['DocumentUrl']);
            }
        } else {
            // return false;
            print '<Pre>';
            print_r($model->getErrors());
            exit;
        }
    }

    public function IsAlreadyUploaded()
    {

        $service = Yii::$app->params['ServiceName']['ESS_Files'];
        $filter = [
            'Docnum' => $this->Document_No,
            'Type' => 'Member Exit Application',
            'File_Name' => $this->FileName,
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
}
