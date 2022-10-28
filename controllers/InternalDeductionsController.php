<?php

namespace app\controllers;

use app\models\LoanInternalDeductions;
use app\models\LoanApplicationHeader;
use Yii;
use yii\filters\AccessControl;
use yii\filters\ContentNegotiator;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\Controller;
use frontend\models\Leave;
use yii\web\Response;


class InternalDeductionsController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => [
                    'account-types', 'account-nos', 'index',
                    'create', 'update', 'delete',
                ],
                'rules' => [
                    [
                        'actions' => [
                            'account-types', 'account-nos', 'index',
                            'create', 'update', 'delete',
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

    public function actionAccountTypes()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $out = [];


        if (isset($_POST['depdrop_parents'])) {
            $parents = $_POST['depdrop_parents'];
            if ($parents != null) {
                $cat_id = $parents[0];
                $cat_id = $parents[0];

                if ($cat_id == '0') {
                    $out = $this->getLoanProducts();
                }

                if ($cat_id == '1') {
                    $out = $this->getAccountsSetUp();
                }
                if ($cat_id == '2') {
                    $out = $this->getNWDAccounts();
                }
                return ['output' => $out, 'selected' => ''];
            }
        }
        return ['output' => '', 'selected' => ''];
    }

    public function actionAccountNos()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $out = [];

        if (isset($_POST['depdrop_parents'])) {
            $parents = $_POST['depdrop_parents'];
            if ($parents != null) {
                $cat_id = $parents[1];
                if ($cat_id == '0') {
                    $out = $this->getMemberLoans();
                }

                if ($cat_id == '1') {
                    $out = $this->getVendors($parents[0]);
                }
                if ($cat_id == '2') {
                    $out = $this->getVendors($parents[0]);
                }
                return ['output' => $out, 'selected' => ''];
            }
        }
        return ['output' => '', 'selected' => ''];
    }


    public function getLoanProducts()
    {
        $service = Yii::$app->params['ServiceName']['LoanProducts'];
        $res = [];
        $selected  = null;
        $LoanProducts = \Yii::$app->navhelper->getData($service);
        foreach ($LoanProducts as $i => $account) {
            $out[] = ['id' => $account->Product_Code, 'name' => $account->Product_Description];
            if ($i == 0) {
                $selected = $account->Product_Code;
            }
        }
        return  $out;
        return $res;
    }


    public function getAccountsSetUp()
    {
        $service = Yii::$app->params['ServiceName']['AccountTypes'];
        $filter = [
            'Share_Capital' => 1
        ];
        $AccountTypes = \Yii::$app->navhelper->getData($service, $filter);
        foreach ($AccountTypes as $i => $account) {
            $out[] = ['id' => $account->Code, 'name' => $account->Description];
        }
        return  $out;
    }

    public function getVendors($AccountType)
    {
        $service = Yii::$app->params['ServiceName']['Vendors'];
        $filter = [
            'Account_Type' => $AccountType,
            'Member_No' => Yii::$app->user->identity->{'No_'},
        ];
        $AccountTypes = \Yii::$app->navhelper->getData($service, $filter);
        if (is_object($AccountTypes)) {
            $out[] = ['id' => '', 'name' => 'No Data Available'];
        } else {
            foreach ($AccountTypes as $i => $account) {
                $out[] = ['id' => $account->No, 'name' => $account->Name];
            }
        }

        return  $out;
    }

    public function getMemberLoans()
    {
        $service = Yii::$app->params['ServiceName']['LoanApplications'];
        $filter = [
            'Member_No' => Yii::$app->user->identity->{'No_'},
        ];
        $LoanApplications = \Yii::$app->navhelper->getData($service, $filter);
        foreach ($LoanApplications as $i => $LoanApplication) {
            $out[] = ['id' => $LoanApplication->Application_No, 'name' => $LoanApplication->Product_Description];
        }
        return  $out;
    }

    public function getNWDAccounts()
    {
        $service = Yii::$app->params['ServiceName']['AccountTypes'];
        $filter = [
            'NWD_Account' => 1
        ];
        $AccountTypes = \Yii::$app->navhelper->getData($service, $filter);
        foreach ($AccountTypes as $i => $account) {
            $out[] = ['id' => $account->Code, 'name' => $account->Description];
        }
        return  $out;
    }

    public function actionCreate($LoanNo)
    {

        $model = new LoanInternalDeductions();
        $service = Yii::$app->params['ServiceName']['LoanInternalDeductions'];
        $model->Loan_No = $LoanNo;
        if (Yii::$app->request->post() && $this->loadpost(Yii::$app->request->post()['LoanInternalDeductions'], $model)) {
            $model->Loan_No = $LoanNo;

            $service = Yii::$app->params['codeUnits']['CreditPortalManagement'];
            $data = [
                'loanNo' => $model->Loan_No,
                'deductionType' => (int)$model->Deduction_Type,
                'accountType' => $model->Account_Type,
                'accountNo' => $model->Account_No,
                'amount' => 0,
                'responseCode' => '',
                'responseMessage' => ''
            ];

            $PostResult = Yii::$app->navhelper->PortalWorkFlows($service, $data, 'SubmitInternalRecovery');
            //   echo '<pre>';
            // print_r($PostResult);
            // exit;

            if (!is_string($PostResult)) {
                if ($PostResult['responseCode'] == 00) { //success Manenos
                    Yii::$app->session->setFlash('success', $PostResult['responseMessage']);
                    return $this->redirect(Yii::$app->request->referrer);
                }
                Yii::$app->session->setFlash('error', $PostResult['responseMessage']);
                return $this->redirect(Yii::$app->request->referrer);
            }
        }

        if (Yii::$app->request->isAjax) {
            return $this->renderAjax('create', [
                'model' => $model,
                'PaySlipParameters' => ArrayHelper::map($this->PaySlipParameters(), 'Code', 'Description'),

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
        $model = new LoanInternalDeductions();
        $service = Yii::$app->params['ServiceName']['LoanTopUp'];

        $LoanModel = $this->GetLoanDetails($Key);
        $model->Loan_Top_Up = $LoanModel->Loan_No;
        $model->Key = $LoanModel->Key;

        if (Yii::$app->request->post() && $this->loadpost(Yii::$app->request->post()['LoanInternalDeductions'], $model)) {
            $result = Yii::$app->navhelper->postData($service, $model);
            if (is_object($result)) {
                Yii::$app->session->setFlash('success', 'Loan Added Successfully');
                return $this->redirect(Yii::$app->request->referrer);
            } else {
                Yii::$app->session->setFlash('error', $result);
                return $this->redirect(Yii::$app->request->referrer);
            }
        }
        return $this->render('index', [
            'model' => $model, 'LoanModel' => $LoanModel,
            'LoanToRecover' => ArrayHelper::map($this->LoansToRecover(), 'Code', 'Description')
        ]);
    }

    public function LoansToRecover1($LoanNo)
    {
        $service = Yii::$app->params['codeUnits']['PortalIntegrations'];
        $data = [
            'memberNo' => Yii::$app->user->identity->{'No_'},
            'responseMessage' => '',
            'loanNo' => $LoanNo
        ];
        $Loan = [];
        $PostResult = Yii::$app->navhelper->PortalWorkFlows($service, $data, 'GetMemberLoans');
        $LoansToDisplay = [];
        if (isset($PostResult['responseMessage'])) {
            $Loans =  json_decode($PostResult['responseMessage'])->Loans;
            if (is_array($Loans)) {
                foreach ($Loans as $Loan) {
                    if ($Loan->CurrentBalance  <= 0) {
                        continue;
                    }
                    $LoansToDisplay[] = [
                        'Code' => $Loan->Code,
                        'Description' => $Loan->Code . '| ' . $Loan->Description . '| ' . $Loan->CurrentBalance,
                    ];
                }

                return $LoansToDisplay;
            }
        } else {
            $Loan = [];
        }

        return $Loan;
    }

    public function actionUpdate()
    {
        $model = new LoanInternalDeductions();
        $service = Yii::$app->params['ServiceName']['LoanInternalDeductions'];
        $filter = [
            'Key' => urldecode(Yii::$app->request->get('Key')),
        ];
        $refresh = Yii::$app->navhelper->readByKey($service, urldecode(Yii::$app->request->get('Key')));
        $model->Key = $refresh->Key;
        $model->Selected = (int)Yii::$app->request->get('Select');
        $result = Yii::$app->navhelper->updateData($service, $model);
        if (is_object($result)) {
            Yii::$app->session->setFlash('success', 'Updated Successfully');
            return $this->redirect(Yii::$app->request->referrer);
        } else {
            Yii::$app->session->setFlash('error', $result);
            return $this->redirect(Yii::$app->request->referrer);
        }
    }


    public function actionDelete()
    {
        $service = Yii::$app->params['ServiceName']['LoanRecoveries'];
        $result = Yii::$app->navhelper->deleteData($service, urldecode(Yii::$app->request->get('Key')));
        if (!is_string($result)) {
            Yii::$app->session->setFlash('success', 'Removed Successfully .');
            return $this->redirect(Yii::$app->request->referrer);
        } else {
            Yii::$app->session->setFlash('error', $result);
            return $this->redirect(Yii::$app->request->referrer);
        }
    }

    public function LoansToRecover()
    {
        $service = Yii::$app->params['ServiceName']['LoansList'];
        $filter = [
            'Member_No'=>Yii::$app->user->identity->{'No_'},
            'Outstanding_Balance' => '> 0'
        ];
        $arr = [];
        $i = 0;
        $result = \Yii::$app->navhelper->getData($service, $filter);
        foreach ($result as $res) {
            if (isset($res->Loan_No)) {
                ++$i;
                $arr[$i] = [
                    'Code' => @$res->Loan_No,
                    'Description' => @$res->Approved_Amount . ' | '. @number_format($res->Outstanding_Balance)
                ];
            }
        }
        return $arr;
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
