<?php

namespace app\controllers;

use app\models\OnllineGuarantorRequests;
use app\models\LoanApplicationHeader;
use Yii;
use yii\filters\AccessControl;
use yii\filters\ContentNegotiator;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\Controller;
use yii\web\Response;
use app\models\AcceptRejectGuarantorshipForm;
use app\models\AcceptRejectGuarantorshipFormSub;

class GuarantorsController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => [
                    'create', 'accep', 'index', 'accept',
                    'accept-sub', 'accept-sub-sec', 'reject',
                    'reject-sub', 'get-loan-details', 'guarantorship-requests',
                    'update', 'delete', 'get-loan-scurities', 'get-members',
                    'view',
                ],
                'rules' => [
                    [
                        'actions' => [
                            'create', 'accep', 'index', 'accept',
                            'accept-sub', 'accept-sub-sec', 'reject',
                            'reject-sub', 'get-loan-details', 'guarantorship-requests',
                            'update', 'delete', 'get-loan-scurities', 'get-members',
                            'view',
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
                'only' => ['getkins', 'get-members', 'get-loan-securities'],
                'formatParam' => '_format',
                'formats' => [
                    'application/json' => Response::FORMAT_JSON,
                    //'application/xml' => Response::FORMAT_XML,
                ],
            ]
        ];
    }

    public function beforeAction($action)
    {

        // your custom code here, if you want the code to run before action filters,
        // which are triggered on the [[EVENT_BEFORE_ACTION]] event, e.g. PageCache or AccessControl
        // if (!Yii::$app->user->isGuest && Yii::$app->recruitment->UserhasImageAndSignature() === false) {
        //     Yii::$app->session->setFlash('error', 'Kindly Provide us with Your Passport and Signature. Seems they are missing from our records.');
        //     $this->redirect(['member-change-request/index']);
        //     return false;
        // }

        if (!parent::beforeAction($action)) {
            return false;
        }

        if (parent::beforeAction($action)) {
            //change layout for error action after 
            //checking for the error action name 
            //so that the layout is set for errors only
            if ($action->id == 'verify-phone') {
                $this->layout = 'error';
            }
            return true;
        }
    }




    public function actionCreate($LoanNo)
    {

        $model = new OnllineGuarantorRequests();
        $service = Yii::$app->params['ServiceName']['LoanGuarantors'];
        $model->Loan_No = $LoanNo;
        if (Yii::$app->request->post() && $this->loadpost(Yii::$app->request->post()['OnllineGuarantorRequests'], $model)) {
            $model->Loan_No = $LoanNo;
            if (Yii::$app->user->identity->{'No_'} == $model->Member_No) {
                // $model->Total_Guaranteed_Amount = null;
            }
            // $model->Amount_Guaranteed = (int)str_replace(',', '',  $model->Amount_Guaranteed);
            $data = (object) [];
            $data->Amount_Guaranteed = $model->Total_Guaranteed_Amount;
            $data->Loan_No = $model->Loan_No;
            $data->Member_No = $model->Member_No;

            $CodeUnitService = Yii::$app->params['ServiceName']['PortalReports'];
            $data = [
                'memberNo' => $model->Member_No,
                'amountRequested' => $model->Total_Guaranteed_Amount,
                'loanNo' => $model->Loan_No,
            ];

            $SubmitResult = Yii::$app->navhelper->PortalWorkFlows($CodeUnitService, $data, 'AddGuarantor');

            // Yii::$app->recruitment->printrr($SubmitResult);

            // $result = Yii::$app->navhelper->postData($service, $data);
            if (is_string($SubmitResult)) {
                Yii::$app->session->setFlash('error', $SubmitResult, true);
                return $this->redirect(Yii::$app->request->referrer);
            } else {
                Yii::$app->session->setFlash('success', 'Added Succesfully', true);
                return $this->redirect(Yii::$app->request->referrer);
            }
        }

        if (Yii::$app->request->isAjax) {
            return $this->renderAjax('create', [
                'model' => $model,
            ]);
        }
    }

    public function Accep($No)
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

    public function actionAccept($Key)
    {
        $model = new AcceptRejectGuarantorshipForm();
        $service = Yii::$app->params['ServiceName']['LoanGuarantors'];
        $result = Yii::$app->navhelper->readByKey($service, urldecode($Key));

        $model->Accepted = true;

        if (Yii::$app->request->post() && $this->loadpost(Yii::$app->request->post()['AcceptRejectGuarantorshipForm'], $model)) {


            $service = Yii::$app->params['codeUnits']['PortalReports'];
            $data = [
                'loanNo' => $model->Loan_No,
                'memberNo' => Yii::$app->user->identity->{'No_'},
                'amount' => $model->Amount_Accepted,
            ];

            // echo '<pre>';
            // print_r($data);
            // exit;

            $PostResult = Yii::$app->navhelper->PortalWorkFlows($service, $data, 'ProcessGuarantorRequest');

            
            if (is_string($PostResult)) {
                Yii::$app->session->setFlash('error', $PostResult);
                return $this->redirect(Yii::$app->request->referrer);
            }else{
                Yii::$app->session->setFlash('success', 'Guarantorship Accepted Succesfully', true);
                return $this->redirect(Yii::$app->request->referrer);
            }
               
        }

        if (Yii::$app->request->isAjax) {
            $model = $this->loadtomodel($result, $model);
            return $this->renderAjax('accept-reject', [
                'model' => $model,
                'loanData' => $this->LoanDetails($result->Loan_No)
            ]);
        }
    }

    public function actionAcceptWitness($Key, $Accept = 0)
    {
        $model = new AcceptRejectGuarantorshipForm();
        $service = Yii::$app->params['ServiceName']['OnllineGuarantorRequests'];
        $result = Yii::$app->navhelper->readByKey($service, urldecode($Key));
        $model->Accepted = true;

        if ($Accept == 1) {

            $service = Yii::$app->params['codeUnits']['PortalIntegrations'];
            $data = [
                'loanNo' => $result->Loan_No,
                'memberNo' => Yii::$app->user->identity->{'National ID No'},
                'amount' => 0,
                'responseType' => 0,
                'responseCode' => '',
                'requestType' => 1
            ];

            // echo '<pre>';
            // print_r($data);
            // exit;

            $PostResult = Yii::$app->navhelper->PortalWorkFlows($service, $data, 'ProcessGuarantorRequest');


            if (!is_string($PostResult)) {
                if ($PostResult['responseCode'] == '01') { //Error Manenos
                    Yii::$app->session->setFlash('error', @$PostResult['responseMessage']);
                    return $this->redirect(Yii::$app->request->referrer);
                }
                // $Message = ' Hello '. $model->Member_Name .' We have Received Your Acceptance To Guarantee '. $model->Loan_Principal;
                // $this->SendSMS($Message, $model->PhoneNo);
                Yii::$app->session->setFlash('success', 'Witness Request Accepted Succesfully', true);
                return $this->redirect(Yii::$app->request->referrer);
            }
            Yii::$app->session->setFlash('error', $PostResult);
            return $this->redirect(Yii::$app->request->referrer);
        }

        if (Yii::$app->request->isAjax) {
            $model = $this->loadtomodel($result, $model);
            return $this->renderAjax('accept-reject', [
                'model' => $model,
            ]);
        }
    }
    public function actionRejectWitness($Key, $Accept = 1)
    {
        $model = new AcceptRejectGuarantorshipForm();
        $service = Yii::$app->params['ServiceName']['OnllineGuarantorRequests'];
        $result = Yii::$app->navhelper->readByKey($service, urldecode($Key));
        $model->Accepted = false;

        if ($Accept == 0) {


            $service = Yii::$app->params['codeUnits']['PortalIntegrations'];
            $data = [
                'loanNo' => $result->Loan_No,
                'memberNo' => Yii::$app->user->identity->{'National ID No'},
                'amount' => 0,
                'responseType' => 1,
                'responseCode' => '',
                'requestType' => 1
            ];

            // echo '<pre>';
            // print_r($data);
            // exit;

            $PostResult = Yii::$app->navhelper->PortalWorkFlows($service, $data, 'ProcessGuarantorRequest');


            if (!is_string($PostResult)) {
                if ($PostResult['responseCode'] == '01') { //Error Manenos
                    Yii::$app->session->setFlash('error', $PostResult['responseMessage']);
                    return $this->redirect(Yii::$app->request->referrer);
                }
                // $Message = ' Hello '. $model->Member_Name .' We have Received Your Acceptance To Guarantee '. $model->Loan_Principal;
                // $this->SendSMS($Message, $model->PhoneNo);
                Yii::$app->session->setFlash('success', 'Witness Request Declined Succesfully', true);
                return $this->redirect(Yii::$app->request->referrer);
            }
            Yii::$app->session->setFlash('error', $PostResult);
            return $this->redirect(Yii::$app->request->referrer);
        }

        if (Yii::$app->request->isAjax) {
            $model = $this->loadtomodel($result, $model);
            return $this->renderAjax('accept-reject', [
                'model' => $model,
            ]);
        }
    }

    public function actionAcceptSub($Key)
    {
        $model = new AcceptRejectGuarantorshipFormSub();
        $service = Yii::$app->params['ServiceName']['OnlineGuarantorSubRequests'];
        $result = Yii::$app->navhelper->readByKey($service, urldecode($Key));
        $model->Accepted = true;
        // $model->Document_No = 

        if (Yii::$app->request->post() && $this->loadpost(Yii::$app->request->post()['AcceptRejectGuarantorshipFormSub'], $model)) {

            $service = Yii::$app->params['codeUnits']['PortalIntegrations'];
            $data = [
                'documentNo' => $result->Document_No,
                'memberNo' => Yii::$app->user->identity->{'National ID No'},
                'initialGuarantor' => $result->Guarantor_No,
                'responseType' => 1,
                'responseCode' => '',
                'amount' => $model->Amount,
            ];

            $PostResult = Yii::$app->navhelper->PortalWorkFlows($service, $data, 'RespondToGuarantorSubstituion');
            echo '<pre>';
            print_r($PostResult);
            exit;

            if (!is_string($PostResult)) {
                if ($PostResult['responseCode'] == '01') { //Error Manenos
                    Yii::$app->session->setFlash('error', $PostResult['responseMessage']);
                    return $this->redirect(Yii::$app->request->referrer);
                }
                // $Message = ' Hello '. $model->Member_Name .' We have Received Your Acceptance To Guarantee '. $model->Loan_Principal;
                // $this->SendSMS($Message, $model->PhoneNo);
                Yii::$app->session->setFlash('success', 'Guarantorship Accepted Succesfully', true);
                return $this->redirect(Yii::$app->request->referrer);
            }
            Yii::$app->session->setFlash('error', $PostResult);
            return $this->redirect(Yii::$app->request->referrer);
        }

        if (Yii::$app->request->isAjax) {
            $model = $this->loadtomodel($result, $model);
            return $this->renderAjax('accept-reject-sub', [
                'model' => $model,
            ]);
        }
    }

    public function actionAcceptSubSec($Key)
    {
        $model = new AcceptRejectGuarantorshipFormSub();
        $service = Yii::$app->params['ServiceName']['SecurityReplacements'];
        $result = Yii::$app->navhelper->readByKey($service, urldecode($Key));
        //   echo '<pre>';
        // print_r($result);
        // exit;

        $model->Accepted = true;
        // $model->Document_No = 

        if (Yii::$app->request->post() && $this->loadpost(Yii::$app->request->post()['AcceptRejectGuarantorshipFormSub'], $model)) {

            //  echo '<pre>';
            // print_r($model);
            // exit;

            $service = Yii::$app->params['codeUnits']['CreditPortalManagement'];
            $data = [
                'loanNo' => $model->Loan_No,
                'memberNo' => Yii::$app->user->identity->{'Member No_'},
                'amount' => $model->GuaranteedAmount,
                'sourceOfFunds' => $model->Account_Type,
                'accepted' => 1,
                'rejected' => 0,
                'responseCode' => '',
                'responseMessage' => '',
                'docNum' => $result->Document_No,
            ];


            $PostResult = Yii::$app->navhelper->PortalWorkFlows($service, $data, 'ProcessOnlineSecuritySub');
            // echo '<pre>';
            // print_r($PostResult);
            // exit;

            if (!is_string($PostResult)) {
                if ($PostResult['responseCode'] == '01') { //Error Manenos
                    Yii::$app->session->setFlash('error', $PostResult['responseMessage']);
                    return $this->redirect(Yii::$app->request->referrer);
                }
                // $Message = ' Hello '. $model->Member_Name .' We have Received Your Acceptance To Guarantee '. $model->Loan_Principal;
                // $this->SendSMS($Message, $model->PhoneNo);
                Yii::$app->session->setFlash('success', 'Guarantorship Accepted Succesfully', true);
                return $this->redirect(Yii::$app->request->referrer);
            }
            Yii::$app->session->setFlash('error', $PostResult);
            return $this->redirect(Yii::$app->request->referrer);
        }

        if (Yii::$app->request->isAjax) {
            $model = $this->loadtomodel($result, $model);
            return $this->renderAjax('accept-reject-sub-sec', [
                'model' => $model,
            ]);
        }
    }

    public function SendSMS($Message, $PhoneNo)
    {
        //Todo: Clean The Phone Number to Form 07... 0r 2547....
        // exit($PhoneNo);
        $url = Yii::$app->params['SMS']['BaseURL'];
        $ch = curl_init($url);
        $BearerToken = Yii::$app->params['SMS']['AcessToken'];

        $data = [
            'sender' => 'MHASIBU',
            'message' => $Message,
            'phone' => $PhoneNo,
        ];
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: Bearer  ' . $BearerToken));
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        $response = curl_exec($ch);
        $result = json_decode($response);
        curl_close($ch); // Close the connection
        if (empty($result->status)) { //Error
            Yii::$app->session->setFlash('error', 'Unable to Send a Message to the ');
            return $this->redirect(Yii::$app->request->referrer);
        }
        return true;
    }


    public function actionReject($Key)
    {
        $model = new AcceptRejectGuarantorshipForm();
        $service = Yii::$app->params['ServiceName']['LoanGuarantors'];
        $result = Yii::$app->navhelper->readByKey($service, urldecode($Key));
        $model->Accepted = false;
        if (Yii::$app->request->post() && $this->loadpost(Yii::$app->request->post()['AcceptRejectGuarantorshipForm'], $model)) {

            $service = Yii::$app->params['codeUnits']['PortalReports'];
            $data = [
                'loanNo' => $result->Loan_No,
                'memberNo' => Yii::$app->user->identity->{'No_'},
                'reason' => $model->Rejection_Reason,
            ];

            // echo '<pre>';
            // print_r($data);
            // exit;

            $PostResult = Yii::$app->navhelper->PortalWorkFlows($service, $data, 'RejectGuarantorRequest');


            if (!is_string($PostResult)) {
                Yii::$app->session->setFlash('success', 'Guarantorship Rejected Succesfully', true);
                return $this->redirect(Yii::$app->request->referrer);
            }
            Yii::$app->session->setFlash('error', $PostResult, true);
            return $this->redirect(Yii::$app->request->referrer);
        }

        if (Yii::$app->request->isAjax) {
            $model = $this->loadtomodel($result, $model);
            return $this->renderAjax('accept-reject', [
                'model' => $model,
            ]);
        }
    }

    public function actionRejectSub($Key)
    {
        $model = new AcceptRejectGuarantorshipFormSub();
        $service = Yii::$app->params['ServiceName']['GuarantorReplacements'];
        $result = Yii::$app->navhelper->readByKey($service, urldecode($Key));
        $model->Accepted = false;
        if (Yii::$app->request->post() && $this->loadpost(Yii::$app->request->post()['AcceptRejectGuarantorshipFormSub'], $model)) {

            $service = Yii::$app->params['codeUnits']['CreditPortalManagement'];
            $data = [
                'documentNo' => $model->Document_No,
                'memberNo' => Yii::$app->user->identity->{'National ID No'},
                'initialGuarantor' => $result->Guarantor_No,
                'responseType' => 0,
                'responseCode' => '',
            ];



            $PostResult = Yii::$app->navhelper->PortalWorkFlows($service, $data, 'ProcessOnlineGuarantorrej');
            //  echo '<pre>';
            // print_r($PostResult);
            // exit;

            if (!is_string($PostResult)) {
                if ($PostResult['responseCode'] == '01') { //Error Manenos
                    Yii::$app->session->setFlash('error', $PostResult['responseMessage']);
                    return $this->redirect(Yii::$app->request->referrer);
                }


                $data = [
                    'IDnumber' => Yii::$app->user->identity->{'National ID No'},
                    'MemberName' => Yii::$app->user->identity->{'Full Name'},
                ];

                Yii::$app->recruitment->LogActionHAsBeenAccessed($data, Yii::$app->user->identity->{'Full Name'} . ' Rejected Guaratorship Request for Loan ' . $model->Loan_No);


                Yii::$app->session->setFlash('success', 'Guarantorship Rejected Succesfully', true);
                return $this->redirect(Yii::$app->request->referrer);
            }
        }

        if (Yii::$app->request->isAjax) {
            $model = $this->loadtomodel($result, $model);
            return $this->renderAjax('accept-reject-sub', [
                'model' => $model,
            ]);
        }
    }


    public function GetLoanDetails($LoanKey)
    {
        $model = new LoanApplicationHeader();
        $service = Yii::$app->params['ServiceName']['LoanApplicationCard'];
        $result = Yii::$app->navhelper->readByKey($service, urldecode($LoanKey));
        return $model = $this->loadtomodel($result, $model);
    }
    public function actionIndex($Key)
    {
        $model = new OnllineGuarantorRequests();
        $LoanModel = $this->GetLoanDetails($Key);
        $model->loanFormKey = $LoanModel->Key;
        return $this->render('index', ['model' => $model, 'LoanModel' => $LoanModel]);
    }

    public function actionGuarantorshipRequests()
    {
        // $data = [
        //     'IDnumber' => Yii::$app->user->identity->{'National ID No'},
        //     'MemberName' => Yii::$app->user->identity->{'Full Name'},
        // ];

        // Yii::$app->recruitment->LogActionHAsBeenAccessed($data, Yii::$app->user->identity->{'Full Name'} . ' Viewed their requests to guarantee');


        $model = new OnllineGuarantorRequests();
        $service = Yii::$app->params['ServiceName']['LoanGuarantors'];
        $filter = [
            'Member_No' => Yii::$app->user->identity->{'No_'},
            'Guarantor_Type' => 'Guarantor',
            'Status' => 'Pending',
        ];

        $SubstituitionService = Yii::$app->params['ServiceName']['LoanGuarantors'];
        // $Substituitionfilter = [
        //     'Replace_With' => Yii::$app->user->identity->{'National ID No'},
        //     'Status' => 'New',
        // ];

        // $SecurityReplacementsService = Yii::$app->params['ServiceName']['SecurityReplacements'];
        // $SecurityReplacementsfilter = [
        //     'Guarantor_No' => Yii::$app->user->identity->{'Member No_'},
        //     'Status' => 'New',


        // ];

        $GuarantorshipRequests = \Yii::$app->navhelper->getData($service, $filter);
        $GuarantorSubstituitionRequests = []; //Yii::$app->navhelper->getData($SubstituitionService, $Substituitionfilter);
        $GuarantorSecurityReplacementsRequests = []; //\Yii::$app->navhelper->getData($SecurityReplacementsService,$SecurityReplacementsfilter);
        // echo '<pre>';
        // print_r(Yii::$app->user->identity);
        // exit;

        return $this->render(
            'guarantorship-requests',
            [
                'model' => $model,
                'GuarantorshipRequests' => $GuarantorshipRequests,
                'GuarantorSubstituitionRequests' => $GuarantorSubstituitionRequests,
                'GuarantorSecurityReplacementsRequests' => $GuarantorSecurityReplacementsRequests
            ]
        );
    }

    public function actionSubstituitionRequests()
    {

        $data = [
            'IDnumber' => Yii::$app->user->identity->{'National ID No'},
            'MemberName' => Yii::$app->user->identity->{'Full Name'},
        ];

        Yii::$app->recruitment->LogActionHAsBeenAccessed($data, Yii::$app->user->identity->{'Full Name'} . ' Viewed their requests to Substitution Requests');

        $model = new OnllineGuarantorRequests();

        $SubstituitionService = Yii::$app->params['ServiceName']['OnlineGuarantorSubRequests'];
        $Substituitionfilter = [
            'Replace_With' => Yii::$app->user->identity->{'National ID No'},
            'Status' => 'New',

        ];


        $GuarantorSubstituitionRequests = Yii::$app->navhelper->getData($SubstituitionService, $Substituitionfilter);


        return $this->render(
            'substituition-requests',
            [
                'model' => $model,
                'GuarantorSubstituitionRequests' => $GuarantorSubstituitionRequests,
            ]
        );
    }

    public function LoanDetails($LoanNo)
    {
        $service = Yii::$app->params['ServiceName']['LoanApplicationCard'];
        $filter = [
            'Loan_No' => $LoanNo
        ];
        $data = Yii::$app->navhelper->getData($service, $filter);
  
        if (is_array($data)) {
            return $data;
        }
        return [];
    }

    public function actionWitnessRequests()
    {
        $data = [
            'IDnumber' => Yii::$app->user->identity->{'National ID No'},
            'MemberName' => Yii::$app->user->identity->{'Full Name'},
        ];

        Yii::$app->recruitment->LogActionHAsBeenAccessed($data, Yii::$app->user->identity->{'Full Name'} . ' Viewed their requests to Witness Loans');

        $model = new OnllineGuarantorRequests();
        $service = Yii::$app->params['ServiceName']['OnllineGuarantorRequests'];
        $filter = [
            'ID_No' => Yii::$app->user->identity->{'National ID No'},
            'Request_Type' => 'Witness',
            'Status' => 'New',

        ];


        $WitnessRequests = \Yii::$app->navhelper->getData($service, $filter);
        // echo '<pre>';
        // print_r($WitnessRequests);
        // exit;
        $GuarantorSubstituitionRequests = []; //\Yii::$app->navhelper->getData($SubstituitionService,$Substituitionfilter);
        $GuarantorSecurityReplacementsRequests = []; //\Yii::$app->navhelper->getData($SecurityReplacementsService,$SecurityReplacementsfilter);

        return $this->render(
            'witness-requests',
            [
                'model' => $model,
                'WitnessRequests' => $WitnessRequests,
                'GuarantorSubstituitionRequests' => $GuarantorSubstituitionRequests,
                'GuarantorSecurityReplacementsRequests' => $GuarantorSecurityReplacementsRequests
            ]
        );
    }

    public function actionUpdate()
    {
        $model = new OnllineGuarantorRequests();
        $service = Yii::$app->params['ServiceName']['LoanGuarantors'];
        $filter = [
            'Key' => urldecode(Yii::$app->request->get('Key')),
        ];
        if (Yii::$app->request->post() && $this->loadpost(Yii::$app->request->post()['OnllineGuarantorRequests'], $model)) {
            $model->Requested_Amount = (int)str_replace(',', '',  $model->Requested_Amount);
            $result = Yii::$app->navhelper->updateData($service, $model);
            if (is_object($result)) {
                Yii::$app->session->setFlash('success', 'Updated Successfully');
                return $this->redirect(Yii::$app->request->referrer);
            } else {
                Yii::$app->session->setFlash('error', $result);
                return $this->redirect(Yii::$app->request->referrer);
            }
        }
        $result = Yii::$app->navhelper->readByKey($service, urldecode(Yii::$app->request->get('Key')));

        if (Yii::$app->request->isAjax) {
            $model = $this->loadtomodel($result, $model);
            return $this->renderAjax('update', [
                'model' => $model,

            ]);
        }
    }


    public function actionDelete()
    {
        $service = Yii::$app->params['ServiceName']['LoanGuarantors'];
        $result = Yii::$app->navhelper->deleteData($service, urldecode(Yii::$app->request->get('Key')));
        if (!is_string($result)) {
            Yii::$app->session->setFlash('success', 'Removed Successfully .');
            return $this->redirect(Yii::$app->request->referrer);
        } else {
            Yii::$app->session->setFlash('error', $result);
            return $this->redirect(Yii::$app->request->referrer);
        }
    }

    public function actionGetLoanSecurities($SecurityType)
    {
        $service = Yii::$app->params['ServiceName']['LoanSecurities'];
        $filter = [];
        $arr = [];
        $i = 0;
        $result = \Yii::$app->navhelper->getData($service, $filter);
        foreach ($result as $res) {
            if (isset($res->Code)) {
                ++$i;
                $arr[$i] = [
                    'Code' => @$res->Code,
                    'Description' => @$res->Description
                ];
            }
        }
        return $arr;
    }

    public function actionGetMembers($SecurityType)
    {
        $service = Yii::$app->params['ServiceName']['Members'];
        $filter = [];
        $arr = [];
        $i = 0;
        $result = \Yii::$app->navhelper->getData($service, $filter);
        foreach ($result as $res) {
            ++$i;
            $arr[$i] = [
                'Code' => $res->No,
                'Description' => $res->Name
            ];
        }
        return $arr;
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



    public function actionGetkins()
    {
        $service = Yii::$app->params['ServiceName']['MemberApplication_KINs'];
        $filter = [
            'App_No' => Yii::$app->user->identity->ApplicationId,
        ];
        $kins = Yii::$app->navhelper->getData($service, $filter);

        // echo '<pre>';
        // print_r($kins);
        // exit;


        $result = [];
        $count = 0;

        if (!is_object($kins)) {
            foreach ($kins as $kin) {

                if (empty($kin->First_Name) && empty($kin->Last_Name) && $kin->Type == '_blank_') { //Useless KIn this One
                    continue;
                }
                ++$count;
                $link = $updateLink =  '';
                if (Yii::$app->user->identity->memberApplicationStatus == 'New') {
                    $updateLink = Html::a('Edit Kin', ['update', 'Key' => urlencode($kin->Key)], ['class' => 'update btn btn-info btn-md']);
                    $link = Html::a('Remove Kin', ['delete', 'Key' => urlencode($kin->Key)], ['class' => 'btn btn-danger btn-md']);
                } else {
                    $updateLink = Html::a('Edit Kin', ['#', 'Key' => urlencode($kin->Key)], ['class' => 'btn btn-info btn-md']);
                    $link = '';
                }
                $result['data'][] = [
                    'index' => $count,
                    'Type' => $kin->Type,
                    'First_Name' => !empty($kin->First_Name) ? $kin->First_Name : '',
                    'Middle_Name' => !empty($kin->Middle_Name) ? $kin->Middle_Name : '',
                    'DOB' => !empty($kin->DOB) ? $kin->DOB : '',
                    'Allocation_Percent' => !empty($kin->Middle_Name) ? $kin->Allocation_Percent : '',
                    'Update_Action' => $updateLink,
                    'Remove' => $link
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
