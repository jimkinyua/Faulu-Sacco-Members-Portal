<?php

namespace app\controllers;

use app\models\MemberApplication_KINs;
use app\models\MemberEditingHeader;
use app\models\MemberEditingAttachments;
use app\models\MemberEditingKINs;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\filters\ContentNegotiator;
use Yii;
use yii\helpers\Html;
use yii\web\UploadedFile;
use yii\helpers\ArrayHelper;

class MemberChangeRequestController extends \yii\web\Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => [
                    'getloans', 'create', 'index',
                    'approved', 'send-for-approval',
                    'get-change-request', 'read',
                    'update',
                ],
                'rules' => [
                    [
                        'actions' => [
                            'getloans', 'create', 'index',
                            'approved', 'send-for-approval',
                            'get-change-request', 'read',
                            'update',
                        ],
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
                'only' => ['get-change-request'],
                'formatParam' => '_format',
                'formats' => [
                    'application/json' => Response::FORMAT_JSON,
                    //'application/xml' => Response::FORMAT_XML,
                ],
            ]
        ];
    }

    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionApproved()
    {
        return $this->render('approved');
    }

    public function actionSendForApproval($No)
    {
        $service = Yii::$app->params['ServiceName']['PortalFactory'];

        $data = [
            'applicationNo' => $No,
        ];


        // $result = Yii::$app->navhelper->PortalWorkFlows($service,$data,'IanSubmitMemberEditing');
        // Yii::$app->recruitment->printrr($result);

        // if(!is_string($result)){
        Yii::$app->session->setFlash('success', 'Change Request Sent for Approval Successfully.', true);
        //  return $this->redirect(['index']);
        // }else{

        // Yii::$app->session->setFlash('error',  $result);
        // return $this->redirect(['view','No' => $No]);
        return $this->redirect(['index']);

        // }
    }

    public function PaymentSchedule($No)
    {
        $service = Yii::$app->params['ServiceName']['PortalFactory'];

        $data = [
            'loanNo' => $No,
            'sendMail' => 1,
            'approvalUrl' => '',
        ];

        Yii::$app->navhelper->PortalWorkFlows($service, $data, 'LoanPaymentSchedule');
        return true;
    }

    public function actionLoanAppraisal()
    {

        $service = Yii::$app->params['ServiceName']['PortalReports'];

        //Yii::$app->recruitment->printrr(ArrayHelper::map($payrollperiods,'Date_Opened','desc'));
        if (Yii::$app->request->post() && Yii::$app->request->post('payperiods')) {
            //Yii::$app->recruitment->printrr(Yii::$app->request->post('payperiods'));
            $data = [
                'selectedPeriod' => Yii::$app->request->post('payperiods'),
                'empNo' => Yii::$app->user->identity->{'Employee No_'}
            ];
            $path = Yii::$app->navhelper->PortalReports($service, $data, 'IanGeneratePayslip');
            //Yii::$app->recruitment->printrr($path);
            if (is_file($path['return_value'])) {
                $binary = file_get_contents($path['return_value']);
                $content = chunk_split(base64_encode($binary));
                //delete the file after getting it's contents --> This is some house keeping
                //unlink($path['return_value']);


                return $this->render('index', [
                    'report' => true,
                    'content' => $content,
                    'pperiods' => $this->getPayrollperiods()
                ]);
            }
        }

        return $this->render('index', [
            'report' => false,
            'pperiods' => $this->getPayrollperiods()
        ]);
    }

    public function actionGetChangeRequest()
    {
        $service = Yii::$app->params['ServiceName']['MemberEditings'];
        $filter = [
            'Member_No' => Yii::$app->user->identity->{'Member No_'},
        ];
        $changeRequests = Yii::$app->navhelper->getData($service, $filter);

        // echo '<pre>';
        // print_r($loans);
        // exit;


        $result = [];
        $count = 0;

        if (!is_object($changeRequests)) {
            foreach ($changeRequests as $changeRequest) {

                // if(empty($loan->First_Name) && empty($loan->Last_Name) && $loan->Type == '_blank_' ){ //Useless loan this One
                //     continue;
                // }
                ++$count;
                $link = $updateLink =  '';
                $updateLink = Html::a('View Details', ['update', 'Key' => urlencode($changeRequest->Key)], ['class' => 'btn btn-info btn-md']);
                $result['data'][] = [
                    'index' => $count,
                    'First_Name' => !empty($changeRequest->First_Name) ? $changeRequest->First_Name : 'Not Set',
                    'Type_of_Change' => !empty($changeRequest->Type_of_Change) ? $changeRequest->Type_of_Change : 'Not Set',
                    'Portal_Status' => !empty($changeRequest->Portal_Status) ? $changeRequest->Portal_Status : 'Not Set',
                    'Middle_Name' => !empty($changeRequest->Middle_Name) ? $changeRequest->Middle_Name : 'Not Set',
                    'Last_Name' => !empty($changeRequest->Last_Name) ? $changeRequest->Last_Name : 'Not Set',
                    'National_ID_No' => !empty($changeRequest->National_ID_No) ? $changeRequest->National_ID_No : 'Not Set',
                    'Update_Action' => $updateLink,
                ];
            }
        }



        return $result;
    }

    public function actionRead($Key)
    {
        $model = new MemberEditingAttachments();
        $content = $model->read($Key);
        return $this->render('read', ['content' => $content]);
    }

    public function actionUpdate()
    {

        $service = Yii::$app->params['ServiceName']['MemberEditingHeader'];
        $filter = [
            'Key' => urldecode(Yii::$app->request->get('Key')),
        ];
        $result = Yii::$app->navhelper->readByKey($service, urldecode(Yii::$app->request->get('Key')));

        $model = new MemberEditingHeader();

        //load nav result to model
        $attachements = []; // $MemberEditingAttachments->getAttachments($model->Document_No);

        if (Yii::$app->request->post() && $this->loadpost(Yii::$app->request->post()['MemberEditingHeader'], $model)) {
            $refresh = Yii::$app->navhelper->readByKey($service, urldecode(Yii::$app->request->get('Key')));


            if ($result->Change_Type == 'Employment') { //Show General Info Only
                $model->Key = $refresh->Key;
                $model->Portal_Status95339 = 'Submitted';
                $result = Yii::$app->navhelper->updateData($service, $model);
                if (is_object($result)) {
                    //Do Upload Here
                    Yii::$app->session->setFlash('success', 'Submitted Succesfully');
                    return $this->goHome();
                } else {
                    Yii::$app->session->setFlash('error', $result);
                    return $this->redirect(['index']);
                }
            }

            $model->PassPortPhoto = UploadedFile::getInstance($model, 'PassPortPhoto');
            $model->Signature = UploadedFile::getInstance($model, 'Signature');
            $model->upload();
            $model->Key = $refresh->Key;
            $model->Portal_Status95339 = 'Submitted';
            $result = Yii::$app->navhelper->updateData($service, $model);
            if (is_object($result)) {
                //Do Upload Here
                Yii::$app->session->setFlash('success', 'Submitted Succesfully');
                return $this->goHome();
            } else {
                Yii::$app->session->setFlash('error', $result);
                return $this->redirect(['index']);
            }
        }

        if ($result->Change_Type == 'General_Info') { //Show General Info Only
            return $this->render('update-general', [
                'model' => $this->loadtomodel($result, $model),
                'RequiredAttachements' => [], // $this->getMemberEditingAttachements(),
                'MyAttachedDocs' => $attachements,
                'Employers' => ArrayHelper::map($this->getEmployers(), 'Code', 'Name'),
                'image' => $this->getMemberImage(),
                'signature' => $this->getMemberSignature()
            ]);
        }

        if ($result->Change_Type == 'Employment') { //Show General Info Only
            return $this->render('update-employment', [
                'model' => $this->loadtomodel($result, $model),
                'RequiredAttachements' => [], // $this->getMemberEditingAttachements(),
                'Employers' => ArrayHelper::map($this->getEmployers(), 'Code', 'Name'),
            ]);
        }

        if ($result->Change_Type == 'Next_of_KIN') { //Show General Info Only
            return $this->render('update-kins-details', [
                'model' => $this->loadtomodel($result, $model),
                'Kins' => $this->getMemberEditingKins($result->Document_No),
                'Employers' => ArrayHelper::map($this->getEmployers(), 'Code', 'Name'),
                'attachments' => [],
            ]);
        }
    }


    public function getEmployers()
    {
        $service = Yii::$app->params['ServiceName']['Employers'];
        $res = [];
        $Employers = \Yii::$app->navhelper->getData($service);
        if (is_array($Employers)) {
            foreach ($Employers as $Employer) {
                if (isset($Employer->Code))
                    $res[] = [
                        'Code' => @$Employer->Code,
                        'Name' => @$Employer->Name
                    ];
            }
        }

        return $res;
    }


    public function getMemberEditingKins($DocNumber)
    {
        $service = Yii::$app->params['ServiceName']['MembersKINs'];
        $filter = [
            'Source_Code' => $DocNumber,
        ];
        $ApplicationSubscriptions = Yii::$app->navhelper->getData($service, $filter);
        if (is_object($ApplicationSubscriptions)) {
            return [];
        }
        return $ApplicationSubscriptions;
    }

    public function actionUpdateKin()
    {
        $model = new MemberEditingKINs();


        $service = Yii::$app->params['ServiceName']['MembersKINs'];
        $filter = [
            'Key' => urldecode(Yii::$app->request->get('Key')),
        ];
        $result = Yii::$app->navhelper->readByKey($service, urldecode(Yii::$app->request->get('Key')));
        //load nav result to model
        $model->isNewRecord = 0;

        $model = $this->loadtomodel($result, $model);

        if (Yii::$app->request->post() && $this->loadpost(Yii::$app->request->post()['MemberEditingKINs'], $model)) {
            $model->Key = $result->Key;

            $result = Yii::$app->navhelper->updateData($service, $model);
            // echo '<pre>';
            // print_r($result);
            // exit;
            if (is_object($result)) {
                Yii::$app->session->setFlash('success', 'Kin Details Updated Successfully', true);
                return $this->redirect(Yii::$app->request->referrer);
            } else {
                Yii::$app->session->setFlash('error', $result);
                return $this->redirect(Yii::$app->request->referrer);
            }
        }

        if (Yii::$app->request->isAjax) {
            return $this->renderAjax('updateKinModal', [
                'model' => $model,
            ]);
        }
    }


    public function actionDeleteKin()
    {
        $service = Yii::$app->params['ServiceName']['MembersKINs'];
        $result = Yii::$app->navhelper->deleteData($service, urldecode(Yii::$app->request->get('Key')));
        if (!is_string($result)) {
            Yii::$app->session->setFlash('success', 'Deleted Successfully');
            return $this->redirect(Yii::$app->request->referrer);
        } else {
            Yii::$app->session->setFlash('error', $result);
            return $this->redirect(Yii::$app->request->referrer);
        }
    }

    public function actionCreateKin($Key)
    {

        $model = new MemberEditingKINs();

        $service = Yii::$app->params['ServiceName']['MembersKINs'];
        $model->isNewRecord = true;
        $refresh = Yii::$app->navhelper->readByKey('MemberEditingHeader', urldecode(Yii::$app->request->get('Key')));
        $model->Source_Code = $refresh->Document_No;
        if (Yii::$app->request->post() && $this->loadpost(Yii::$app->request->post()['MemberEditingKINs'], $model)) {

            $result = Yii::$app->navhelper->postData($service, $model);
            if (is_object($result)) {
                Yii::$app->session->setFlash('success', 'Kin Added Successfully', true);
                return $this->redirect(Yii::$app->request->referrer);
            } else {
                Yii::$app->session->setFlash('error', $result);
                return $this->redirect(Yii::$app->request->referrer);
            }
        }

        if (Yii::$app->request->isAjax) {
            return $this->renderAjax('updateKinModal', [
                'model' => $model,
            ]);
        }
    }


    public function actionCreate()
    {

        $model = new MemberEditingHeader();
        $service = Yii::$app->params['ServiceName']['MemberEditingHeader'];

        if (Yii::$app->request->post() && $this->loadpost(Yii::$app->request->post()['MemberEditingHeader'], $model)) {
           
            if (Yii::$app->recruitment->CheckIfMemberHasPriorPendingDocument(2) === false) {
                $model->Member_No = Yii::$app->user->identity->{'Member No_'};

                $result = Yii::$app->navhelper->postData($service, $model);
    
                if (is_object($result)) {
                    // $model->isNewRecord = false;
                    // Yii::$app->session->setFlash('success','Loan Created Successfully');
                    return $this->redirect(['update', 'Key' => $result->Key]);
                } else {
                    // $model->isNewRecord = true;
                    Yii::$app->session->setFlash('error', $result);
                    return $this->redirect(['index']);
                }
            }
            Yii::$app->session->setFlash('error', 'Kindly Utilise the document created before creating another one');
            return $this->redirect(Yii::$app->request->referrer);
        
        }

        return $this->render('create', [
            'model' => $model,
            // 'loanProducts'=>$this->getLoanProducts()
        ]);
    }

    public function actionEditProfile()
    {

        $model = new MemberEditingHeader();
        $service = Yii::$app->params['ServiceName']['MemberEditingHeader'];

        $model->Member_No = Yii::$app->user->identity->{'Member No_'};
        // echo '<pre>';
        // print_r($model);
        // exit;
        $result = Yii::$app->navhelper->postData($service, $model);

        if (is_object($result)) {
            // $model->isNewRecord = false;
            // Yii::$app->session->setFlash('success','Loan Created Successfully');
            return $this->redirect(['update', 'Key' => $result->Key,]);
        } else {
            // $model->isNewRecord = true;
            Yii::$app->session->setFlash('error', $result);
            return $this->redirect(['index']);
        }
    }

    public function getMemberImage()
    {
        $service = Yii::$app->params['ServiceName']['PortalIntegrations'];

        $data = [
            'memberNo' => Yii::$app->user->identity->{'Member No_'},
            'responseCode' => '',
            'responseMessage' => ''
        ];
        $response = Yii::$app->navhelper->PortalReports($service, $data, 'GetMemberImage');
        if (is_array($response)) {

            if (isset($response['responseCode']) && $response['responseCode'] == '00') { //image iko
                $image =  json_decode($response['responseMessage']);
                return $image->Image;
            } else {
                return false;
            }
        }
        return false;
    }


    public function getMemberSignature()
    {
        $service = Yii::$app->params['ServiceName']['PortalIntegrations'];

        $data = [
            'memberNo' => Yii::$app->user->identity->{'Member No_'},
            'responseCode' => '',
            'responseMessage' => ''
        ];
        $response = Yii::$app->navhelper->PortalReports($service, $data, 'GetMemberSignature');
        if (is_array($response)) {

            if (isset($response['responseCode']) && $response['responseCode'] == '00') { //image iko
                $image =  json_decode($response['responseMessage']);
                return $image->Signature;
            } else {
                return false;
            }
        }
        return false;
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
