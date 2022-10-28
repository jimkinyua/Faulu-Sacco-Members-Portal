<?php
namespace app\controllers;

use app\models\ChequeBookApplication;
use app\models\FixedDepositCard;
use Yii;
use yii\filters\AccessControl;
use yii\filters\ContentNegotiator;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\Controller;
use frontend\models\Leave;
use yii\web\Response;


class ChequeBookRequestController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['create', 'update','index', 
                'set-fixed-product', 
                'set-fixed-period', 'set-fixed-amount',
                'set-maturity-action',
                'set-fixed-date', 'get-banks','get-vendors',
                'delete', 'view', 'get-cheque-book-requests'
            ],
                'rules' => [
                     [
                        'actions' => ['create', 'update','index', 
                        'set-fixed-product', 
                        'set-fixed-period', 'set-fixed-amount',
                        'set-maturity-action',
                        'set-fixed-date', 'get-banks','get-vendors',
                        'delete', 'view', 'get-cheque-book-requests'
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
            'contentNegotiator' =>[
                'class' => ContentNegotiator::class,
                'only' => ['get-cheque-book-requests', 'get-banks', 'get-vendors'],
                'formatParam' => '_format',
                'formats' => [
                    'application/json' => Response::FORMAT_JSON,
                    //'application/xml' => Response::FORMAT_XML,
                ],
            ]
        ];
    }

    public function actionIndex(){
        $model = new FixedDepositCard();
        return $this->render('index');

    }

    public function actionCreate(){

        $model = new ChequeBookApplication();
        $service = Yii::$app->params['ServiceName']['ChequeBookApplication'];

        if(!isset(Yii::$app->request->post()['ChequeBookApplication'])){
            $model->Member_No = Yii::$app->user->identity->{'Member No_'};
            $model->Application_Date = date('Y-m-d');
            $result = Yii::$app->navhelper->postData($service,$model);
            if(is_object($result) )
            {
                return $this->redirect(['update','Key' => urlencode( $result->Key)]);
            }else{
                Yii::$app->session->setFlash('error', $result);
                return $this->redirect(['index']);
            }
        }

        if(Yii::$app->request->post() && $this->loadpost(Yii::$app->request->post()['ChequeBookApplication'],$model)){

            $result = Yii::$app->navhelper->postData($service,$model);

            if(is_object($result)){

                Yii::$app->session->setFlash('success','Added Successfully',true);
                return $this->redirect(['index']);
            }else{

                Yii::$app->session->setFlash('error',$result);
                return $this->redirect(['index']);

            }

        }

        if(Yii::$app->request->isAjax){
            return $this->renderAjax('create', [
                'model' => $model,
            ]);
        }
    }

    public function actionUpdate(){
        $model = new ChequeBookApplication();
        $service = Yii::$app->params['ServiceName']['ChequeBookApplication'];
        $filter = [
            'Key' => urldecode(Yii::$app->request->get('Key')),
        ];
        $result = Yii::$app->navhelper->readByKey($service, urldecode(Yii::$app->request->get('Key')));
      
        //load nav result to model
        // $model = $this->loadtomodel($result,$model);
       

        if(Yii::$app->request->post() && $this->loadpost(Yii::$app->request->post()['ChequeBookApplication'],$model)){


            $result = Yii::$app->navhelper->updateData($service,$model);
            //  echo '<pre>';
            // print_r($result->FD_No);
            // exit;
            if(is_object($result)){
                //Submit
                // $CodeUnitService = Yii::$app->params['ServiceName']['PortalFactory'];
                // $data = [
                //     'docNum' => $result->FD_No,
                // ];
                // $SubmitResult = Yii::$app->navhelper->PortalWorkFlows($CodeUnitService,$data,'SubmitFixedDeposit');
                // if(!is_string($SubmitResult)){
                    Yii::$app->session->setFlash('success', 'Cheque Book Request Submitted Successfully.', true);
                    return $this->redirect(['index']);
                }else{
                    Yii::$app->session->setFlash('error', $result);
                    return $this->redirect(['index']);
                }
            }

        

        return $this->render('update', [
            'model' => $this->loadtomodel($result,$model),
            'ChequeBookTypes'=>$this->getChequeBookTypes(), 
        ]);

 
    }

    public function actionSetFixedProduct(){
        $model = new FixedDepositCard();
        $service = Yii::$app->params['ServiceName']['FixedDepositCard'];

        $filter = [
            'FD_No' => Yii::$app->request->post('DocNum')
        ];
        $request = Yii::$app->navhelper->getData($service, $filter);

        if(is_array($request)){
            Yii::$app->navhelper->loadmodel($request[0],$model);
            $model->Key = $request[0]->Key;
            $model->FD_No = Yii::$app->request->post('DocNum');
            $model->FD_Type = Yii::$app->request->post('Product');

        }

        $request = Yii::$app->navhelper->updateData($service,$model);
        // echo '<pre>';
        // print_r($request);
        // exit;
        Yii::$app->response->format = \yii\web\response::FORMAT_JSON;
        if(is_object($request)){
            $ReturnData  = (object) [
                "Interest_Rate"=> isset($request->Interest_Rate)?$request->Interest_Rate:'',
                "Maturity_Date"=> isset($request->Maturity_Date)?$request->Maturity_Date:'',
                "Expected_Interest"=> isset($request->Expected_Interest)?number_format($request->Expected_Interest):'',
                "Key"=>isset($request->Key)?$request->Key:'',
                'Charges'=> isset($request->Charges)?number_format($request->Charges):'', 
                'Expected_Interest_Net'=> isset($request->Expected_Interest_Net)?number_format($request->Expected_Interest_Net):'', 
                ];
                return $ReturnData;
        }
        return $request;

    }

    public function actionSetFixedPeriod(){
        $model = new FixedDepositCard();
        $service = Yii::$app->params['ServiceName']['FixedDepositCard'];

        $filter = [
            'FD_No' => Yii::$app->request->post('DocNum')
        ];
        $request = Yii::$app->navhelper->getData($service, $filter);

        if(is_array($request)){
            Yii::$app->navhelper->loadmodel($request[0],$model);
            $model->Key = $request[0]->Key;
            $model->FD_No = Yii::$app->request->post('DocNum');
            $model->Fixed_Period_M = Yii::$app->request->post('Months');

        }

        $request = Yii::$app->navhelper->updateData($service,$model);
        // echo '<pre>';
        // print_r($request);
        // exit;
        Yii::$app->response->format = \yii\web\response::FORMAT_JSON;
        if(is_object($request)){
            $ReturnData  = (object) [
                "Interest_Rate"=> isset($request->Interest_Rate)?$request->Interest_Rate:'',
                "Maturity_Date"=> isset($request->Maturity_Date)?$request->Maturity_Date:'',
                "Expected_Interest"=> isset($request->Expected_Interest)?number_format($request->Expected_Interest):'',
                "Key"=>isset($request->Key)?$request->Key:'',
                'Charges'=> isset($request->Charges)?number_format($request->Charges):'', 
                'Expected_Interest_Net'=> isset($request->Expected_Interest_Net)?number_format($request->Expected_Interest_Net):'', 
                ];
                return $ReturnData;
        }
        return $request;

    }

    public function actionSetFixedAmount(){
        $model = new FixedDepositCard();
        $service = Yii::$app->params['ServiceName']['FixedDepositCard'];

        $filter = [
            'FD_No' => Yii::$app->request->post('DocNum')
        ];
        $request = Yii::$app->navhelper->getData($service, $filter);

        if(is_array($request)){
            Yii::$app->navhelper->loadmodel($request[0],$model);
            $model->Key = $request[0]->Key;
            $model->FD_No = Yii::$app->request->post('DocNum');
            $model->Fixed_Amount = (int)Yii::$app->request->post('AmountToFix');

        }

        $request = Yii::$app->navhelper->updateData($service,$model);
        // echo '<pre>';
        // print_r($request);
        // exit;
        Yii::$app->response->format = \yii\web\response::FORMAT_JSON;
        if(is_object($request)){
            $ReturnData  = (object) [
                "Interest_Rate"=> isset($request->Interest_Rate)?$request->Interest_Rate:'',
                "Maturity_Date"=> isset($request->Maturity_Date)?$request->Maturity_Date:'',
                "Expected_Interest"=> isset($request->Expected_Interest)?number_format($request->Expected_Interest):'',
                "Key"=>isset($request->Key)?$request->Key:'',
                'Charges'=> isset($request->Charges)?number_format($request->Charges):'', 
                'Expected_Interest_Net'=> isset($request->Expected_Interest_Net)?number_format($request->Expected_Interest_Net):'', 
                ];
                return $ReturnData;
        }
        return $request;

    }

    public function actionSetMaturityAction(){
        $model = new FixedDepositCard();
        $service = Yii::$app->params['ServiceName']['FixedDepositCard'];

        $filter = [
            'FD_No' => Yii::$app->request->post('DocNum')
        ];
        $request = Yii::$app->navhelper->getData($service, $filter);

        if(is_array($request)){
            Yii::$app->navhelper->loadmodel($request[0],$model);
            $model->Key = $request[0]->Key;
            $model->FD_No = Yii::$app->request->post('DocNum');
            $model->Maturity_Action = Yii::$app->request->post('MaturityAction');
        }

        $request = Yii::$app->navhelper->updateData($service,$model);
        // echo '<pre>';
        // print_r($request);
        // exit;
        Yii::$app->response->format = \yii\web\response::FORMAT_JSON;
        if(is_object($request)){
            $ReturnData  = (object) [
                "Interest_Rate"=> isset($request->Interest_Rate)?$request->Interest_Rate:'',
                "Maturity_Date"=> isset($request->Maturity_Date)?$request->Maturity_Date:'',
                "Expected_Interest"=> isset($request->Expected_Interest)?number_format($request->Expected_Interest):'',
                "Key"=>isset($request->Key)?$request->Key:'',
                'Charges'=> isset($request->Charges)?number_format($request->Charges):'', 
                'Expected_Interest_Net'=> isset($request->Expected_Interest_Net)?number_format($request->Expected_Interest_Net):'', 
                ];
                return $ReturnData;
        }
        return $request;

    }

    public function actionSetFixedDate(){
        $model = new FixedDepositCard();
        $service = Yii::$app->params['ServiceName']['FixedDepositCard'];

        $filter = [
            'FD_No' => Yii::$app->request->post('DocNum')
        ];
        $request = Yii::$app->navhelper->getData($service, $filter);

        if(is_array($request)){
            Yii::$app->navhelper->loadmodel($request[0],$model);
            $model->Key = $request[0]->Key;
            $model->FD_No = Yii::$app->request->post('DocNum');
            $model->Fixed_Date = date('Y-m-d', strtotime(Yii::$app->request->post('FixedDate')));
        }

        $request = Yii::$app->navhelper->updateData($service,$model);
        // echo '<pre>';
        // print_r($request);
        // exit;
        Yii::$app->response->format = \yii\web\response::FORMAT_JSON;
        if(is_object($request)){
            $ReturnData  = (object) [
                "Interest_Rate"=> isset($request->Interest_Rate)?$request->Interest_Rate:'',
                "Maturity_Date"=> isset($request->Maturity_Date)?$request->Maturity_Date:'',
                "Expected_Interest"=> isset($request->Expected_Interest)?number_format($request->Expected_Interest):'',
                "Key"=>isset($request->Key)?$request->Key:'',
                'Charges'=> isset($request->Charges)?number_format($request->Charges):'', 
                'Expected_Interest_Net'=> isset($request->Expected_Interest_Net)?number_format($request->Expected_Interest_Net):'', 
                ];
                return $ReturnData;
        }
        return $request;

    }



    public function actionGetBanks(){
        $service = Yii::$app->params['ServiceName']['BankAccounts'];
        $BankAccounts = \Yii::$app->navhelper->getData($service);
        if(is_array($BankAccounts)){
            foreach($BankAccounts as $BankAccount){
                if(!empty(@$BankAccount->No || @$BankAccount->Name)){
                    $res[] = [
                        'Code' => $BankAccount->No,
                        'Name' => @$BankAccount->Name
                    ];
                }
                    
            }
        }
        return $res;
    }

    public function actionGetVendors(){
        $service = Yii::$app->params['ServiceName']['Vendors'];
        $filter = [
            'Supplier_Type'=>'SACCO',
            'Member_No'=>Yii::$app->user->identity->{'No_'},
            'Share_Capital_Account'=>0,
            'NWD_Account'=>0
        ];
        $Vendors = \Yii::$app->navhelper->getData($service, $filter);
        if(is_array($Vendors)){
            foreach($Vendors as $Vendor){
                if(!empty(@$Vendor->No || @$Vendor->Name)){
                    $res[] = [
                        'Code' => $Vendor->No,
                        'Name' => @$Vendor->Name
                    ];
                }
                    
            }
        }
        return $res;
    }

    public function getChequeBookTypes(){
        $service = Yii::$app->params['ServiceName']['ChequeBookTypes'];
        $res = [];
        $ChequeBookTypes = \Yii::$app->navhelper->getData($service);
        if(is_object($ChequeBookTypes)){
            return $res;
        }
        foreach($ChequeBookTypes as $FDType){
            if(!empty($FDType->Cheque_Book_Type || $FDType->Description))
            $res[] = [
                'Code' => $FDType->Cheque_Book_Type,
                'Name' => $FDType->Description
            ];
        }

        return $res;
    }

    public function actionDelete(){
        $service = Yii::$app->params['ServiceName']['FixedDepositCard'];
        $result = Yii::$app->navhelper->deleteData($service,urldecode(Yii::$app->request->get('Key')));
        if(!is_string($result)){
            Yii::$app->session->setFlash('success','Kin Deleted Successfully .',true);
            return $this->redirect(['index']);
        }else{
            Yii::$app->session->setFlash('error','Kin Deleted Successfully: '.$result,true);
            return $this->redirect(['index']);
        }
    }


    public function actionView($ApplicationNo){
        $service = Yii::$app->params['ServiceName']['leaveApplicationCard'];
        $leaveTypes = $this->getLeaveTypes();
        $employees = $this->getEmployees();

        $filter = [
            'Application_No' => $ApplicationNo
        ];

        $leave = Yii::$app->navhelper->getData($service, $filter);

        //load nav result to model
        $leaveModel = new FixedDepositCard();
        $model = $this->loadtomodel($leave[0],$leaveModel);


        return $this->render('view',[
            'model' => $model,
            'leaveTypes' => ArrayHelper::map($leaveTypes,'Code','Description'),
            'relievers' => ArrayHelper::map($employees,'No','Full_Name'),
        ]);
    }

    public function actionGetChequeBookRequests(){
        $service = Yii::$app->params['ServiceName']['NewChequeBookApplications'];
        $filter = [
            'Member_No' => Yii::$app->user->identity->{'Member No_'},
        ];
        $FixedDeposits = Yii::$app->navhelper->getData($service,$filter);

        // echo '<pre>';
        // print_r($kins);
        // exit;


        $result = [];
        $count = 0;
      
        if(!is_object($FixedDeposits)){
            foreach($FixedDeposits as $FixedDeposit){
                ++$count;
                $link = $updateLink =  '';
                // if($FixedDeposit->PortalStatus == 'New'){
                    $updateLink = Html::a('Edit',['update','Key'=> urlencode($FixedDeposit->Key) ],['class'=>'btn btn-info btn-md']);
                    $link = Html::a('Remove',['delete','Key'=> urlencode($FixedDeposit->Key) ],['class'=>'btn btn-danger btn-md']);
                // }else{
                    // $updateLink = Html::a('Edit Kin',['#','Key'=> urlencode($FixedDeposit->Key) ],['class'=>'btn btn-info btn-md']);
                    // $link = '';
                // }
                $result['data'][] = [
                    'index' => $count,
                    'Application_Date' => !empty($FixedDeposit->Application_Date)?$FixedDeposit->Application_Date:'',
                    'No_of_Leafs' => !empty($FixedDeposit->No_of_Leafs)?$FixedDeposit->No_of_Leafs:'',
                    'Cheque_Book_Type' => !empty($FixedDeposit->Cheque_Book_Type)?$FixedDeposit->Cheque_Book_Type:'',
                    'Update_Action' => $updateLink,
                    // 'Remove' => $link
                ];
            }
        
        }
           
      

        return $result;
    }

    public function getReligion(){
        $service = Yii::$app->params['ServiceName']['Religion'];
        $filter = [
            'Type' => 'Religion'
        ];
        $religion = \Yii::$app->navhelper->getData($service, $filter);
        return $religion;
    }

    public function loadtomodel($obj,$model){

        if(!is_object($obj)){
            return false;
        }
        $modeldata = (get_object_vars($obj)) ;
        foreach($modeldata as $key => $val){
            if(is_object($val)) continue;
            $model->$key = $val;
        }

        return $model;
    }

    public function loadpost($post,$model){ // load model with form data


        $modeldata = (get_object_vars($model)) ;

        foreach($post as $key => $val){

            $model->$key = $val;
        }

        return $model;
    }
}