<?php

namespace app\controllers;

use app\models\MemberApplication_KINs;
use app\models\NextofKinAttachements;
use app\models\MemberApplicationCard;
use Yii;
use yii\filters\AccessControl;
use yii\filters\ContentNegotiator;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\Controller;
use frontend\models\Leave;
use yii\web\Response;
use yii\web\UploadedFile;

class KinController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'user' => 'applicant', // this user object defined in web.php
                'only' => ['create', 'update', 'index', 'read', 'delete', 'getkins'],
                'rules' => [
                    [
                        'actions' => ['create', 'update', 'index', 'read', 'delete', 'getkins'],
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

    public function ApplicantDetails($Key)
    {
        $model = new MemberApplicationCard();
        $service = Yii::$app->params['ServiceName']['MemberApplicationSingle'];
        $memberApplication = Yii::$app->navhelper->readByKey($service, urldecode($Key));
        return $model = $this->loadtomodel($memberApplication, $model);
    }

    public function ApplicantDetailWithDocNum($Docnum)
    {
        $model = new MemberApplicationCard();
        $service = Yii::$app->params['ServiceName']['MemberApplicationSingle'];
        $filter = [
            'Application_No' => $Docnum
        ];
        $memberApplication = Yii::$app->navhelper->getData($service, $filter);
        return $model = $this->loadtomodel($memberApplication[0], $model);
    }

    public function actionIndex($Key)
    {
        $this->layout = 'applicant-main';
        $model = new MemberApplication_KINs();
        $ApplicantData = $this->ApplicantDetails($Key);
        $model->Member_Category = $ApplicantData->Member_Category;
        $model->Key = $ApplicantData->Key;

        $KinAttachmentModel = new NextofKinAttachements();

        $service = Yii::$app->params['ServiceName']['MemberApplication_KINs'];
        // $ApplicantionData = $this->ApplicantDetails($Key);
        $model->Account_No = $ApplicantData->No;
        $model->isNewRecord = true;
        // $attachements = $model->getKinAttachments();

        if (Yii::$app->request->post() && $this->loadpost(Yii::$app->request->post()['MemberApplication_KINs'], $model)) {
            $model->Type = 'Next_of_Kin';
            $model->Account_No = $ApplicantData->No;
            $result = Yii::$app->navhelper->postData($service, $model);
            // echo '<pre>';
            // print_r($result);
            // exit;
            if (is_object($result)) {
                // $model->Kin_Type = $result->Kin_Type;
                // $model->IdentificationDocument = UploadedFile::getInstance($model, 'IdentificationDocument');
                // $model->PassportSizePhoto = UploadedFile::getInstance($model, 'PassportSizePhoto');
                // if ($model->validate()) {
                //     $uploadResult = $model->upload();
                // }
                return $this->redirect(['index', 'Key' => $ApplicantData->Key]);
            } else {
                Yii::$app->session->setFlash('error', $result);
                return $this->redirect(['index', 'Key' => $ApplicantData->Key]);
            }


            // echo '<pre>';
            // print_r($result);
            // exit;


        }


        return $this->render('index', [
            'model' => $model,
            'Applicant' => $ApplicantData,
            'KinAttachmentModel' => $KinAttachmentModel,
            'RequiredAttachements' => [], // $this->getKinAttachements(),
            'attachments' => [],
            'RelationshipTypes'=>ArrayHelper::map($this->RelationshipTypes(), 'Code', 'Name') ,
        ]);
    }

    public function RelationshipTypes(){
        $service = Yii::$app->params['ServiceName']['RelationshipTypes'];
        $res = [];
        $Constituencies = \Yii::$app->navhelper->getData($service);
        foreach ($Constituencies as $Constituency) {
            if (!empty($Constituency->Description))
                $res[] = [
                    'Code' => $Constituency->Description,
                    'Name' => $Constituency->Description
                ];
        }

        return $res;
    }
    public function actionCreate($Key)
    {

        $model = new MemberApplication_KINs();
        $KinAttachmentModel = new NextofKinAttachements();

        $service = Yii::$app->params['ServiceName']['MemberApplication_KINs'];
        $ApplicantionData = $this->ApplicantDetails($Key);
        $model->Source_Code = $ApplicantionData->Application_No;
        $model->isNewRecord = true;
        $attachements = false; //$KinAttachmentModel->getAttachments( $ApplicantionData->Application_No);

        if (Yii::$app->request->post() && $this->loadpost(Yii::$app->request->post()['MemberApplication_KINs'], $model)) {

            $model->IdentificationDocument = UploadedFile::getInstance($model, 'IdentificationDocument');
            $model->PassportSizePhoto = UploadedFile::getInstance($model, 'PassportSizePhoto');
            if ($model->validate()) {
                $uploadResult = $model->upload();
            }

            $result = Yii::$app->navhelper->postData($service, $model);
            // echo '<pre>';
            // print_r($result);
            // exit;

            if (is_object($result)) {
                // Yii::$app->session->setFlash('success', 'Kin Added Successfully', true);
                return $this->redirect(['index', 'Key' => $ApplicantionData->Key]);
            } else {
                Yii::$app->session->setFlash('error', $result);
                return $this->redirect(['index', 'Key' => $ApplicantionData->Key]);
            }
        }

        if (Yii::$app->request->isAjax) {
            return $this->renderAjax('create', [
                'model' => $model,
                'KinAttachmentModel' => $KinAttachmentModel,
                'RequiredAttachements' => [], // $this->getKinAttachements(),
                'MyAttachedDocs' => $attachements
            ]);
        }
    }

    public function getKinAttachements()
    {
        $res = [
            ['Id' => 1, 'Name' => 'Identification Document'],
            ['Id' => 2, 'Name' => 'Passport Size Photo']
        ];
        return $res;
    }


    public function actionUpdate()
    {
        $service = Yii::$app->params['ServiceName']['MemberApplication_KINs'];
        $KinAttachmentModel = new NextofKinAttachements();
        $filter = [
            'Key' => urldecode(Yii::$app->request->get('Key')),
        ];
        $result = Yii::$app->navhelper->readByKey($service, urldecode(Yii::$app->request->get('Key')));
        $ApplicationData = $this->ApplicantDetailWithDocNum($result->Source_Code);
        $model = new MemberApplication_KINs();
        //load nav result to model
        $model->isNewRecord = 0;

        $model = $this->loadtomodel($result, $model);
        $attachements = $model->getKinAttachments();

        if (Yii::$app->request->post() && $this->loadpost(Yii::$app->request->post()['MemberApplication_KINs'], $model)) {
            $model->IdentificationDocument = UploadedFile::getInstance($model, 'IdentificationDocument');
            $model->PassportSizePhoto = UploadedFile::getInstance($model, 'PassportSizePhoto');
            $model->Key = $result->Key;
            if ($model->validate()) {
                $uploadResult = $model->upload();
            }

            $result = Yii::$app->navhelper->updateData($service, $model);
            // echo '<pre>';
            // print_r($result);
            // exit;
            if (is_object($result)) {
                // Yii::$app->session->setFlash('success', 'Kin Added Successfully', true);
                return $this->redirect(['index', 'Key' => $ApplicationData->Key]);
            } else {
                Yii::$app->session->setFlash('error', $result);
                return $this->redirect(['index', 'Key' => $ApplicationData->Key]);
            }
        }

        if (Yii::$app->request->isAjax) {
            return $this->renderAjax('updatemodal', [
                'model' => $model,
                'KinAttachmentModel' => $KinAttachmentModel,
                'RequiredAttachements' => $this->getKinAttachements(),
                'MyAttachedDocs' => $attachements
            ]);
        }
    }

    public function actionRead($Key)
    {
        $model = new NextofKinAttachements();
        $content = $model->read($Key);
        //    print '<pre>';
        //    print_r($content); exit;
        return $this->render('read', ['content' => $content]);
    }

    public function actionDelete()
    {
        $service = Yii::$app->params['ServiceName']['MemberApplication_KINs'];
        $result = Yii::$app->navhelper->deleteData($service, urldecode(Yii::$app->request->get('Key')));
        if (!is_string($result)) {
            return $this->redirect(Yii::$app->request->referrer);
        } else {
            Yii::$app->session->setFlash('error', 'Kin Deleted Successfully: ' . $result, true);
            return $this->redirect(Yii::$app->request->referrer);
        }
    }

    public function actionDeleteAttachment()
    {
        $service = Yii::$app->params['ServiceName']['DocumentUploads'];
        $result = Yii::$app->navhelper->deleteData($service, urldecode(Yii::$app->request->get('Key')));
        if (!is_string($result)) {
            return $this->redirect(Yii::$app->request->referrer);
        } else {
            return $this->redirect(Yii::$app->request->referrer);
        }
    }

    public function actionView($ApplicationNo)
    {
        $service = Yii::$app->params['ServiceName']['leaveApplicationCard'];
        $leaveTypes = $this->getLeaveTypes();
        $employees = $this->getEmployees();

        $filter = [
            'Application_No' => $ApplicationNo
        ];

        $leave = Yii::$app->navhelper->getData($service, $filter);

        //load nav result to model
        $leaveModel = new MemberApplication_KINs();
        $model = $this->loadtomodel($leave[0], $leaveModel);


        return $this->render('view', [
            'model' => $model,
            'leaveTypes' => ArrayHelper::map($leaveTypes, 'Code', 'Description'),
            'relievers' => ArrayHelper::map($employees, 'No', 'Full_Name'),
        ]);
    }



    public function actionGetkins($AppNo)
    {
        $service = Yii::$app->params['ServiceName']['MemberApplication_KINs'];
        $filter = [
            'Source_Code' => urldecode($AppNo),
        ];
        $kins = Yii::$app->navhelper->getData($service, $filter);
        $ApplicantData = $this->ApplicantDetailWithDocNum($AppNo);
        // echo '<pre>';
        // print_r($kins);
        // exit;


        $result = [];
        $count = 0;

        if (!is_object($kins)) {
            foreach ($kins as $kin) {

                if (empty($kin->Name)) { //Useless KIn this One
                    continue;
                }
                ++$count;
                $link = $updateLink =  '';
                $updateLink = Html::a('Edit Kin', ['update', 'Key' => urlencode($kin->Key)], ['class' => 'update btn btn-info btn-md']);
                $link = Html::a('Remove Kin', ['delete', 'Key' => urlencode($kin->Key)], ['class' => 'btn btn-danger btn-md']);

                $result['data'][] = [
                    'index' => $count,
                    'Type' => $kin->Kin_Type,
                    'Name' => !empty($kin->Name) ? $kin->Name : '',
                    'DOB' => !empty($kin->Date_of_Birth) ? $kin->Date_of_Birth : '',
                    'Phone_No' => !empty($kin->Phone_No) ? $kin->Phone_No : '',
                    'Allocation' => !empty($kin->Allocation) ? $kin->Allocation : '',
                    'Update_Action' => $updateLink,
                    // 'Remove' => $link
                ];
            }
        }



        return $result;
    }

    public function getReligion()
    {
        $service = Yii::$app->params['ServiceName']['Religion'];
        $filter = [
            'Type' => 'Religion'
        ];
        $religion = \Yii::$app->navhelper->getData($service, $filter);
        return $religion;
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
