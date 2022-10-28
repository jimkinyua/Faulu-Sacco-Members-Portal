<?php

namespace app\controllers;

use app\models\LoanApplicationHeader;
use app\models\PaymentDetails;
use phpDocumentor\Reflection\Types\Object_;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\filters\ContentNegotiator;
use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

class PaymentDetailsController extends \yii\web\Controller
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
                'only' => ['getloans', 'get-approved-loans'],
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

    public function actionBranches()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $out = [];


        if (isset($_POST['depdrop_parents'])) {
            $parents = $_POST['depdrop_parents'];
            if ($parents != null) {
                $cat_id = $parents[0];
                $cat_id = $parents[0];
                $out = $this->getLoanProducts();

                return ['output' => $out, 'selected' => ''];
            }
        }
        return ['output' => '', 'selected' => ''];
    }

    public function GetLoanDetails($LoanKey)
    {
        $model = new LoanApplicationHeader();
        $service = Yii::$app->params['ServiceName']['LoanApplicationHeader'];
        $result = Yii::$app->navhelper->readByKey($service, urldecode($LoanKey));
        return $model = $this->loadtomodel($result, $model);
    }



    public function actionSendForApproval($Key)
    {
        $model = new LoanApplicationHeader();
        $LoanModel = $this->GetLoanDetails($Key);
        $service = Yii::$app->params['ServiceName']['LoanApplicationHeader'];


        if (Yii::$app->request->post() && $this->loadpost(Yii::$app->request->post()['LoanApplicationHeader'], $model)) {

            //Check If Deliquency Status of Applicant
            // if($this->CheckDeliquencyStatus($LoanModel) === true){
            //     $LoanModel->HasDefaultRecordInCRB = 1;
            // }else{
            //     $LoanModel->HasDefaultRecordInCRB = 0;
            // }

            $LoanModel->AgreedToTerms = Yii::$app->request->post()['LoanApplicationHeader']['AgreedToTerms'];
            $result = Yii::$app->navhelper->updateData($service, $LoanModel);
            // echo '<pre>';
            // print_r($result);
            // exit;
            if (is_string($result)) {
                Yii::$app->session->setFlash('error', $result);
                return $this->redirect(Yii::$app->request->referrer);
            }
            // If Listed, Tell Them to Attach the CRB Clearance Cert
            // if($result->HasDefaultRecordInCRB == 1 && empty($LoanModel->getCRBClearanceCertificates())){
            //     return $this->redirect(['clearance-ceriticate/index', 'Key'=>$result->Key]);
            // }


            // exit('ut');
            // $service = Yii::$app->params['ServiceName']['PortalFactory'];
            // $data = [
            //     'loanno' => $LoanModel->Application_No,
            //     'sendMail' => 1,
            //     'approvalUrl' => '',
            // ];

            $result = []; //Yii::$app->navhelper->PortalWorkFlows($service,$data,'Changelloanstatus');

            if (!is_string($result)) {
                Yii::$app->session->setFlash('success', 'Loan Sent To The Guarantors  Successfully.', true);
                $guarantors =  $this->NotifyGuarantors($LoanModel->Application_No);
                // $ApplicantData = Yii::$app->user->identity->getApplicantData();
                if ($guarantors) {
                    foreach ($guarantors as $guarantor) {
                        // echo '<pre>';
                        // print_r($guarantor);
                        // exit;
                        // $Message = ' Hello '. $guarantor->Member_Name . '. Member '. $ApplicantData->Full_Names . ' Has Added You as a Guarantor for their loan Application. Kindly Log on to the Members Portal (http://197.248.217.154:8060/site/login) To Guarantee The Loan.';
                        // $this->SendSMS($Message, $guarantor->PhoneNo);
                    }
                }
                return $this->redirect(['index']);
            } else {

                Yii::$app->session->setFlash('error', $result);
                // return $this->redirect(['view','No' => $No]);
                return $this->redirect(['index']);
            }
        }

        return $this->render('Confirm', ['model' => $model, 'LoanModel' => $LoanModel]);
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

    public function getBanks()
    {
        $service = Yii::$app->params['ServiceName']['KenyaBanks'];
        $filter = [];

        $res = [];
        $Banks = \Yii::$app->navhelper->getData($service, $filter);
        if (is_object($Banks)) {
            return $res[] = [
                'Code' => '',
                'Name' => ''
            ];
        }
        foreach ($Banks as $Bank) {
            if (!empty($Bank->Bank_Code))
                $res[] = [
                    'Code' => $Bank->Bank_Code,
                    'Name' => $Bank->Bank_Name
                ];
        }

        return $res;
    }

    public function actionGetloans()
    {
        $service = Yii::$app->params['ServiceName']['LoanApplications'];
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
                $updateLink = Html::a('View Details', ['update', 'Key' => urlencode($loan->Key)], ['class' => 'btn btn-info btn-md']);
                $result['data'][] = [
                    'index' => $count,
                    'Loan_Product' => @$loan->Product_Description,
                    'Status' => @$loan->Portal_Status,
                    'Application_Date' => !empty($loan->Application_Date) ? $loan->Application_Date : '',
                    'Applied_Amount' => !empty($loan->Applied_Amount) ? number_format($loan->Applied_Amount) : '',
                    'Update_Action' => $updateLink,
                ];
            }
        }



        return $result;
    }

    public function actionGetApprovedLoans()
    {
        $service = Yii::$app->params['ServiceName']['ApprovedLoans'];
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
                $updateLink = Html::a('View Details', ['update', 'Key' => urlencode($loan->Key)], ['class' => 'btn btn-info btn-md']);
                $result['data'][] = [
                    'index' => $count,
                    'Loan_Product' => @$loan->Product_Description,
                    'Status' => @$loan->Approval_Status,
                    'Application_Date' => !empty($loan->Application_Date) ? date_format(date_create($loan->Application_Date), 'l jS F Y') : '',
                    'Principle_Amount' => !empty($loan->Principle_Amount) ? number_format($loan->Principle_Amount, 2) : 0,
                    'Update_Action' => $updateLink,
                ];
            }
        }



        return $result;
    }

    public function actionUpdate()
    {

        $service = Yii::$app->params['ServiceName']['LoanApplicationHeader'];

        $model = new PaymentDetails();

        $model->isNewRecord = false;
        $result = Yii::$app->navhelper->readByKey($service, urldecode(Yii::$app->request->get('Key')));


        //load nav result to model

        if (Yii::$app->request->isAjax) {
            if ($this->loadpost(Yii::$app->request->post()['PaymentDetails'], $model) && $model->validate()) {

                $model->Key = $result->Key;

                $result = Yii::$app->navhelper->updateData($service, $model);
                if (is_string($result)) {
                    return $this->asJson(['error' => $result]);
                } else {
                    Yii::$app->session->setFlash('success', 'Payment Details Saved Successfully', true);
                    return $this->redirect(['/internal-deductions', 'Key' => $result->Key]);
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
        return $this->render('update', [
            'model' => $model,
            'Banks' => ArrayHelper::map($this->getBanks(), 'Code', 'Name'),


        ]);
    }

    public function actionSubSectors($id)
    {
        $service = Yii::$app->params['ServiceName']['SubSectorNames'];
        $filter = [
            'Sector_Code' => urldecode($id),
        ];
        $res = [];
        $SubSectorNames = \Yii::$app->navhelper->getData($service, $filter);
        echo "<option value=''>-- Select Option --</option>";
        foreach ($SubSectorNames as $SubSector) {
            if (!empty($SubSector->Subsector_Code || $SubSector->Subsector_Name)) {
                echo "<option value='" . $SubSector->Subsector_Code . "'>" . $SubSector->Subsector_Name . "</option>";
            }
        }
    }

    public function actionSubSubSectors($SubSectorCode)
    {
        $service = Yii::$app->params['ServiceName']['SubSubSectors'];
        $filter = [
            'Subsector_Code' => urldecode($SubSectorCode),
        ];
        $res = [];
        $SubSubSectors = \Yii::$app->navhelper->getData($service, $filter);
        echo "<option value=''>-- Select Option --</option>";
        foreach ($SubSubSectors as $SubSubSector) {
            if (!empty($SubSubSector->Sub_Subsector_Code || $SubSubSector->Subsector_Name)) {
                echo "<option value='" . $SubSubSector->Sub_Subsector_Code . "'>" . $SubSubSector->Subsector_Name . "</option>";
            }
        }
    }

    public function actionCreate()
    {

        $model = new LoanApplicationHeader();
        $service = Yii::$app->params['ServiceName']['LoanApplicationHeader'];
        /*Do initial request */
        if (!isset(Yii::$app->request->post()['LoanApplicationHeader'])) {
            //$now = date('Y-m-d');
            $model->Member_No = Yii::$app->user->identity->{'Member No_'};
            $result = Yii::$app->navhelper->postData($service, $model);
            if (is_object($result)) {
                return $this->redirect(['update', 'Key' => urlencode($result->Key)]);
            } else {
                Yii::$app->session->setFlash('error', $result);
                return $this->redirect(['index']);
            }
        }

        if (Yii::$app->request->post() && $this->loadpost(Yii::$app->request->post()['LoanApplicationHeader'], $model)) {

            $model->Member_No = Yii::$app->user->identity->{'Member No_'};

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

        $request = Yii::$app->navhelper->updateData($service, $model);

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
        $service = Yii::$app->params['ServiceName']['OfficialBankBranches'];
        $filter = [
            // 'NWD_Account' => 1
        ];
        $res = [];
        $LoanProducts = \Yii::$app->navhelper->getData($service, $filter);
        if (is_object($LoanProducts)) {
            return $res;
        }

        foreach ($LoanProducts as $LoanProduct) {
            if (isset($LoanProduct->Branch_Code) && isset($LoanProduct->Branch_Name)) {
                $res[] = [
                    'id' => $LoanProduct->Branch_Code,
                    'name' => $LoanProduct->Branch_Name
                ];
            }
        }

        return $res;
    }


    public function getEconomicSectors()
    {
        $service = Yii::$app->params['ServiceName']['EconomicSectors'];
        $res = [];
        $EconomicSectors = \Yii::$app->navhelper->getData($service);
        if (is_object($EconomicSectors)) {
            return $res;
        }
        foreach ($EconomicSectors as $EconomicSector) {
            if (!empty($EconomicSector->Sector_Code || $EconomicSector->Sector_Name))
                $res[] = [
                    'Code' => $EconomicSector->Sector_Code,
                    'Name' => $EconomicSector->Sector_Name
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
