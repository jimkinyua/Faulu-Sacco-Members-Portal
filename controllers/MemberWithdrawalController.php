<?php

namespace app\controllers;
use app\models\MemberWithdrawalCard;
use app\models\AccountClosureCard;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\filters\ContentNegotiator;
use Yii;
use yii\helpers\Html;
use app\models\WithdrawalAttachements;
use yii\web\UploadedFile;
class MemberWithdrawalController extends \yii\web\Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => [
                    'getloans', 'create','index',
                    'send-for-Approval', 'close-account',
                ],
                'rules' => [
                     [
                        'actions' => [
                            'getloans', 'create','index',
                            'send-for-Approval', 'close-account',
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
                'only' => ['getloans'],
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

    public function actionSendForApproval($No){
        $service = Yii::$app->params['ServiceName']['PortalFactory'];

        $data = [
            'applicationNo' => $No,
            'sendMail' => 1,
            'approvalUrl' => '',
        ];


        $result = Yii::$app->navhelper->PortalWorkFlows($service,$data,'IanSendLoanApplicationForApproval');

        if(!is_string($result)){
            Yii::$app->session->setFlash('success', 'Loan SEnt for Approval Successfully.', true);
            //return $this->redirect(['view','No' => $No]);
             return $this->redirect(['index']);
        }else{

            Yii::$app->session->setFlash('error', 'Error Sending Request for Approval  : '. $result);
            // return $this->redirect(['view','No' => $No]);
             return $this->redirect(['index']);

        }
    }

    public function PaymentSchedule($No){
        $service = Yii::$app->params['ServiceName']['PortalFactory'];

        $data = [
            'loanNo' => $No,
            'sendMail' => 1,
            'approvalUrl' => '',
        ];

        Yii::$app->navhelper->PortalWorkFlows($service,$data,'LoanPaymentSchedule');
        return true;
    }

    public function actionLoanAppraisal(){

        $service = Yii::$app->params['ServiceName']['PortalReports'];

        //Yii::$app->recruitment->printrr(ArrayHelper::map($payrollperiods,'Date_Opened','desc'));
        if(Yii::$app->request->post() && Yii::$app->request->post('payperiods')){
            //Yii::$app->recruitment->printrr(Yii::$app->request->post('payperiods'));
            $data = [
                'selectedPeriod' =>Yii::$app->request->post('payperiods'),
                'empNo' => Yii::$app->user->identity->{'Employee No_'}
             ];
            $path = Yii::$app->navhelper->PortalReports($service,$data,'IanGeneratePayslip');
            //Yii::$app->recruitment->printrr($path);
            if(is_file($path['return_value']))
            {
                $binary = file_get_contents($path['return_value']);
                $content = chunk_split(base64_encode($binary));
                //delete the file after getting it's contents --> This is some house keeping
                //unlink($path['return_value']);


                return $this->render('index',[
                    'report' => true,
                    'content' => $content,
                    'pperiods' => $this->getPayrollperiods()
                ]);
            }

        }

        return $this->render('index',[
            'report' => false,
            'pperiods' => $this->getPayrollperiods()
        ]);

    }

    public function actionCreate(){

        $model = new MemberWithdrawalCard();
        $WithdrawalAttachementsModel = new WithdrawalAttachements();

        $service = Yii::$app->params['ServiceName']['MemberExitHeader'];
        $attachements =false; //$KinAttachmentModel->getAttachments( $ApplicantionData->Application_No);

        if(Yii::$app->request->post() && $this->loadpost(Yii::$app->request->post()['MemberWithdrawalCard'],$model)){

            $result = Yii::$app->navhelper->postData($service,$model);

            if(is_object($result)){
                Yii::$app->session->setFlash('success','Your Request to Exit the SACCO has been submitted Succefully',true);
                return $this->goHome();
            }else{
                Yii::$app->session->setFlash('error',$result,true);
                return $this->goHome();
            }
        }
        // if(Yii::$app->request->isAjax){
            return $this->render('create', [
                'model' => $model,
                'MemberAccounts'=> [], //$this->getMemberAccounts(),
                'RequiredAttachements'=>$this->getWithdrawalAttachements(),
                'MyAttachedDocs'=>[]//$attachements
            ]);
        
       
    }

    public function getWithdrawalAttachements(){
        $res = [
            ['Id' => 1,'Name' => 'Copy of Id'],
        ];
        return $res;
    }


    public function actionCloseAccount(){

        $model = new AccountClosureCard();
        $service = Yii::$app->params['ServiceName']['AccountClosureCard'];

        if(Yii::$app->request->post() && $this->loadpost(Yii::$app->request->post()['AccountClosureCard'],$model)){
          
            $service = Yii::$app->params['codeUnits']['CreditPortalManagement'];
            $data = [
               'memberNo'=> Yii::$app->user->identity->{'No_'},
               'accountNo'=>$model->Account_No,
               'closingReason'=>$model->Closing_Reason,
               'speedProcess'=>(boolean)$model->Speed_Process,
               'responseCode'=>'',
               'responseMessage'=>''
            ];
           
            $PostResult = Yii::$app->navhelper->PortalWorkFlows($service,$data,'CreateAccountWithdrawalRequest');
            //   echo '<pre>';
            // print_r($PostResult);
            // exit;

            if(!is_string($PostResult)){
                if($PostResult['responseCode'] == 00){ //success Manenos
                    Yii::$app->session->setFlash('success',$PostResult['responseMessage']);
                    return $this->goHome();
                }
                Yii::$app->session->setFlash('error',$PostResult['responseMessage']);
                return $this->goHome();
            }
                
        }

        // if(Yii::$app->request->isAjax){
            return $this->render('create-close', [
                'model' => $model,
                'MemberAccounts'=>$this->getMemberAccounts(),
            ]);
        // }
       
    }

    
    public function getMemberAccounts(){
        $service = Yii::$app->params['ServiceName']['Vendors'];
        $filter = [
            'Supplier_Type'=>'SACCO',
            'Member_No'=>Yii::$app->user->identity->{'No_'},
            'Share_Capital_Account'=>0
        ];
        $res = [];
        $MemberAccounts = \Yii::$app->navhelper->getData($service, $filter);
        foreach($MemberAccounts as $MemberAccount){
            if(!empty($MemberAccount->No))
            $res[] = [
                'Code' => isset($MemberAccount->No)?$MemberAccount->No:'Not Set',
                'Name' => isset($MemberAccount->Name)?$MemberAccount->Name:'Not Set'
            ];
        }

        return $res;
    }



    public function getLoanProducts(){
        $service = Yii::$app->params['ServiceName']['LoanProducts'];
        $res = [];
        $LoanProducts = \Yii::$app->navhelper->getData($service);
        foreach($LoanProducts as $LoanProduct){
            if(!empty($LoanProduct->Product_Code || $LoanProduct->Product_Description))
            $res[] = [
                'Code' => $LoanProduct->Product_Code,
                'Name' => $LoanProduct->Product_Description
            ];
        }

        return $res;
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
