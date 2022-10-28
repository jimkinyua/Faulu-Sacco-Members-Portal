<?php

namespace app\controllers;

use app\models\LoanApplicationHeader;
use app\models\LoanDetails;
use app\models\SubmitLoanModel;
use phpDocumentor\Reflection\Types\Object_;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\filters\ContentNegotiator;
use Yii;
use yii\helpers\Html;
use yii\helpers\Url;

class LoanController extends \yii\web\Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => [
                    'getloans', 'create', 'index',
                    'approved', 'payment-schedule',
                    'send-for-approval', 'update',
                    'sub-sectors', 'sub-sub-sectors',
                    'set-loan-product', 'set-loan-applied-amount',
                    'set-loan-repayment-period', 'get-approved-loans'
                ],
                'rules' => [
                    [
                        'actions' => [
                            'getloans', 'create', 'index',
                            'approved', 'payment-schedule',
                            'send-for-approval', 'update',
                            'sub-sectors', 'sub-sub-sectors',
                            'set-loan-product', 'set-loan-applied-amount',
                            'set-loan-repayment-period', 'get-approved-loans'
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
                'only' => ['getloans', 'get-approved-loans', 'get-pending-loans'],
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

    public function actionPendingApproval()
    {
        return $this->render('pending');
    }





    public function GetLoanDetails($LoanKey)
    {
        $model = new LoanApplicationHeader();
        $service = Yii::$app->params['ServiceName']['LoanApplicationCard'];
        $result = Yii::$app->navhelper->readByKey($service, urldecode($LoanKey));
        return $model = $this->loadtomodel($result, $model);
    }



    public function actionSendForApproval($Key)
    {
        $model = new SubmitLoanModel();

        $LoanModel = $this->GetLoanDetails($Key);
        $service = Yii::$app->params['ServiceName']['LoanApplicationCard'];

        if (Yii::$app->request->isAjax) {
            if ($this->loadpost(Yii::$app->request->post()['SubmitLoanModel'], $model) && $model->validate()) {

                $model->AgreedToTerms = Yii::$app->request->post()['SubmitLoanModel']['AgreedToTerms'];
                $model->Key = $LoanModel->Key;
                $result = Yii::$app->navhelper->updateData($service, $model);
                if (is_string($result)) {
                    return $this->asJson(['error' => $result]);
                } else {
                    // $data = [
                    //     'loanNo' => $result->Loan_No,
                    //     'responseCode' => ''
                    // ];
                    // $codeUnitService = Yii::$app->params['codeUnits']['PortalIntegrations'];

                    // $codeUnitResult = Yii::$app->navhelper->PortalWorkFlows($codeUnitService, $data, 'SubmitLoanApplication');
                    // if (is_string($codeUnitResult)) {
                    //     return $this->asJson(['error' => $codeUnitResult]);
                    // }

                    // $data = [
                    //     'IDnumber' => Yii::$app->user->identity->{'National ID No'},
                    //     'MemberName' => Yii::$app->user->identity->{'Full Name'},
                    // ];

                    // Yii::$app->recruitment->LogActionHAsBeenAccessed($data, Yii::$app->user->identity->{'Full Name'} . ' Submitted a Loan');


                    Yii::$app->session->setFlash('success', 'Loan Submitted Succesfully');
                    return $this->redirect(['index']);
                }
            }

            $result = [];
            // The code below comes from ActiveForm::validate(). We do not need to validate the model
            // again, as it was already validated by save(). Just collect the messages.
            foreach ($model->getErrors() as $attribute => $errors) {
                $result[Html::getInputId($model, $attribute)] = $errors;
            }

            return $this->asJson(['validation' => $result]);
        }


        return $this->render('Confirm', [
            'model' => $model,
            'LoanModel' => $LoanModel,
            'applicationForm' => $this->getLoanApplicationinPDF($LoanModel->Loan_No),
        ]);
    }

    public function getLoanApplicationinPDF($ApplicationNo)
    {

        $service = Yii::$app->params['ServiceName']['PortalReports'];

        $data = [
            'loanNo' => urldecode($ApplicationNo),
        ];
        $path = Yii::$app->navhelper->PortalReports($service, $data, 'GenerateLoanForm');
        // Yii::$app->recruitment->printrr($path);

        if (is_array($path)) {

            $imagePath = Yii::getAlias($path['return_value']);
            // Yii::$app->recruitment->printrr($imagePath);

            if (is_file($imagePath)) {
                $binary = file_get_contents($imagePath);
                $content = chunk_split(base64_encode($binary));
                return $content;
            }
            return '';
        }

        Yii::$app->session->setFlash('error', $path);
        return $this->redirect(['index']);
    }

    public function actionSchedule($Key)
    {
        $model = new LoanApplicationHeader();
        $LoanModel = $this->GetLoanDetails($Key);
        $service = Yii::$app->params['ServiceName']['LoanApplicationCard'];


        if (Yii::$app->request->isPost) {
            return $this->redirect(['/payslip-information', 'Key' => $LoanModel->Key]);
        }
        $Schedule = $this->getLoanScheduleinPDF($LoanModel->Loan_No);
        // if (is_string($Schedule)) {
        //     Yii::$app->recruitment->printrr($Schedule);
        //     Yii::$app->session->setFlash('error', $Schedule);
        //     return $this->redirect(Yii::$app->request->referrer);
        // }
        // exit('g');
        return $this->render('LoanSchedule', [
            'model' => $model,
            'LoanModel' => $LoanModel,
            'applicationForm' => $Schedule,
        ]);
    }

    public function getLoanScheduleinPDF($ApplicationNo)
    {

        $service = Yii::$app->params['ServiceName']['PortalReports'];

        $data = [
            'loanNo' => urldecode($ApplicationNo),
        ];
        $path = Yii::$app->navhelper->PortalReports($service, $data, 'GenerateLoanSchedule');
        // Yii::$app->recruitment->printrr($path);


        if (is_array($path)) {

            $imagePath = Yii::getAlias($path['return_value']);
            // Yii::$app->recruitment->printrr($imagePath);

            if (is_file($imagePath)) {
                $binary = file_get_contents($imagePath);
                $content = chunk_split(base64_encode($binary));
                return $content;
            }
            return '';
        } else {
            return $path;
        }

        Yii::$app->session->setFlash('error', $path);
        return $this->redirect(['index']);
    }


    public function CheckDeliquencyStatus($LoanData)
    {
        Yii::$app->params['MetroPol']['BaseURL'];
        $IndividualDeliquencyStatus = Yii::$app->MetroPolIntergration->actionMetroPolCheck($LoanData, Yii::$app->user->identity->getMemberData(), Yii::$app->params['MetroPol']['IdentityTypes']['NationalID']);

        if ($LoanData->Application_Category == 'Business' || $LoanData->Application_Category == 'salary_and_Business') { //Has Applied Loan For Loan
            if (!empty(Yii::$app->user->identity->getApplicantData()->Pin_Number)) { //registered Business This One
                $BusinessDeliquencyStatus = Yii::$app->MetroPolIntergration->actionMetroPolCheck($LoanData, Yii::$app->user->identity->getMemberData(), Yii::$app->params['MetroPol']['IdentityTypes']['BusinessRegistrationNo']);
            } else {
                $BusinessDeliquencyStatus = (object)[];
                $BusinessDeliquencyStatus->delinquency_code = '004'; //Can't Be Found Because It's not Registered
            }
        } else {
            $BusinessDeliquencyStatus = (object)[];
            $BusinessDeliquencyStatus->delinquency_code = '004'; //Can't Be Found Because It's not Registered
        }

        // echo '<pre>';
        // print_r($BusinessDeliquencyStatus);
        // print_r($IndividualDeliquencyStatus);

        // exit;

        if (
            $IndividualDeliquencyStatus->delinquency_code == '004' || $IndividualDeliquencyStatus->delinquency_code == '005' ||
            $BusinessDeliquencyStatus->delinquency_code == '004' || $BusinessDeliquencyStatus->delinquency_code == '005'
        ) { //Defaulter Detected
            return true;
        }
        return false; //Has Never Defaulted
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

    public function actionGetloans()
    {
        $service = Yii::$app->params['ServiceName']['LoanApplicationList'];
        $filter = [
            'Member_No' => Yii::$app->user->identity->{'No_'},
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
                $updateLink = Html::a('View Details', ['update', 'Key' => urlencode($loan->Key)], ['class' => 'btn btn-info btn-md']);
                $result['data'][] = [
                    'index' => $count,
                    'Loan_Product' => @$loan->Loan_Product_Type_Name,
                    'Status' => @$loan->Status,
                    'Application_Date' => !empty($loan->Application_Date) ? $loan->Application_Date : '',
                    'Applied_Amount' => !empty($loan->Requested_Amount) ? number_format($loan->Requested_Amount) : '',
                    'Update_Action' => $updateLink,
                ];
            }
        }



        return $result;
    }



    public function actionGetApprovedLoans()
    {
        $service = Yii::$app->params['ServiceName']['PostedLoans'];
        $filter = [
            'Member_No' => Yii::$app->user->identity->{'No_'},
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
                $updateLink =   \yii\helpers\Html::a('Disbursement Details', Url::to(['loan/disbursal-details', 'LoanNo' => $loan->Loan_No]), ['class' => 'create btn btn-danger btn-md mr-2 ']);

                // Html::a('Disbursement Details', ['update', 'Key' => urlencode($loan->Key)], ['class' => 'btn btn-danger btn-md']);
                $result['data'][] = [
                    'index' => $count,
                    'Loan_Product' => @$loan->Loan_Product_Type_Name,
                    'Application_Date' => @$loan->Application_Date,
                    // 'Repayment_Start_Date' => !empty($loan->Repayment_Start_Date) ? date_format(date_create($loan->Application_Date), 'l jS F Y') : '',
                    // 'Repayment_End_Date' => !empty($loan->Repayment_End_Date) ? date_format(date_create($loan->Application_Date), 'l jS F Y') : '',
                    'Installments' => !empty($loan->Installments) ? number_format($loan->Installments, 2) : 0,
                    // 'Applied_Amount' => !empty($loan->Applied_Amount) ? number_format($loan->Applied_Amount, 2) : 0,
                    'LoanAmount' => !empty($loan->Applied_Amount) ? number_format($loan->Approved_Amount, 2) : 0,
                    'Principle_Balance' => !empty($loan->Loan_Principle_Repayment) ? number_format($loan->Loan_Principle_Repayment, 2) : 0,
                    'Loan_Interest_Repayment' => !empty($loan->Loan_Interest_Repayment) ? number_format($loan->Loan_Interest_Repayment, 2) : 0,
                    'Update_Action' => $updateLink,
                ];
            }
        }



        return $result;
    }
    public function actionDisbursalDetails($LoanNo)
    {

        $service = Yii::$app->params['ServiceName']['OnllineGuarantorRequests'];
        if (Yii::$app->request->isAjax) {
            return $this->renderAjax('disbursal-charges', [
                'DisbursalDetails' => $this->getDisbursalCharges(urldecode($LoanNo)),
            ]);
        }
    }

    public function getDisbursalCharges($LoanNo)
    {
        $data = [
            'loanNo' => $LoanNo,
            'responseCode' => '',
            'responseMessage' => ''
        ];
        $codeUnitService = Yii::$app->params['codeUnits']['PortalIntegrations'];

        $codeUnitResult = Yii::$app->navhelper->PortalWorkFlows($codeUnitService, $data, 'GetLoanCharges');

        if (isset($codeUnitResult['responseMessage'])) {
            return json_decode($codeUnitResult['responseMessage']);
        } else {
            return 'No Charges ';
        }
    }


    public function actionGetPendingLoans()
    {
        $service = Yii::$app->params['ServiceName']['SubmittedOnlineLoans'];
        $filter = [
            'Member_No' => Yii::$app->user->identity->{'No_'},
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
                $updateLink = Html::a('View Details', ['update', 'Key' => urlencode($loan->Key)], ['class' => 'btn btn-info btn-md']);
                $result['data'][] = [
                    'index' => $count,
                    'Loan_Product' => @$loan->Product_Description,
                    'Applied_Amount' => @number_format($loan->Applied_Amount),
                    'Status' => @$loan->Approval_Status,
                    'Application_Date' => !empty($loan->Application_Date) ? date_format(date_create($loan->Application_Date), 'l jS F Y') : '',
                    'Approved_Amount' => !empty($loan->Principle_Amount) ? number_format($loan->Approved_Amount, 2) : 0,
                    'Status' => $loan->Status,
                ];
            }
        }



        return $result;
    }



    public function actionUpdate()
    {

        $service = Yii::$app->params['ServiceName']['LoanApplicationCard'];
        $filter = [
            'Key' => urldecode(Yii::$app->request->get('Key')),
            // 'Application_No'=>urldecode(Yii::$app->request->get('DocumentNo')),
        ];

        $model = new LoanDetails();

        $model->isNewRecord = false;
        // $LoanApplication = \Yii::$app->navhelper->getData($service, $filter);
        $result = Yii::$app->navhelper->readByKey($service, urldecode(Yii::$app->request->get('Key')));

        //load nav result to model

        if (Yii::$app->request->isAjax) {
            if ($this->loadpost(Yii::$app->request->post()['LoanDetails'], $model) && $model->validate()) {

                $model->Requested_Amount = (int)str_replace(',', '',  $model->Requested_Amount);
                $model->Key = $result->Key;

                $result = Yii::$app->navhelper->updateData($service, $model);
                if (is_string($result)) {
                    return $this->asJson(['error' => $result]);
                } else {
                    Yii::$app->session->setFlash('success', 'Loan Details Saved Successfully', true);
                    return $this->redirect(['schedule', 'Key' => $result->Key]);
                }
            }

            $result = [];
            // The code below comes from ActiveForm::validate(). We do not need to validate the model
            // again, as it was already validated by save(). Just collect the messages.
            foreach ($model->getErrors() as $attribute => $errors) {
                $result[Html::getInputId($model, $attribute)] = $errors;
            }

            return $this->asJson(['validation' => $result]);
        }

        $model = $this->loadtomodel($result, $model);
        // echo '<pre>';
        // print_r($model);
        // exit;

        return $this->render('update', [
            'model' => $model,
            'loanProducts' => $this->getLoanProducts(),
            'EconomicSectors' => $this->getEconomicSectors(),
            'SubEconomicSectors' => $this->getSubSectors($model->Sectors),
            'SubSubEconomicSectors' => $this->getSubSubSectors($model->Sub_Sectors)
        ]);
    }


    public function actionSubSectors($type)
    {
        $result = $this->getSubSectors($type);
        $data = Yii::$app->navhelper->refactorArray($result, 'Code', 'Description');
        echo "<option value=''> Select Sub Sector </option>";

        if (count($data)) {
            foreach ($data  as $k => $v) {
                echo "<option value='$k'>" . $v . "</option>";
            }
        } else {
            echo "<option value=''>No data Available</option>";
        }
    }

    public function actionSubSubSectors($type)
    {
        $result = $this->getSubSubSectors($type);

        $data = Yii::$app->navhelper->refactorArray($result, 'Code', 'Description');
     
        echo "<option value=''> Select Loan Purpose </option>";
        if (count($data)) {
            foreach ($data  as $k => $v) {

                echo "<option value='$k'>" . $v . "</option>";
            }
        } else {
            echo "<option value=''>No data Available</option>";
        }
    }

    public function actionCreate()
    {
        $service = Yii::$app->params['ServiceName']['LoanApplicationCard'];

        $model = new LoanApplicationHeader();
        /*Do initial request */
        if (!isset(Yii::$app->request->post()['LoanApplicationCard'])) {
     
                $model->Member_No = Yii::$app->user->identity->{'No_'};
                $result = Yii::$app->navhelper->postData($service, $model);
               
                if (is_object($result)) {

                    // $data = [
                    //     'IDnumber' => Yii::$app->user->identity->{'National ID No'},
                    //     'MemberName' => Yii::$app->user->identity->{'Full Name'},
                    // ];

                    // Yii::$app->recruitment->LogActionHAsBeenAccessed($data, Yii::$app->user->identity->{'Full Name'} . ' Created a Loan Document Successfully');


                    return $this->redirect(['update', 'Key' => urlencode($result->Key)]);
                } else {
                    Yii::$app->session->setFlash('error', $result);

                    return $this->redirect(['index']);
                }
            Yii::$app->session->setFlash('error', 'Kindly Utilise the document created before creating another one');
            return $this->redirect(['index']);
        }

        if (Yii::$app->request->post() && $this->loadpost(Yii::$app->request->post()['LoanApplicationHeader'], $model)) {

            $model->Member_No = Yii::$app->user->identity->{'No_'};

            $result = Yii::$app->navhelper->postData($service, $model);
            // echo '<pre>';
            // print_r($result);
            // exit;
            if (is_object($result)) {
                $model->isNewRecord = false;
                Yii::$app->session->setFlash('success', 'Loan Created Successfully');
                return $this->redirect(['update', 'Key' => $result->Key, 'DocumentNo' => $result->Application_No]);
            } else {
                $model->isNewRecord = true;
                Yii::$app->session->setFlash('error', $result);
                return $this->redirect(['index']);
            }
        }

        return $this->render('create', [
            'model' => $model,
            'loanProducts' => $this->getLoanProducts()
        ]);
    }

    public function actionSetLoanProduct()
    {
        Yii::$app->response->format = \yii\web\response::FORMAT_JSON;

        $model = new LoanApplicationHeader();
        $service = Yii::$app->params['ServiceName']['LoanApplicationHeader'];
        $result = Yii::$app->navhelper->readByKey($service, urldecode(Yii::$app->request->post('Key')));
        $data = (object) [];
        if (is_object($result)) {
            Yii::$app->navhelper->loadmodel($result, $model);
            $data->Key = $result->Key;
            $data->Product_Code = Yii::$app->request->post('Product');
            $request = Yii::$app->navhelper->updateData($service, $data);
            if (is_object($request)) {
                $ReturnData  = (object) [
                    "Maximum_Repayment_Period" => isset($request->Maximum_Repayment_Period) ? number_format($request->Maximum_Repayment_Period) : ''
                ];
                return $ReturnData;
            }
            return $this->asJson(['error' => $request]);
        }
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
            $newStr =  // If you want it to be "185345321"

                $model->Applied_Amount = (int)str_replace(',', '', Yii::$app->request->post('Applied_Amount'));
        }

        $result = Yii::$app->navhelper->updateData($service, $model);
        if (is_string($result)) {
            return $result;
        }
        //refresh Here
        $this->PaymentSchedule(Yii::$app->request->post('LoanNo'));
        $request = Yii::$app->navhelper->getData($service, $filter);

        Yii::$app->response->format = \yii\web\response::FORMAT_JSON;

        if (is_array($request)) {
            $ReturnData  = (object) [
                "Total_Principle_Repayment" => isset($request[0]->Total_Principle_Repayment) ? number_format($request[0]->Total_Principle_Repayment) : '',
                "Total_Interest_Repayment" => isset($request[0]->Total_Interest_Repayment) ? number_format($request[0]->Total_Interest_Repayment) : '',
                "Total_Loan_Repayment" => isset($request[0]->Total_Loan_Repayment) ? number_format($request[0]->Total_Loan_Repayment) : '',
                "Key" => isset($request[0]->Key) ? $request[0]->Key : '',
                'Monthly_Installment' => isset($request[0]->Monthly_Installment) ? number_format($request[0]->Monthly_Installment) : '',
            ];
            return $ReturnData;
        }
        return $request;
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
            $model->Repayment_Period_M = (int)str_replace(',', '', Yii::$app->request->post('Repayment_Period_M'));
        }

        $result = Yii::$app->navhelper->updateData($service, $model);
        if (is_string($result)) {
            return $result;
        } else {
            //refresh Here
            $this->PaymentSchedule(Yii::$app->request->post('LoanNo'));
            $request = Yii::$app->navhelper->getData($service, $filter);
            Yii::$app->response->format = \yii\web\response::FORMAT_JSON;

            if (is_array($request)) {
                $ReturnData  = (object) [
                    "Total_Principle_Repayment" => isset($request[0]->Total_Principle_Repayment) ? number_format($request[0]->Total_Principle_Repayment) : '',
                    "Total_Interest_Repayment" => isset($request[0]->Total_Interest_Repayment) ? number_format($request[0]->Total_Interest_Repayment) : '',
                    "Total_Loan_Repayment" => isset($request[0]->Total_Loan_Repayment) ? number_format($request[0]->Total_Loan_Repayment) : '',
                    "Key" => isset($request[0]->Key) ? $request[0]->Key : '',
                    'Monthly_Installment' => isset($request[0]->Monthly_Installment) ? number_format($request[0]->Monthly_Installment) : '',
                ];
                return $ReturnData;
            }
            return $request;
        }
    }

    public function getLoanProducts()
    {
        $service = Yii::$app->params['ServiceName']['ProductSetupList'];
        $res = [];
        $filter = [
            'Product_Class_Type' => 'Loan',
        ];
        $LoanProducts = \Yii::$app->navhelper->getData($service, $filter);
        if (is_object($LoanProducts)) {
            return $res;
        }
        foreach ($LoanProducts as $LoanProduct) {
            if (!empty($LoanProduct->Product_ID || @$LoanProduct->Product_Description))
                $res[] = [
                    'Code' => $LoanProduct->Product_ID,
                    'Name' => @$LoanProduct->Product_Description
                ];
        }

        return $res;
    }

    public function getSubSectors($cat_id)
    {
        $service = Yii::$app->params['ServiceName']['SasraSubSector'];
        $filter = [
            'Sector' => $cat_id
        ];
        $res = [];
        $LoanProducts = \Yii::$app->navhelper->getData($service, $filter);
        // if (is_object($LoanProducts)) {
        return $LoanProducts;
        // }

        foreach ($LoanProducts as $LoanProduct) {
            if (isset($LoanProduct->Code) && isset($LoanProduct->Description)) {
                $res[] = [
                    'id' => $LoanProduct->Code,
                    'name' => $LoanProduct->Description
                ];
            }
        }

        return $res;
    }


    public function getSubSubSectors($cat_id)
    {
        $service = Yii::$app->params['ServiceName']['LoanPurpose'];
        $filter = [
            'Sub_Sector' => $cat_id
        ];
        $res = [];
        $LoanProducts = \Yii::$app->navhelper->getData($service, $filter);
    

        // if (is_object($LoanProducts)) {
        return $LoanProducts;
        // }

        foreach ($LoanProducts as $LoanProduct) {
            if (isset($LoanProduct->Code) && isset($LoanProduct->Description)) {
                $res[] = [
                    'id' => $LoanProduct->Code,
                    'name' => $LoanProduct->Description
                ];
            }
        }
       
        return $res;
    }

    public function getEconomicSectors()
    {
        $service = Yii::$app->params['ServiceName']['SasraSectors'];
        $res = [];
        $EconomicSectors = \Yii::$app->navhelper->getData($service);
        if (is_object($EconomicSectors)) {
            return $res;
        }
        foreach ($EconomicSectors as $EconomicSector) {
            if (!empty($EconomicSector->Code || $EconomicSector->Description))
                $res[] = [
                    'Code' => $EconomicSector->Code,
                    'Name' => $EconomicSector->Description
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
