<?php

namespace app\controllers;

use app\models\GuarantorManagementCard;
use phpDocumentor\Reflection\Types\Object_;
use PHPUnit\Framework\Constraint\ArrayHasKey;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\filters\ContentNegotiator;
use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

class GuarantorManagementController extends \yii\web\Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' =>
                [
                    'get-substituition-requests',
                    'create', 'index', 'submit',
                    'update', 'create', 'approved'
                ],
                'rules' => [
                    [
                        'actions' => [
                            'get-substituition-requests',
                            'create', 'index', 'submit',
                            'update', 'create', 'approved'
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
                'only' => ['get-substituition-requests'],
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
        if (!Yii::$app->user->isGuest && Yii::$app->recruitment->UserhasImageAndSignature() === false) {
            Yii::$app->session->setFlash('error', 'Kindly Provide us with Your Passport and Signature. Seems they are missing from our records.');
            $this->redirect(['member-change-request/index']);
            return false;
        }

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



    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionApproved()
    {
        return $this->render('approved');
    }



    public function actionSubmit($DocNum)
    {
        $model = new GuarantorManagementCard();
        // echo '<pre>';
        // print_r($LoanModel);
        // exit;
        $service = Yii::$app->params['ServiceName']['PortalIntegrations'];
        $data = [
            'documentNo' => $DocNum,
        ];

        $result = Yii::$app->navhelper->PortalWorkFlows($service, $data, 'SubmitOnlineGuarantorSubstitution');

        if (!is_string($result)) {
            Yii::$app->session->setFlash('success', 'Loan Sent To The Guarantors  Successfully.', true);
            return $this->redirect(['index']);
        } else {

            Yii::$app->session->setFlash('error', $result);
            // return $this->redirect(['view','No' => $No]);
            return $this->redirect(['index']);
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

    public function NotifyGuarantors($LoanNo)
    {
        $Guarantors = $this::getGuarantors($LoanNo);
        if ($Guarantors) {
            return $Guarantors;
        }
        return false;
    }
    static function getGuarantors($LoanNo)
    {
        $service = Yii::$app->params['ServiceName']['OnllineGuarantorRequests'];
        $filter = [
            'Loan_No' => $LoanNo,
        ];
        $Guarantors = Yii::$app->navhelper->getData($service, $filter);
        if (!is_object($Guarantors)) {
            return $Guarantors;
        }
        return false;
    }


    public function actionGetSubstituitionRequests()
    {
        $service = Yii::$app->params['ServiceName']['GuarantorManagements'];

        $filter = [
            'Member_No' => Yii::$app->user->identity->{'Member No_'},
        ];
        $loans = Yii::$app->navhelper->getData($service, $filter);

        // echo '<pre>';
        // print_r($loans);
        // exit;


        $result = [];
        $count = 0;

        if (!is_object($loans)) {
            foreach ($loans as $loan) {

                // if(empty($loan->First_Name) && empty($loan->Last_Name) && $loan->Type == '_blank_' ){ //Useless loan this One
                //     continue;
                // }
                ++$count;
                $link = $updateLink =  '';
                if ($loan->Approval_Status != 'New') {
                    $updateLink = '';
                } else {
                    $updateLink = Html::a('Edit', ['update', 'Key' => urlencode($loan->Key)], ['class' => 'btn btn-info btn-md']);
                }

                $result['data'][] = [
                    'index' => $count,
                    'Document_No' => @$loan->Document_No,
                    'Created_On' => @$loan->Created_On,
                    'Approval_Status' => @$loan->Approval_Status,
                    'Created_By' => !empty($loan->Created_By) ? $loan->Created_By : '',
                    'Update_Action' => $updateLink,
                ];
            }
        }



        return $result;
    }

    public function actionUpdate()
    {

        $service = Yii::$app->params['ServiceName']['GuarantorManagementCard'];
        $filter = [
            'Key' => urldecode(Yii::$app->request->get('Key')),
        ];
        $result = Yii::$app->navhelper->readByKey($service, urldecode(Yii::$app->request->get('Key')));
        //   echo '<pre>';
        //     print_r($result);
        //     exit;
        $model = new GuarantorManagementCard();

        //load nav result to model

        if (Yii::$app->request->post() && $this->loadpost(Yii::$app->request->post()['GuarantorManagementCard'], $model)) {
            // $model->Application_Category = 0;
            $result = Yii::$app->navhelper->updateData($service, $model);
            // echo '<pre>';
            // print_r($model);
            // exit;
            if (is_string($result)) {
                Yii::$app->session->setFlash('error', $result);
                return $this->redirect(Yii::$app->request->referrer);
            } else {
                Yii::$app->session->setFlash('success', 'Details Saved Successfully', true);
                return $this->redirect(Yii::$app->request->referrer);
            }
        }
        $model = $this->loadtomodel($result, $model);
        return $this->render('update', [
            'model' => $model,
            'MemberLoans' => ArrayHelper::map($this->getMemberLoans(), 'Code', 'Code')
        ]);
    }


    public function actionCreate()
    {
        $service = Yii::$app->params['ServiceName']['GuarantorManagementCard'];
        $model = new GuarantorManagementCard();
        $model->Portal_Status = 'New';

        if (Yii::$app->request->post() && $this->loadpost(Yii::$app->request->post()['GuarantorManagementCard'], $model)) {
           
            if (Yii::$app->recruitment->CheckIfMemberHasPriorPendingDocument(1) === false) {
                $model->Member_No = Yii::$app->user->identity->{'Member No_'};
                // $model->Loan_No = 'L000102';
                $result = Yii::$app->navhelper->postData($service, $model);

                if (is_object($result)) {
                    $this->populateSubstituitionLines($result->Document_No);
                    Yii::$app->session->setFlash('success', 'Added Successfully', true);
                    return $this->redirect(['update', 'Key' => $result->Key]);
                } else {
                    Yii::$app->session->setFlash('error', $result, true);
                    return $this->redirect(Yii::$app->request->referrer);
                }
            }
            Yii::$app->session->setFlash('error', 'Kindly Utilise the document created before creating another one');
            return $this->redirect(Yii::$app->request->referrer);

           
        }

        if (Yii::$app->request->isAjax) {
            return $this->renderAjax('create', [
                'model' => $model,
                'MemberLoans' => ArrayHelper::map($this->getMemberLoans(), 'Code', 'Code')

            ]);
        }
    }

    public function populateSubstituitionLines($DocumentNo)
    {
        $service = Yii::$app->params['codeUnits']['PortalIntegrations'];
        $data = [
            'documentNo' => $DocumentNo,
        ];
        $Loan = [];
        $PostResult = Yii::$app->navhelper->PortalWorkFlows($service, $data, 'PopulateSubstitutionLines');

        return true;
    }

    public function getMemberLoans()
    {
        $service = Yii::$app->params['codeUnits']['PortalIntegrations'];
        $data = [
            'memberNo' => Yii::$app->user->identity->{'Member No_'},
            'responseMessage' => '',
            'loanNo' => ''
        ];
        $Loan = [];
        $PostResult = Yii::$app->navhelper->PortalWorkFlows($service, $data, 'GetMemberLoans');


        if (isset($PostResult['responseMessage'])) {
            $Loan =  json_decode($PostResult['responseMessage'])->Loans;
        } else {
            $Loan = [];
        }

        return $Loan;
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
