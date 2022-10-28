<?php

namespace app\controllers;
use app\models\PortalLoanRepayment;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\filters\ContentNegotiator;
use Yii;
use yii\helpers\Html;
class LoanRepaymentController extends \yii\web\Controller{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => [
                        'signup', 'processcallback', 'index',
                        'create', 'get-member-accounts', 'getloans'
                        ],
                'rules' => [
                   
                     [
                        'actions' => [
                             'signup', 'processcallback', 'index',
                            'create', 'get-member-accounts', 'getloans'
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
                    'processcallback' => ['post'],

                ],
            ],
            'contentNegotiator' =>[
                'class' => ContentNegotiator::class,
                'only' => ['get-member-accounts', 'getloans', 'processcallback'],
                'formatParam' => '_format',
                'formats' => [
                    'application/json' => Response::FORMAT_JSON,
                    //'application/xml' => Response::FORMAT_XML,
                ],
            ]
        ];
    }

    public function actionIndex(){
        return $this->render('index');
    }

    public function actionGetloans(){
        $service = Yii::$app->params['ServiceName']['DisbursedLoans'];
        $filter = [
            'Member_No' => Yii::$app->user->identity->{'No_'},
        ];
        $loans = Yii::$app->navhelper->getData($service,$filter);

        // echo '<pre>';
        // print_r($loans);
        // exit;


        $result = [];
        $count = 0;
      
        if(!is_object($loans)){
            foreach($loans as $loan){

                // if(empty($loan->First_Name) && empty($loan->Last_Name) && $loan->Type == '_blank_' ){ //Useless loan this One
                //     continue;
                // }
                ++$count;
                $link = $updateLink =  '';
                $PayLoan =  Html::a('Pay Loan',['create','Key'=>urlencode($loan->Key)],['class' => 'create btn btn-success btn-md mr-2 ']);

                $updateLink = Html::a('View Details',['update','Key'=> urlencode($loan->Key) ],['class'=>'btn btn-info btn-md']);
                $result['data'][] = [
                    'index' => $count,
                    'Loan_Product' => @$loan->Product_Description,
                    'Application_Date' =>!empty($loan->Application_Date)?date_format( date_create($loan->Application_Date), 'l jS F Y'):'',
                    'Loan_Balance'=> !empty($loan->Loan_Balance)?number_format($loan->Loan_Balance):'', 
                    'Update_Action' =>$PayLoan,
                ];
            }
        
        }
           
      

        return $result;
    }

    public function actionCreate(){

        $model = new PortalLoanRepayment();
        $service = Yii::$app->params['ServiceName']['LoanApplicationHeader'];
        $result = Yii::$app->navhelper->readByKey($service, urldecode(Yii::$app->request->get('Key')));
        
        $model->MemberNo = $result->Member_No;
        $model->Loan_Product = $result->Product_Description;
        $model->LoanAmount = number_format($result->Applied_Amount);
        $model->KeyWord = $this->GetPaybillKeyword($result->Loan_Product);
        
        if(empty($model->KeyWord)){ //Don't Alllow this
            Yii::$app->session->setFlash('error','Keyword for this loan product has bot been set up.');
            return $this->redirect(['index']); 
        }

        $model->RefrenceNo = $model->MemberNo.''.$model->KeyWord;

        if(Yii::$app->request->post() && $this->loadpost(Yii::$app->request->post()['PortalLoanRepayment'],$model)){
          
            if($model->Source == 'MPESA'){
               $PhoneNo =  ltrim($model->PhoneNo, '+254');
               $model->LoanNo = $result->Application_No;
                //Do STK Manenos
                $StkResult =  Yii::$app->MpesaIntergration->createPushSTKNotification($PhoneNo, $model);
                if(isset($StkResult->errorCode)){//Error Occured
                    Yii::$app->session->setFlash('error','We are unable to send a notificationto your mobile. Kindly Try again after sometime');
                    return $this->redirect(['index']);
                }
                Yii::$app->session->setFlash('success','We have sent a notification to the Mobile Phone');
                return $this->redirect(['index']);
            }
            $model->MemberNo = Yii::$app->user->identity->{'No_'};
            
            $result = Yii::$app->navhelper->postData($service,$model);
            // echo '<pre>';
            // print_r($result);
            // exit;
            if(is_object($result)){
                // $model->isNewRecord = false;
                Yii::$app->session->setFlash('success','Your Loan Repayment has been received');
                return $this->redirect(['index']);
            }else{
                // $model->isNewRecord = true;
                Yii::$app->session->setFlash('error',$result);
                return $this->redirect(['index']);

            }

        }
        // if(Yii::$app->request->isAjax){
            return $this->render('create', [
                'model' => $model,
                'LoanData'=>$result
                // 'loanProducts'=>$this->getLoanProducts()
            ]);
        // }
       
    }


    public function actionGetMemberAccounts(){
        $service = Yii::$app->params['ServiceName']['Vendors'];
        $filter = [
            'Supplier_Type'=>'SACCO',
            'Member_No'=>Yii::$app->user->identity->{'No_'},
            'Share_Capital_Account'=>0
        ];
        $res = [];
        $MemberAccounts = \Yii::$app->navhelper->getData($service, $filter);
          return $MemberAccounts;
    }

    

    public function GetPaybillKeyword($LoanProduct){
        $service = Yii::$app->params['ServiceName']['PaybillKeywords'];
        $filter = [
            'Destination_Code'=>$LoanProduct,
            'Destination_Type'=>'Loan_Repayment',
        ];
        $PaybillKeywords = \Yii::$app->navhelper->getData($service, $filter);
        if(is_object($PaybillKeywords)){
            return '';
        }
        return $PaybillKeywords[0]->Keyword;
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