<?php

namespace app\controllers;

use app\models\PayslipInformation;
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
use app\models\LoanApplicationAttachements;
use yii\web\UploadedFile;



class PayslipInformationController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => [
                    'create', 'update', 'index',
                    'delete',
                ],
                'rules' => [
                    [
                        'actions' => [
                            'create', 'update', 'index',
                            'delete',
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



    public function actionCreate($LoanNo)
    {

        $model = new PayslipInformation();
        $service = Yii::$app->params['ServiceName']['PayslipInformation'];
        $model->Application_No = $LoanNo;
        if (Yii::$app->request->post() && $this->loadpost(Yii::$app->request->post()['PayslipInformation'], $model)) {

            $model->Application_No = $LoanNo;
            $result = Yii::$app->navhelper->postData($service, $model);
            if (is_object($result)) {
                Yii::$app->session->setFlash('success', 'Payslip Information Added Successfully', true);
                return $this->redirect(Yii::$app->request->referrer);
            } else {
                Yii::$app->session->setFlash('error', $result, true);
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
        $model = new PayslipInformation();
        $LoanModel = $this->GetLoanDetails($Key);
        $model->Key = $LoanModel->Key;
        return $this->render('index', [
            'model' => $model,
            'LoanModel' => $LoanModel,
            'OneThird' =>0, // $this->GetOneThird($LoanModel->Application_No),
            'EstimatedMonthly' =>1, // $this->GetEstimatedMonthly($LoanModel->Application_No),
            'AvailableForRepayment' =>1, // $this->GetAvailableForRepayment($LoanModel->Application_No),
            'AdjustNetIncome' =>1, // $this->GetAdjustNetIncome($LoanModel->Application_No),
        ]);
    }

    public function actionUpdate()
    {
        $model = new PayslipInformation();
        $service = Yii::$app->params['ServiceName']['LoanAppraisalParameters'];
        $filter = [
            'Key' => urldecode(Yii::$app->request->get('Key')),
        ];
        if (Yii::$app->request->post() && $this->loadpost(Yii::$app->request->post()['LoanAppraisalParameters'], $model)) {
            $refresh = Yii::$app->navhelper->readByKey($service, urldecode(Yii::$app->request->get('Key')));
            $model->Key = $refresh->Key;
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
                'PaySlipParameters' => ArrayHelper::map($this->PaySlipParameters(), 'Code', 'Description'),
            ]);
        }
    }

    public function actionCommitRowData()
    {
        Yii::$app->response->format = \yii\web\response::FORMAT_JSON;
        $model = new PayslipInformation();
        $service = Yii::$app->params['ServiceName']['AppraisalSalaryDetails'];
        $OtherInfo = [];

        $refresh = Yii::$app->navhelper->readByKey($service, urldecode(Yii::$app->request->post('Key')));

        if (is_object($refresh)) {
            $model->Key = $refresh->Key;
            $model->Amount = (int)str_replace(',', '', Yii::$app->request->post('Parameter_Value'));
            $result = Yii::$app->navhelper->updateData($service, $model);
            return $result;
        }


        return $refresh;
    }

    public function GetOneThird($LoanNo)
    {
        $service = Yii::$app->params['codeUnits']['PortalIntegrations'];
        $data = [
            'loanNo' => $LoanNo,
        ];

        $PostResult = Yii::$app->navhelper->PortalWorkFlows($service, $data, 'OneThirdBasic');
        if (isset($PostResult['return_value'])) {
            return number_format($PostResult['return_value']);
        }
    }

    public function GetAvailableForRepayment($LoanNo)
    {
        $service = Yii::$app->params['codeUnits']['PortalIntegrations'];
        $data = [
            'loanNo' => $LoanNo,
        ];

        $PostResult = Yii::$app->navhelper->PortalWorkFlows($service, $data, 'GetAvailableRecovery');

        if (isset($PostResult['return_value'])) {
            return number_format($PostResult['return_value']);
        }
    }

    public function GetEstimatedMonthly($LoanNo)
    {
        $service = Yii::$app->params['codeUnits']['PortalIntegrations'];
        $data = [
            'loanNo' => $LoanNo,
        ];

        $PostResult = Yii::$app->navhelper->PortalWorkFlows($service, $data, 'MonthlyRepayment');
        if (isset($PostResult['return_value'])) {
            return number_format($PostResult['return_value']);
        }
    }

    public function GetAdjustNetIncome($LoanNo)
    {
        $service = Yii::$app->params['codeUnits']['PortalIntegrations'];
        $data = [
            'loanNo' => $LoanNo,
        ];

        $PostResult = Yii::$app->navhelper->PortalWorkFlows($service, $data, 'AdjustedNet');
        if (isset($PostResult['return_value'])) {
            return number_format($PostResult['return_value']);
        }
    }




    public function actionDelete()
    {
        $service = Yii::$app->params['ServiceName']['PayslipInformation'];
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
        $service = Yii::$app->params['ServiceName']['PaySlipParameters'];
        $filter = ['Application_Area' => 'Payslip'];
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
