<?php

namespace app\controllers;

use app\models\CheckOffVariationHeader;
use app\models\FixedDepositCard;
use app\models\StandingOrderCard;
use Yii;
use yii\filters\AccessControl;
use yii\filters\ContentNegotiator;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\Controller;
use frontend\models\Leave;
use yii\web\Response;


class CheckOffVariationController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => [
                    'create', 'update', 'index',
                    'set-fixed-product',
                    'set-fixed-period', 'set-fixed-amount',
                    'set-maturity-action',
                    'set-fixed-date', 'get-banks', 'get-vendors',
                    'delete', 'view', 'get-fixed-deposits'
                ],
                'rules' => [
                    [
                        'actions' => [
                            'create', 'update', 'index',
                            'set-fixed-product',
                            'set-fixed-period', 'set-fixed-amount',
                            'set-maturity-action',
                            'set-fixed-date', 'get-banks', 'get-vendors',
                            'delete', 'view', 'get-fixed-deposits'
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
                'only' => ['get-checkoff-applications', 'get-banks', 'get-vendors'],
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
        $model = new FixedDepositCard();
        return $this->render('index');
    }

    public function actionCreate()
    {

        $model = new CheckOffVariationHeader();


        $service = Yii::$app->params['ServiceName']['CheckoffVariation'];


        if (Yii::$app->recruitment->CheckIfMemberHasPriorPendingDocument(4) === false) {
            $model->Member_No = Yii::$app->user->identity->{'Member No_'};
            $model->Effective_Date = date('Y-m-d');
            $result = Yii::$app->navhelper->postData($service, $model);
    
            if (is_object($result)) {
    
                Yii::$app->session->setFlash('success', 'Added Successfully', true);
                return $this->redirect(['update', 'Key' => $result->Key]);
            } else {
                Yii::$app->session->setFlash('error', $result);
                return $this->redirect(['index']);
            }
        }
        Yii::$app->session->setFlash('error', 'Kindly Utilise the document created before creating another one');
        return $this->redirect(['index']);
    
    }

    public function actionCommitRowData()
    {
        Yii::$app->response->format = \yii\web\response::FORMAT_JSON;
        $service = Yii::$app->params['ServiceName']['CheckoffVariationLines'];
        $OtherInfo = [];
        $data = (object)[];

        $refresh = Yii::$app->navhelper->readByKey($service, urldecode(Yii::$app->request->post('Key')));

        if (is_object($refresh)) {
            $data->Key = $refresh->Key;
            $data->New_Contribution = (int)str_replace(',', '', Yii::$app->request->post('Parameter_Value'));
            $result = Yii::$app->navhelper->updateData($service, $data);
            if (is_string($result)) {
                return $result;
            }
            $OtherInfo  = [
                'Key' => $result->Key
            ];
            return $OtherInfo;
        }


        return $refresh;
    }

    public function actionUpdate()
    {
        $model = new CheckOffVariationHeader();

        $service = Yii::$app->params['ServiceName']['CheckoffVariation'];
        $filter = [
            'Key' => urldecode(Yii::$app->request->get('Key')),
        ];
        $result = Yii::$app->navhelper->readByKey($service, urldecode(Yii::$app->request->get('Key')));
        $model->isNewRecord = false;
        //load nav result to model
        // $model = $this->loadtomodel($result,$model);


        if (Yii::$app->request->post() && $this->loadpost(Yii::$app->request->post()['CheckOffVariationHeader'], $model)) {
            $model->Key = $result->Key;
            $model->Portal_Status = 'Submitted';

            $result = Yii::$app->navhelper->updateData($service, $model);
            //  echo '<pre>';
            // print_r($result->FD_No);
            // exit;int
            if (is_object($result)) {
                //Submit
                // $CodeUnitService = Yii::$app->params['ServiceName']['PortalFactory'];
                // $data = [
                //     'docNum' => $result->FD_No,
                // ];
                // $SubmitResult = Yii::$app->navhelper->PortalWorkFlows($CodeUnitService, $data, 'SubmitFixedDeposit');
                // if (!is_string($SubmitResult)) {
                Yii::$app->session->setFlash('success', 'Check Off Submitted Successfully.', true);
                return $this->redirect(['index']);
                // } else {
                //     Yii::$app->session->setFlash('error', $SubmitResult);
                //     return $this->redirect(['index']);
                // }
            } else {
                Yii::$app->session->setFlash('error', $result);
                return $this->redirect(['index']);
            }
        }

        return $this->render('update', [
            'model' => $this->loadtomodel($result, $model),
            'STOTypes' => $this->getStandingOrderTypes(),
        ]);
    }

    public function actionSetFixedProduct()
    {
        $model = new FixedDepositCard();
        $service = Yii::$app->params['ServiceName']['FixedDepositCard'];

        $filter = [
            'FD_No' => Yii::$app->request->post('DocNum')
        ];
        $request = Yii::$app->navhelper->getData($service, $filter);

        if (is_array($request)) {
            Yii::$app->navhelper->loadmodel($request[0], $model);
            $model->Key = $request[0]->Key;
            $model->FD_No = Yii::$app->request->post('DocNum');
            $model->FD_Type = Yii::$app->request->post('Product');
        }

        $request = Yii::$app->navhelper->updateData($service, $model);
        // echo '<pre>';
        // print_r($request);
        // exit;
        Yii::$app->response->format = \yii\web\response::FORMAT_JSON;
        if (is_object($request)) {
            $ReturnData  = (object) [
                "Interest_Rate" => isset($request->Interest_Rate) ? $request->Interest_Rate : '',
                "Maturity_Date" => isset($request->Maturity_Date) ? $request->Maturity_Date : '',
                "Expected_Interest" => isset($request->Expected_Interest) ? number_format($request->Expected_Interest) : '',
                "Key" => isset($request->Key) ? $request->Key : '',
                'Charges' => isset($request->Charges) ? number_format($request->Charges) : '',
                'Expected_Interest_Net' => isset($request->Expected_Interest_Net) ? number_format($request->Expected_Interest_Net) : '',
            ];
            return $ReturnData;
        }
        return $request;
    }

    public function actionSetFixedPeriod()
    {
        $model = new FixedDepositCard();
        $service = Yii::$app->params['ServiceName']['FixedDepositCard'];

        $filter = [
            'FD_No' => Yii::$app->request->post('DocNum')
        ];
        $request = Yii::$app->navhelper->getData($service, $filter);

        if (is_array($request)) {
            Yii::$app->navhelper->loadmodel($request[0], $model);
            $model->Key = $request[0]->Key;
            $model->FD_No = Yii::$app->request->post('DocNum');
            $model->Fixed_Period_M = Yii::$app->request->post('Months');
        }

        $request = Yii::$app->navhelper->updateData($service, $model);
        // echo '<pre>';
        // print_r($request);
        // exit;
        Yii::$app->response->format = \yii\web\response::FORMAT_JSON;
        if (is_object($request)) {
            $ReturnData  = (object) [
                "Interest_Rate" => isset($request->Interest_Rate) ? $request->Interest_Rate : '',
                "Maturity_Date" => isset($request->Maturity_Date) ? $request->Maturity_Date : '',
                "Expected_Interest" => isset($request->Expected_Interest) ? number_format($request->Expected_Interest) : '',
                "Key" => isset($request->Key) ? $request->Key : '',
                'Charges' => isset($request->Charges) ? number_format($request->Charges) : '',
                'Expected_Interest_Net' => isset($request->Expected_Interest_Net) ? number_format($request->Expected_Interest_Net) : '',
            ];
            return $ReturnData;
        }
        return $request;
    }

    public function actionSetFixedAmount()
    {
        $model = new FixedDepositCard();
        $service = Yii::$app->params['ServiceName']['FixedDepositCard'];

        $filter = [
            'FD_No' => Yii::$app->request->post('DocNum')
        ];
        $request = Yii::$app->navhelper->getData($service, $filter);

        if (is_array($request)) {
            Yii::$app->navhelper->loadmodel($request[0], $model);
            $model->Key = $request[0]->Key;
            $model->FD_No = Yii::$app->request->post('DocNum');
            $model->Fixed_Amount = (int)Yii::$app->request->post('AmountToFix');
        }

        $request = Yii::$app->navhelper->updateData($service, $model);
        // echo '<pre>';
        // print_r($request);
        // exit;
        Yii::$app->response->format = \yii\web\response::FORMAT_JSON;
        if (is_object($request)) {
            $ReturnData  = (object) [
                "Interest_Rate" => isset($request->Interest_Rate) ? $request->Interest_Rate : '',
                "Maturity_Date" => isset($request->Maturity_Date) ? $request->Maturity_Date : '',
                "Expected_Interest" => isset($request->Expected_Interest) ? number_format($request->Expected_Interest) : '',
                "Key" => isset($request->Key) ? $request->Key : '',
                'Charges' => isset($request->Charges) ? number_format($request->Charges) : '',
                'Expected_Interest_Net' => isset($request->Expected_Interest_Net) ? number_format($request->Expected_Interest_Net) : '',
            ];
            return $ReturnData;
        }
        return $request;
    }

    public function actionSetMaturityAction()
    {
        $model = new FixedDepositCard();
        $service = Yii::$app->params['ServiceName']['FixedDepositCard'];

        $filter = [
            'FD_No' => Yii::$app->request->post('DocNum')
        ];
        $request = Yii::$app->navhelper->getData($service, $filter);

        if (is_array($request)) {
            Yii::$app->navhelper->loadmodel($request[0], $model);
            $model->Key = $request[0]->Key;
            $model->FD_No = Yii::$app->request->post('DocNum');
            $model->Maturity_Action = Yii::$app->request->post('MaturityAction');
        }

        $request = Yii::$app->navhelper->updateData($service, $model);
        // echo '<pre>';
        // print_r($request);
        // exit;
        Yii::$app->response->format = \yii\web\response::FORMAT_JSON;
        if (is_object($request)) {
            $ReturnData  = (object) [
                "Interest_Rate" => isset($request->Interest_Rate) ? $request->Interest_Rate : '',
                "Maturity_Date" => isset($request->Maturity_Date) ? $request->Maturity_Date : '',
                "Expected_Interest" => isset($request->Expected_Interest) ? number_format($request->Expected_Interest) : '',
                "Key" => isset($request->Key) ? $request->Key : '',
                'Charges' => isset($request->Charges) ? number_format($request->Charges) : '',
                'Expected_Interest_Net' => isset($request->Expected_Interest_Net) ? number_format($request->Expected_Interest_Net) : '',
            ];
            return $ReturnData;
        }
        return $request;
    }

    public function actionSetFixedDate()
    {
        $model = new FixedDepositCard();
        $service = Yii::$app->params['ServiceName']['FixedDepositCard'];

        $filter = [
            'FD_No' => Yii::$app->request->post('DocNum')
        ];
        $request = Yii::$app->navhelper->getData($service, $filter);

        if (is_array($request)) {
            Yii::$app->navhelper->loadmodel($request[0], $model);
            $model->Key = $request[0]->Key;
            $model->FD_No = Yii::$app->request->post('DocNum');
            $model->Fixed_Date = date('Y-m-d', strtotime(Yii::$app->request->post('FixedDate')));
        }

        $request = Yii::$app->navhelper->updateData($service, $model);
        // echo '<pre>';
        // print_r($request);
        // exit;
        Yii::$app->response->format = \yii\web\response::FORMAT_JSON;
        if (is_object($request)) {
            $ReturnData  = (object) [
                "Interest_Rate" => isset($request->Interest_Rate) ? $request->Interest_Rate : '',
                "Maturity_Date" => isset($request->Maturity_Date) ? $request->Maturity_Date : '',
                "Expected_Interest" => isset($request->Expected_Interest) ? number_format($request->Expected_Interest) : '',
                "Key" => isset($request->Key) ? $request->Key : '',
                'Charges' => isset($request->Charges) ? number_format($request->Charges) : '',
                'Expected_Interest_Net' => isset($request->Expected_Interest_Net) ? number_format($request->Expected_Interest_Net) : '',
            ];
            return $ReturnData;
        }
        return $request;
    }



    public function actionGetBanks()
    {
        $service = Yii::$app->params['ServiceName']['BankAccounts'];
        $BankAccounts = \Yii::$app->navhelper->getData($service);
        if (is_array($BankAccounts)) {
            foreach ($BankAccounts as $BankAccount) {
                if (!empty(@$BankAccount->No || @$BankAccount->Name)) {
                    $res[] = [
                        'Code' => $BankAccount->No,
                        'Name' => @$BankAccount->Name
                    ];
                }
            }
        }
        return $res;
    }

    public function actionGetVendors()
    {
        $service = Yii::$app->params['ServiceName']['Vendors'];
        $filter = [
            'Supplier_Type' => 'SACCO',
            'Member_No' => Yii::$app->user->identity->{'No_'},
            'Share_Capital_Account' => 0,
            'NWD_Account' => 0
        ];
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

    public function getStandingOrderTypes()
    {
        $service = Yii::$app->params['ServiceName']['StandingOrderTypes'];
        $res = [];
        $FDTypes = \Yii::$app->navhelper->getData($service);
        if (is_object($FDTypes)) {
            return $res;
        }
        foreach ($FDTypes as $FDType) {
            if (!empty($FDType->STO_Code || $FDType->Description))
                $res[] = [
                    'Code' => $FDType->STO_Code,
                    'Name' => $FDType->Description
                ];
        }

        return $res;
    }

    public function actionDelete()
    {
        $service = Yii::$app->params['ServiceName']['CheckoffVariationApplications'];
        $result = Yii::$app->navhelper->deleteData($service, urldecode(Yii::$app->request->get('Key')));
        if (!is_string($result)) {
            Yii::$app->session->setFlash('success', 'Deleted Successfully .', true);
            return $this->redirect(['index']);
        } else {
            Yii::$app->session->setFlash('error',  $result);
            return $this->redirect(['index']);
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
        $leaveModel = new FixedDepositCard();
        $model = $this->loadtomodel($leave[0], $leaveModel);


        return $this->render('view', [
            'model' => $model,
            'leaveTypes' => ArrayHelper::map($leaveTypes, 'Code', 'Description'),
            'relievers' => ArrayHelper::map($employees, 'No', 'Full_Name'),
        ]);
    }

    public function actionGetCheckoffApplications()
    {
        $service = Yii::$app->params['ServiceName']['CheckoffVariationApplications'];
        $filter = [
            'Member_No' => Yii::$app->user->identity->{'Member No_'},
            'Portal_Status' => 'New'
        ];
        $FixedDeposits = Yii::$app->navhelper->getData($service, $filter);

        // echo '<pre>';
        // print_r($FixedDeposits);
        // exit;


        $result = [];
        $count = 0;

        if (!is_object($FixedDeposits)) {
            foreach ($FixedDeposits as $FixedDeposit) {
                ++$count;
                $link = $updateLink =  '';
                if ($FixedDeposit->Portal_Status == 'New') {
                    $updateLink = Html::a('Edit', ['update', 'Key' => urlencode($FixedDeposit->Key)], ['class' => 'btn btn-info btn-md']);
                    $deleteLink = Html::a('Delete', ['delete', 'Key' => urlencode($FixedDeposit->Key)], ['class' => 'btn btn-danger btn-md']);
                } else {
                    $updateLink = '';
                    $deleteLink = '';
                }
                $result['data'][] = [
                    'index' => $count,
                    'Document_No' =>  $FixedDeposit->Document_No,
                    'Created_On' => date_format(date_create($FixedDeposit->Created_On), 'l jS F Y'),
                    'Status' => $FixedDeposit->Portal_Status,
                    'Update_Action' => $updateLink . ' | ' . $deleteLink,
                    // 'Remove' => $deleteLink
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
