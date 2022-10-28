<?php

namespace app\models;

use Yii;
use yii\base\Model;


class GuarantorReplacements extends Model
{
    public $Key;
    public $Type;
    public $Replace_With;
    public $Guarantor_Names;
    public $Guarantor_Value;
    public $Max_Guarantee;
    public $Document_No;
    public $docFile;
    public $FileName;
    public $FilePath;
    public $isNewRecord;
    public $Member_No;

    public $Guarantor_No;
    public $Replace_With_Name;
    public $Loan_Balance;
    public $Amount;
    public $Status;
    public $Outstanding_Guarantee;

    public function rules()
    {
        return [
            [['Replace_With', 'Amount'], 'required'],
            [['Amount'], 'number', 'min' => 1],
            ['Replace_With', 'compare', 'compareValue' => Yii::$app->user->identity->{'National ID No'}, 'operator' => '!='],
            ['docFile', 'required', 'when' => function ($model) {
                return $model->Type == 'Security' && $model->isNewRecord == true;
            }],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'Replace_With' => 'Replacement (Type the ID No of the new guarantor)',
            'docFile' => 'Security Attachment',

        ];
    }

    public function upload($docId = false)
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
                $model->Document_No = $model->Document_No;
                // Create Applicant in EDMS
                //    $CreateApplicantResult = Yii::$app->Mfiles->CreateMember($model->Loan_No);

                // if($CreateApplicantResult['Exists']== true || $CreateApplicantResult['Created']== true){
                //      $UploadResult = Yii::$app->Mfiles->UploadApplicantDocument($model);                  
                //      if($UploadResult['Uploaded'] == false){
                //               Yii::$app->session->setFlash('error',$UploadResult['Message']);
                //               return false;
                //      }
                // }
                //Post to Nav
                return $this->SaveDocumentToNav($model, 'Not Yet Intergrated');
            }
        } else {
            // return false;
            print '<Pre>';
            // print_r($model->getErrorSummary());
            exit;
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

    public function read($DocNo)
    {
        $service = Yii::$app->params['ServiceName']['LoanApplicationAttachements'];
        $filter = [
            'DocNum' => $DocNo
        ];

        $result = Yii::$app->navhelper->getData($service, $filter);

        /*print '<pre>';
        print_r($result); exit;*/


        $path = $result[0]->FilePath;
        $imagePath = Yii::getAlias('C:\\inetpub\\wwwroot\\SaccoMembersPortal\\web\\attachements\\' . $path);


        if (is_file($imagePath)) {
            $binary = file_get_contents($imagePath);
            $content = chunk_split(base64_encode($binary));
            return $content;
        }
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

    public function SaveDocumentToNav($model, $MfilesUrl)
    {
        $service = Yii::$app->params['ServiceName']['LoanApplicationAttachements'];
        $model->FilePath = basename($model->FilePath);
        $model->FileName = $model->FileName;
        // exit('h');
        // $model->Type = $model->Type;
        // $model->Member_Category = $model->Member_Category;


        $data = [
            'FilePath' => basename($model->FilePath),
            'MfilesUrl' => $MfilesUrl,
            'FileName' => $model->FileName,
            // 'Type'=>$model->Type,
            'DocNum' => $model->Document_No,
        ];

        $result = Yii::$app->navhelper->postData($service, $data);
        // echo '<pre>';
        // print_r($result);
        // exit;       
        if (is_string($result)) {
            Yii::$app->session->setFlash('error', $result);
            return false;
        } else {
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
}
