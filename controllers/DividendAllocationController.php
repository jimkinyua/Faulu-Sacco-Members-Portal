<?php

namespace app\controllers;
use app\models\DividendAdvance;
use app\models\DividendAllocationHeader;
use yii\filters\AccessControl;
use yii\helpers\Url;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\filters\ContentNegotiator;
use Yii;
use yii\helpers\Html;
class DividendAllocationController extends \yii\web\Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['index', 'apply','get-dividend-qualifications'],
                'rules' => [
                     [
                        'actions' => ['index', 'apply', 'get-dividend-qualifications'],
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
                'only' => ['get-dividend-allocations', 'get-member-loans', 'get-banks', 'get-bank-branches'],
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

    public function actionGetMemberLoans(){
        $service = Yii::$app->params['ServiceName']['LoansLookup'];
        $filter = [
            'Status'=>'Disbursed',
            'Member_No'=>Yii::$app->user->identity->{'No_'},
            'Loan_Balance'=> '>0'
        ];

        $res = [];
        $LoanProducts = \Yii::$app->navhelper->getData($service, $filter);
        if(is_object($LoanProducts)){
            return $res;
        }
        foreach($LoanProducts as $LoanProduct){
            if(!empty($LoanProduct->Application_No || $$LoanProduct->Applied_Amount))
            $res[] = [
                'Code' => $LoanProduct->Application_No,
                'Product_Description'=>$LoanProduct->Product_Description,
                'Name' => $LoanProduct->Product_Description . ' || '. number_format($LoanProduct->Loan_Balance)
            ];
        }

        return $res;
    }

    public function actionGetBanks(){
        $service = Yii::$app->params['ServiceName']['ExternalAccounts'];
        $filter = [

        ];

        $res = [];
        $ExternalAccounts = \Yii::$app->navhelper->getData($service, $filter);
        if(is_object($ExternalAccounts)){
            return $res;
        }
        foreach($ExternalAccounts as $ExternalAccounts){
            if(!empty($ExternalAccounts->Bank_Code))
            $res[] = [
                'Code' => $ExternalAccounts->Bank_Code,
                'Name' => $ExternalAccounts->Bank_Name
            ];
        }

        return $res;
    }

    public function actionGetBankBranches($Bank_Code){
        $service = Yii::$app->params['ServiceName']['ExtBankBranches'];
        $filter = [
            'Bank_Code'=>urldecode($Bank_Code)
        ];

        $res = [];
        $ExtBankBranches = \Yii::$app->navhelper->getData($service, $filter);
        if(is_object($ExtBankBranches)){
            return $res;
        }
        foreach($ExtBankBranches as $ExtBankBranch){
            if(!empty($ExtBankBranch->Branch_Code))
            $res[] = [
                'Code' => $ExtBankBranch->Branch_Code,
                'Name' => $ExtBankBranch->Branch_Name
            ];
        }

        return $res;
    }
 
    public function actionGetDividendAllocations(){
        $service = Yii::$app->params['ServiceName']['DividendAllocations'];
        $filter = [
            'Member_No' => Yii::$app->user->identity->{'No_'},
        ];
        $DividendAllocations = Yii::$app->navhelper->getData($service,$filter);
        // echo '<pre>';
        // print_r($DividendAllocations);
        // exit;
        $result = [];
        $count = 0;
      
        if(!is_object($DividendAllocations)){
            foreach($DividendAllocations as $DividendAllocation){
                ++$count;
                $link = $updateLink =  '';

                if($DividendAllocation->Submitted == 1){
                    $updateLink = Html::a('View Allocation',['view','Key'=> urlencode($DividendAllocation->Key) ],['class'=>'update btn btn-info btn-md']);
                }else{
                    $updateLink = Html::a('Allocate Dividend',['update','Key'=> urlencode($DividendAllocation->Key) ],['class'=>'update btn btn-info btn-md']);
                }

                $result['data'][] = [
                    'index' => $count,
                    'Net_Amount' => isset($DividendAllocation->Net_Amount)?number_format($DividendAllocation->Net_Amount):0,
                    'Allocation_Type' => !empty($DividendAllocation->Allocation_Type)?$DividendAllocation->Allocation_Type:'',
                    'Account_Name' => !empty($DividendAllocation->Account_Name)?$DividendAllocation->Account_Name:'',
                    'Update_Action' => $updateLink,
                ];
            }
        
        }
           
      

        return $result;
    }



    public function actionUpdate($Key){
        $model = new DividendAllocationHeader();
        $service = Yii::$app->params['ServiceName']['DividendAllocationHeader'];
        $result = Yii::$app->navhelper->readByKey($service, urldecode(Yii::$app->request->get('Key')));
        $model = $this->loadtomodel($result,$model);
         
        if(Yii::$app->request->post() && $this->loadpost(Yii::$app->request->post()['DividendAllocationHeader'],$model)){
            // exit('ha');
            $filter = [
                'Dividend_Code' =>$model->Dividend_Code,
                'Member_No' => Yii::$app->user->identity->{'No_'},
            ];
            $request = Yii::$app->navhelper->getData($service, $filter);
    
            if(is_array($request)){
                $model->Key = $request[0]->Key;
                $model->Net_Amount= null;
            }
    
            $result = Yii::$app->navhelper->updateData($service,$model);


            if(is_object($result)){
                // $model->isNewRecord = false;

                $service = Yii::$app->params['codeUnits']['CreditPortalManagement'];
                $data = [
                    'dividendCode' => $model->Dividend_Code,
                    'memberNo' => Yii::$app->user->identity->{'No_'},
                ];
    
    
                $PostResult = Yii::$app->navhelper->PortalWorkFlows($service,$data,'SubmitDividendAllocation');

                if(is_array($PostResult)){
                    // if($PostResult['return_value'] == 1){ 
                        Yii::$app->session->setFlash('success', 'Dividend Allocation Submitted Successfully', true);
                        return $this->redirect(['index']);
                    // }
                }else{
                    Yii::$app->session->setFlash('error', $PostResult, true);
                    return $this->redirect(['index']);
                }

            }else{
                // $model->isNewRecord = true;
                Yii::$app->session->setFlash('error',$result);
                return $this->redirect(['index']);
            }

        }

        return $this->render('update', [
            'model' => $model,
        ]);
       
    }


    public function actionView($Key){
        $model = new DividendAllocationHeader();
        $service = Yii::$app->params['ServiceName']['DividendAllocationHeader'];
        $result = Yii::$app->navhelper->readByKey($service, urldecode(Yii::$app->request->get('Key')));
        $model = $this->loadtomodel($result,$model);
        return $this->render('view', [
            'model' => $model,
        ]);
       
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
