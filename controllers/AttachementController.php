<?php

namespace app\controllers;

use app\models\Attachement;
use app\models\MemberApplicationCard;
use Yii;
use yii\filters\AccessControl;
use yii\filters\ContentNegotiator;
use yii\filters\VerbFilter;
use yii\helpers\Html;
use yii\web\Controller;
use yii\web\Response;
use yii\web\UploadedFile;


class AttachementController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'user' => 'applicant', // this user object defined in web.php
                'only' => ['create', 'update', 'index', 'read', 'delete', 'list'],
                'rules' => [
                    [
                        'actions' => ['update', 'index', 'read', 'delete', 'create', 'list'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
            'contentNegotiator' => [
                'class' => ContentNegotiator::class,
                'only' => ['getkins'],
                'formatParam' => '_format',
                'formats' => [
                    'application/json' => Response::FORMAT_JSON,
                    //'application/xml' => Response::FORMAT_XML,
                ],
            ]
        ];
    }



    public function actionIndex($Key)
    {
        $this->layout = 'applicant-main';
        $model = new Attachement();
        $service = Yii::$app->params['ServiceName']['DocumentUploads'];
        $ApplicantData = $this->ApplicantDetails($Key);
        $filter = [
            // 'Docnum' => $ApplicantData->Application_No
        ];

        $memberAttachements = Yii::$app->navhelper->getData($service, $filter);
        $model->Docnum = $ApplicantData->Application_No;
        $model->Type = 'ApplicantDocuments';
        $model->Member_Category = $ApplicantData->Member_Category;

        if (Yii::$app->request->isAjax) {
            // $model->File_Name = Yii::$app->request->post()['Essfile']['File_Name'];
            // $model->attachmentfile = UploadedFile::getInstance($model, 'docFile');
            foreach ($this->getRequiredAttachements() as $requiredDoc) {
                $parameterName = str_replace(' ', '_', $requiredDoc['Name']);
                $parameterName = str_replace('.', '_', $parameterName);
                $model->attachmentfile = UploadedFile::getInstancesByName($parameterName);

                foreach ($model->attachmentfile as $File) {
                    $model->docFile = $File;
                    $model->File_Name =  $requiredDoc['Name'];
                    $model->AttachementID = $requiredDoc['Id'];
                    $model->upload();

                    if ($model->getErrors()) {
                        $result = [];
                        foreach ($model->getErrors() as $attribute => $errors) {
                            $result[Html::getInputId($model, $attribute)] = $errors;
                        }
                        return $this->asJson(['error' => $result]);
                    }
                }
            }

            // file is uploaded successfully
            return $this->redirect(['/referral', 'Key' => $ApplicantData->Key]);
        }


        $this->loadtomodel($memberAttachements, $model);
        return $this->render('index', [
            'model' => $model,
            'Applicant' => $ApplicantData,
            'RequiredAttachements' => $this->getRequiredAttachements(),
            'MyAttachedDocs' => $ApplicantData->getAttachments(),

        ]);
    }

    public function getRequiredAttachements()
    {
        return  $res = [
            ['Id' => 1, 'Name' => 'Passport Photo', 'required' => true, 'ExtraDesc' => '', 'extensions' => '.jpg,.png'],
            ['Id' => 2, 'Name' => 'Signature', 'required' => true, 'ExtraDesc' => '', 'extensions' => '.jpg,.png'],
            ['Id' => 3, 'Name' => 'National ID', 'required' => true, 'ExtraDesc' => '', 'extensions' => '.jpg,.png'],
            ['Id' => 4, 'Name' => 'KRA Pin', 'required' => true, 'ExtraDesc' => '', 'extensions' => '.pdf'],
            // ['Id' => 5, 'Name' => 'Contract Letter', 'required' => false, 'ExtraDesc' => '(Individual Members)', 'extensions' => '.pdf'],
            // ['Id' => 6, 'Name' => 'Marriage Cerrtificate', 'required' => false, 'ExtraDesc' => '(If Applicable)', 'extensions' => '.pdf'],
            // ['Id' => 7, 'Name' => 'Passport Document', 'required' => false, 'ExtraDesc' => '(If Applicable)', 'extensions' => '.pdf'],
            // ['Id' => 8, 'Name' => 'Current Payslip', 'required' => false, 'ExtraDesc' => '(Individual Members)', 'extensions' => '.pdf'],

        ];
    }

    public function actionUpdate()
    {
        $this->layout = 'applicant-main';
        $service = Yii::$app->params['ServiceName']['ESS_Files'];
        $filter = [
            'Key' => urldecode(Yii::$app->request->get('Key')),
        ];
        $result = Yii::$app->navhelper->readByKey($service, urldecode(Yii::$app->request->get('Key')));
        $ApplicationData = $this->ApplicantDetailWithDocNum($result->Docnum);


        $model = new Attachement();
        //load nav result to model
        $model = $this->loadtomodel($result, $model);
        $model->isNewRecord = false;

        if (Yii::$app->request->isPost) {
            $model->File_Name = Yii::$app->request->post()['Attachement']['File_Name'];
            $model->docFile = UploadedFile::getInstance($model, 'docFile');
            if ($model->updateAttachement()) {
                // file is uploaded successfully
                return $this->redirect(['index', 'Key' => $ApplicationData->Key]);
            } else {
                return $this->redirect(['index', 'Key' => $ApplicationData->Key]);
            }
        }

        if (Yii::$app->request->isAjax) {
            return $this->renderAjax('update', [
                'model' => $model,
                'RequiredDocuments' => $this->getRequiredDocuments($model->Member_Category)
            ]);
        }
    }

    public function ApplicantDetails($Key)
    {
        $model = new MemberApplicationCard();
        $service = Yii::$app->params['ServiceName']['MemberApplicationCard'];
        $memberApplication = Yii::$app->navhelper->readByKey($service, urldecode($Key));
        return $model = $this->loadtomodel($memberApplication, $model);
    }

    public function ApplicantDetailWithDocNum($Docnum)
    {
        $model = new MemberApplicationCard();
        $service = Yii::$app->params['ServiceName']['MemberApplicationCard'];
        $filter = [
            'Application_No' => $Docnum
        ];
        $memberApplication = Yii::$app->navhelper->getData($service, $filter);
        return $model = $this->loadtomodel($memberApplication[0], $model);
    }



    public function getRequiredDocuments($Category)
    {
        $service = Yii::$app->params['ServiceName']['EssDocumentsList'];

        $res = [
            ['Code' => 1, 'Name' => 'Identification Document'],
            ['Code' => 2, 'Name' => 'Passport Size Photo']
        ];
        return $res;
    }


    public function actionCreate($Key)
    {
        $this->layout = 'applicant-main';
        $model = new Attachement();
        $model->Type = 'MemberApplication';
        $ApplicantionData = $this->ApplicantDetails($Key);
        $model->Docnum = $ApplicantionData->Application_No;
        $model->Member_Category = $ApplicantionData->Member_Category;
        $model->isNewRecord = true;


        if (Yii::$app->request->isPost) {
            $model->File_Name = Yii::$app->request->post()['Attachement']['File_Name'];
            $model->docFile = UploadedFile::getInstance($model, 'docFile');
            if ($model->upload($ApplicantionData)) {
                // file is uploaded successfully
                return $this->redirect(['index', 'Key' => $ApplicantionData->Key]);
            } else {
                return $this->redirect(['index', 'Key' => $ApplicantionData->Key]);
            }
        }

        if (Yii::$app->request->isAjax) {
            return $this->renderAjax('create', [
                'model' => $model,
                'RequiredDocuments' => $this->getRequiredDocuments($model->Member_Category)
            ]);
        }
    }


    public function actionRead($Key)
    {
        $this->layout = 'applicant-main';
        $service = Yii::$app->params['ServiceName']['DocumentUploads'];
        $model = new Attachement();
        $result = Yii::$app->navhelper->readByKey($service, urldecode(Yii::$app->request->get('Key')));
        if (is_object($result)) {
            $content = $model->read($result->URL);
            return $this->render('read', ['content' => $content]);
        } else {
            Yii::$app->session->setFlash('error', $result);
            return Yii::$app->request->referrer;
        }
    }

    public function actionDelete()
    {
        $service = Yii::$app->params['ServiceName']['DocumentUploads'];
        $result = Yii::$app->navhelper->readByKey($service, urldecode(Yii::$app->request->get('Key')));
        $ApplicationData = $this->ApplicantDetailWithDocNum($result->Docnum);
        $result = Yii::$app->navhelper->deleteData($service, Yii::$app->request->get('Key'));
        if (!is_string($result)) {
            Yii::$app->session->setFlash('success', 'Document Deleted Successfully.');
            return $this->redirect(['index', 'Key' => $ApplicationData->Key]);
        } else {
            Yii::$app->session->setFlash('error', $result);
            return $this->redirect(['index', 'Key' => $ApplicationData->Key]);
        }
    }

    public function GetApplicationDetails($ApplicationNo)
    {
        $model = new MemberApplicationCard();
        $filter = [
            'Application_No' => $ApplicationNo
        ];
        $service = Yii::$app->params['ServiceName']['MemberApplicationCard'];
        $result = Yii::$app->navhelper->getData($service, $filter);

        return $model = $this->loadtomodel($result[0], $model);
    }


    public function actionList($AppNo)
    {
        $model = new Attachement();
        $results = $model->getAttachments($AppNo);

        $result = [];
        if (is_array($results)) {
            foreach ($results as $item) {

                if (empty($item->File_Name)) {
                    continue;
                }

                $data = $this->GetApplicationDetails($item->Docnum);

                $link = Html::a('View Attachement', ['read', 'No' => $item->Line_No], ['title' => 'Read File', 'class' => 'btn btn-primary btn-md']);
                // $deleteLink = Html::a('Delete Attachement',['delete','Key'=> $item->Key],['class'=>'btn btn-danger btn-md']);

                if ($data->Portal_Status == 'New') {
                    $editLink = Html::a('Edit Attachement', ['update', 'Key' => $item->Key], ['class' => 'btn btn-success btn-md update']);
                    $link = Html::a('View Attachement', ['read', 'No' => $item->Line_No], ['title' => 'Read File', 'class' => 'btn btn-primary btn-md']);
                    $deleteLink = Html::a('Delete Attachement', ['delete', 'Key' => $item->Key], ['class' => 'btn btn-danger btn-md']);
                } else {
                    $link = Html::a('View Attachement', ['read', 'No' => $item->Line_No], ['title' => 'Read File', 'class' => 'btn btn-primary btn-md']);
                    $deleteLink = '';
                    $editLink = '';
                }

                $docLink = Html::a($item->File_Name, ['read', 'No' => $item->Line_No], ['class' => 'btn btn-success btn-xs', 'target' => '_blank']);


                $result['data'][] = [
                    'Key' => $item->Key,
                    'File_Name' => $item->File_Name,
                    'view' => $link . ' ' . $editLink
                ];
            }
        }


        Yii::$app->response->format = Response::FORMAT_JSON;

        return $result;
    }

    public function loadtomodel($obj, $model)
    {

        if (!is_object($obj)) {
            return false;
        }
        $modeldata = (get_object_vars($obj));
        foreach ($modeldata as $key => $val) {
            if (is_object($val)) continue;
            $model->$key = $val;
        }

        return $model;
    }

    public function loadpost($post, $model)
    { // load model with form data


        $modeldata = (get_object_vars($model));

        foreach ($post as $key => $val) {

            $model->$key = $val;
        }

        return $model;
    }
}
