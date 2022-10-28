<?php

namespace app\controllers;

use app\models\AccountsModel;
use app\models\ApplicationSubscriptions;
use app\models\LoanInternalDeductions;
use app\models\LoanApplicationHeader;
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




class AccountsController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'user' => \Yii::$app->applicant, // this user object defined in web.php
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


    public function actionCommitRowData()
    {
        Yii::$app->response->format = \yii\web\response::FORMAT_JSON;
        $model = new ApplicationSubscriptions();
        $service = Yii::$app->params['ServiceName']['ApplicationSubscriptions'];
        $OtherInfo = [];

        $refresh = Yii::$app->navhelper->readByKey($service, urldecode(Yii::$app->request->post('Key')));

        if (is_object($refresh)) {
            $model->Key = $refresh->Key;
            $model->Amount = (int)str_replace(',', '', Yii::$app->request->post('Parameter_Value'));
            $result = Yii::$app->navhelper->updateData($service, $model);
            $OtherInfo  = [
                'Key' => $result->Key
            ];
            return $OtherInfo;
        }


        return $refresh;
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
        $service = Yii::$app->params['ServiceName']['LoanApplicationHeader'];
        $result = Yii::$app->navhelper->readByKey($service, urldecode($LoanKey));
        return $model = $this->loadtomodel($result, $model);
    }

    public function ApplicantDetails($Key)
    {
        $model = new MemberApplicationCard();
        $service = Yii::$app->params['ServiceName']['MemberApplicationSingle'];
        $memberApplication = Yii::$app->navhelper->readByKey($service, urldecode($Key));
        return $model = $this->loadtomodel($memberApplication, $model);
    }

    public function actionIndex($Key)
    {
        $this->layout = 'applicant-main';
        $model = new AccountsModel();
        $ApplicantData = $this->ApplicantDetails($Key);
        $model->ATM = $ApplicantData->ATM;
        $model->Mobile = $ApplicantData->Mobile;
        $model->Portal = $ApplicantData->Portal;
        $model->FOSA = $ApplicantData->FOSA;
        $service = Yii::$app->params['ServiceName']['MemberApplicationSingle'];

        if (Yii::$app->request->isAjax) {
            if ($this->loadpost(Yii::$app->request->post()['AccountsModel'], $model) && $model->validate()) {

                $filter = [
                    'Application_No' => $ApplicantData->Application_No,
                ];
                $refresh = Yii::$app->navhelper->getData($service, $filter);
                $model->Key = $refresh[0]->Key;
                $model->Portal = true;
                $result = Yii::$app->navhelper->updateData($service, $model);

                if (is_object($result)) {
                    Yii::$app->session->setFlash('success', 'Accounts Information Data Added Successfully', true);
                    return $this->redirect(['/attachement', 'Key' => $Key]);
                } else {
                    return $this->asJson(['error' => $result]);
                }
            }

            $result = [];
            // The code below comes from ActiveForm::validate(). We do not need to validate the model
            // again, as it was already validated by validate(). Just collect the messages.
            foreach ($model->getErrors() as $attribute => $errors) {
                $result[Html::getInputId($model, $attribute)] = $errors;
            }

            return $this->asJson(['validation' => $result]);
        }

        $model->Member_Category = $ApplicantData->Member_Category;

        return $this->render(
            'index',
            [
                'model' => $model,
                'Applicant' => $ApplicantData,
            ]
        );
    }



    public function getAccounts()
    {
        $service = Yii::$app->params['ServiceName']['LoanProducts'];
        $res = [];
        $filter = [
            'Product_Type' => 'Savings_Account'
        ];
        $ApplicationSubscriptions = \Yii::$app->navhelper->getData($service, $filter);
        foreach ($ApplicationSubscriptions as $ApplicationSubscription) {
            if (isset($ApplicationSubscription->Code))
                $res[] = [
                    'Code' => @$ApplicationSubscription->Code,
                    'Name' => @$ApplicationSubscription->Name
                ];
        }

        return $res;
    }

    public function LoansToRecover($LoanNo)
    {
        $service = Yii::$app->params['codeUnits']['PortalIntegrations'];
        $data = [
            'memberNo' => Yii::$app->user->identity->{'Member No_'},
            'responseMessage' => ''
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
        $service = Yii::$app->params['ServiceName']['ApplicationSubscriptions'];
        $result = Yii::$app->navhelper->deleteData($service, urldecode(Yii::$app->request->get('Key')));
        if (!is_string($result)) {
            Yii::$app->session->setFlash('success', 'Removed Successfully .');
            return $this->redirect(Yii::$app->request->referrer);
        } else {
            Yii::$app->session->setFlash('error', $result);
            return $this->redirect(Yii::$app->request->referrer);
        }
    }

    public function PaySlipParameters()
    {
        $service = Yii::$app->params['ServiceName']['LoanInternalDeductions'];
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
