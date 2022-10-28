<?php
namespace app\controllers;
use app\models\DirectDebitList;
use app\models\LoanApplicationHeader;
use Yii;
use yii\filters\AccessControl;
use yii\filters\ContentNegotiator;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\Controller;
use yii\web\Response;
use yii\web\UploadedFile;



class DirectDebitController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['create', 'update','index', 'delete', ],
                'rules' => [
                     [
                        'actions' => ['create', 'update','index', 'delete'],
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
                'only' => ['getkins', 'get-direct-debits', 'get-loan-securities'],
                'formatParam' => '_format',
                'formats' => [
                    'application/json' => Response::FORMAT_JSON,
                    //'application/xml' => Response::FORMAT_XML,
                ],
            ]
        ];
    }

 

    public function actionCreate($LoanNo){

        $model = new DirectDebitList();
        $service = Yii::$app->params['ServiceName']['DirectDebitList'];
        $model->Loan_No = $LoanNo;
        $model->isNewRecord = true;

        if(Yii::$app->request->post() && $this->loadpost(Yii::$app->request->post()['DirectDebitList'],$model)){
            $model->FileName = str_replace(' ', '_', $model->Bank_Code).$model->Account_No;
            $model->DocumentName = $model->FileName = str_replace(' ', '_', $model->Bank_Code);
            $model->docFile = UploadedFile::getInstance($model, 'docFile');
            $model->Loan_No = $LoanNo;
            $model->Member_No = Yii::$app->user->identity->{'No_'};
            // $result = Yii::$app->navhelper->postData($service,$model);
            if($model->upload() == true){
                Yii::$app->session->setFlash('success','Direct Debit Information Added Successfully',true);
                return $this->redirect(Yii::$app->request->referrer);
            }
            return $this->redirect(Yii::$app->request->referrer);
      
        }

        if(Yii::$app->request->isAjax){
            return $this->renderAjax('create', [
                'model' => $model,
                'content'=>'',
                // 'PaySlipParameters' => ArrayHelper::map($this->PaySlipParameters(),'Code','Description'),

            ]);
        }
    }

    public function GetLoanDetails($LoanKey){
        $model = new LoanApplicationHeader();
        $service = Yii::$app->params['ServiceName']['LoanApplicationHeader'];
        $result = Yii::$app->navhelper->readByKey($service, urldecode($LoanKey));
       return $model = $this->loadtomodel($result,$model);
    }
    public function actionIndex($Key){
        $model = new DirectDebitList();
        $LoanModel = $this->GetLoanDetails($Key);
        return $this->render('index', ['model' => $model, 'LoanModel'=>$LoanModel]);

    }

    public function actionUpdate(){
        $model = new DirectDebitList();
        $model->isNewRecord = false;
        $service = Yii::$app->params['ServiceName']['DirectDebitList'];
        $filter = [
            'Key' => urldecode(Yii::$app->request->get('Key')),
        ];
        if(Yii::$app->request->post() && $this->loadpost(Yii::$app->request->post()['DirectDebitList'],$model)){

            $model->FileName = str_replace(' ', '_', $model->Bank_Code).$model->Account_No;
            $model->DocumentName = $model->FileName = str_replace(' ', '_', $model->Bank_Code) .$model->Account_No;
            $model->docFile = UploadedFile::getInstance($model, 'docFile');
            // $model->Loan_No = $LoanNo;
            $model->Member_No = Yii::$app->user->identity->{'No_'};
            // $result = Yii::$app->navhelper->postData($service,$model);
            if($model->upload() == true){
                Yii::$app->session->setFlash('success','Direct Debit Information Added Successfully',true);
                return $this->redirect(Yii::$app->request->referrer);
            }

        }
        $result = Yii::$app->navhelper->readByKey($service, urldecode(Yii::$app->request->get('Key')));
        $content = $model->read($result->LineNo);


        if(Yii::$app->request->isAjax){
            $model->isNewRecord = false;
            // $model = $this->loadtomodel($result,$model);
            // echo '<pre>';
            // print_r($model);
            // exit;
            return $this->renderAjax('update', [
                'model' => $this->loadtomodel($result,$model),
                'content'=>$content,
                'PaySlipParameters' => ArrayHelper::map($this->PaySlipParameters(),'Code','Description'),
            ]);
        }

 
    }


    public function actionDelete(){
        $service = Yii::$app->params['ServiceName']['DirectDebitList'];
        $result = Yii::$app->navhelper->deleteData($service,urldecode(Yii::$app->request->get('Key')));
        if(!is_string($result)){
            Yii::$app->session->setFlash('success','Removed Successfully .');
            return $this->redirect(Yii::$app->request->referrer);
        }else{
            Yii::$app->session->setFlash('error',$result);
            return $this->redirect(Yii::$app->request->referrer);
        }
    }

    public function PaySlipParameters(){
        $service = Yii::$app->params['ServiceName']['PaySlipParameters'];
        $filter = [];
        $arr = [];
        $i = 0;
        $result = \Yii::$app->navhelper->getData($service,$filter);
        foreach($result as $res){
            if(isset($res->Code)){
                ++$i;
                $arr[$i] = [
                    'Code' => @$res->Code,
                    'Description' => @$res->Description
                ];
            }
                
        }
        return $arr;
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