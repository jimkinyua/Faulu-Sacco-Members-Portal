<?php

namespace app\controllers;

use app\models\InterAccountTransferCard;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\filters\ContentNegotiator;
use Yii;
use yii\helpers\Html;

class InterAccountController extends \yii\web\Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['create', 'index'],
                'rules' => [
                    [
                        'actions' => ['index', 'create'],
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
                'only' => ['getloans'],
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

    public function actionSendForApproval($No)
    {
        $service = Yii::$app->params['ServiceName']['PortalFactory'];

        $data = [
            'applicationNo' => $No,
            'sendMail' => 1,
            'approvalUrl' => '',
        ];


        $result = Yii::$app->navhelper->PortalWorkFlows($service, $data, 'IanSendLoanApplicationForApproval');

        if (!is_string($result)) {
            Yii::$app->session->setFlash('success', 'Loan SEnt for Approval Successfully.', true);
            //return $this->redirect(['view','No' => $No]);
            return $this->redirect(['index']);
        } else {

            Yii::$app->session->setFlash('error', 'Error Sending Request for Approval  : ' . $result);
            // return $this->redirect(['view','No' => $No]);
            return $this->redirect(['index']);
        }
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


    public function actionUpdate()
    {

        $service = Yii::$app->params['ServiceName']['LoanApplicationHeader'];
        $filter = [
            'Key' => urldecode(Yii::$app->request->get('Key')),
        ];
        $result = Yii::$app->navhelper->readByKey($service, urldecode(Yii::$app->request->get('Key')));

        $model = new LoanApplicationHeader();
        $model->isNewRecord = false;

        //load nav result to model
        $model = $this->loadtomodel($result, $model);

        if (Yii::$app->request->post() && $this->loadpost(Yii::$app->request->post()['LoanApplicationHeader'], $model)) {
            $result = Yii::$app->navhelper->updateData($service, $model);
            if (!empty($result)) {
                Yii::$app->session->setFlash('success', 'Kin Updated Successfully', true);
                return $this->redirect(Yii::$app->request->referrer);
            } else {
                Yii::$app->session->setFlash('error', 'Error Updating Kin : ' . $result, true);
                return $this->redirect(['index']);
            }
        }

        return $this->render('update', [
            'model' => $model,
            'loanProducts' => $this->getLoanProducts()

        ]);
    }


    public function actionCreate()
    {

        $model = new InterAccountTransferCard();

        $service = Yii::$app->params['ServiceName']['InterAccountTransferCard'];

        if (Yii::$app->request->post() && $this->loadpost(Yii::$app->request->post()['InterAccountTransferCard'], $model)) {

            $service = Yii::$app->params['codeUnits']['CreditPortalManagement'];
            $data = [
                'sourceMemberNo' => Yii::$app->user->identity->{'Member No_'},
                'destinationMemberNo' => $model->Destination_Member_No,
                'amount' => (int)$model->Amount,
                'reponseCode' => '',
                'responseMessage' => ''
            ];

            $PostResult = Yii::$app->navhelper->PortalWorkFlows($service, $data, 'CreateInterAccountTransfer');
            //   echo '<pre>';
            // print_r($PostResult);
            // exit;

            if (!is_string($PostResult)) {
                if ($PostResult['reponseCode'] == 00) { //success Manenos
                    Yii::$app->session->setFlash('success', $PostResult['responseMessage']);
                    return $this->goHome();
                }
                Yii::$app->session->setFlash('error', $PostResult['responseMessage']);
                return $this->goHome();
            }
            Yii::$app->session->setFlash('error', $PostResult['responseMessage']);
            return $this->goHome();
        }

        // if(Yii::$app->request->isAjax){
        return $this->render('create', [
            'model' => $model,
            'MemberAccounts' => $this->getMemberAccounts(),
        ]);
        // }

    }


    public function getMemberAccounts()
    {
        $service = Yii::$app->params['ServiceName']['VendorLookupCustom'];
        $filter = [
            'Member_No' => Yii::$app->user->identity->{'Member No_'},
        ];
        $res = [];
        $Vendors = \Yii::$app->navhelper->getData($service, $filter);
        if (is_array($Vendors)) {
            foreach ($Vendors as $Vendor) {
                if (!empty(@$Vendor->No || @$Vendor->Name)) {
                    $res[] = [
                        'Code' => $Vendor->No,
                        'Name' => @$Vendor->Name
                    ];
                }
            }
        }
        return $res;
    }

    public function actionSetLoanProduct()
    {
        $model = new LoanApplicationHeader();
        $service = Yii::$app->params['ServiceName']['LoanApplicationHeader'];

        $filter = [
            'Application_No' => Yii::$app->request->post('LoanNo')
        ];
        $request = Yii::$app->navhelper->getData($service, $filter);

        if (is_array($request)) {
            Yii::$app->navhelper->loadmodel($request[0], $model);
            $model->Key = $request[0]->Key;
            $model->Loan_Product = Yii::$app->request->post('LoanProduct');
        }

        $result = Yii::$app->navhelper->updateData($service, $model);

        Yii::$app->response->format = \yii\web\response::FORMAT_JSON;

        return $result;
    }

    public function actionSetLoanAppliedAmount()
    {
        $model = new LoanApplicationHeader();
        $service = Yii::$app->params['ServiceName']['LoanApplicationHeader'];

        $filter = [
            'Application_No' => Yii::$app->request->post('LoanNo')
        ];
        $request = Yii::$app->navhelper->getData($service, $filter);

        if (is_array($request)) {
            Yii::$app->navhelper->loadmodel($request[0], $model);
            $model->Key = $request[0]->Key;
            $model->Applied_Amount = Yii::$app->request->post('Applied_Amount');
        }

        $result = Yii::$app->navhelper->updateData($service, $model);
        //refresh Here
        $this->PaymentSchedule(Yii::$app->request->post('LoanNo'));
        $request = Yii::$app->navhelper->getData($service, $filter);


        Yii::$app->response->format = \yii\web\response::FORMAT_JSON;

        return $request[0];
    }

    public function actionSetLoanRepaymentPeriod()
    {
        $model = new LoanApplicationHeader();
        $service = Yii::$app->params['ServiceName']['LoanApplicationHeader'];

        $filter = [
            'Application_No' => Yii::$app->request->post('LoanNo')
        ];
        $request = Yii::$app->navhelper->getData($service, $filter);

        if (is_array($request)) {
            Yii::$app->navhelper->loadmodel($request[0], $model);
            $model->Key = $request[0]->Key;
            $model->Repayment_Period_M = Yii::$app->request->post('Repayment_Period_M');
        }

        $result = Yii::$app->navhelper->updateData($service, $model);
        //refresh Here
        $this->PaymentSchedule(Yii::$app->request->post('LoanNo'));
        $request = Yii::$app->navhelper->getData($service, $filter);


        Yii::$app->response->format = \yii\web\response::FORMAT_JSON;

        return $request[0];
    }

    public function getLoanProducts()
    {
        $service = Yii::$app->params['ServiceName']['LoanProducts'];
        $res = [];
        $LoanProducts = \Yii::$app->navhelper->getData($service);
        foreach ($LoanProducts as $LoanProduct) {
            if (!empty($LoanProduct->Product_Code || $LoanProduct->Product_Description))
                $res[] = [
                    'Code' => $LoanProduct->Product_Code,
                    'Name' => $LoanProduct->Product_Description
                ];
        }

        return $res;
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
