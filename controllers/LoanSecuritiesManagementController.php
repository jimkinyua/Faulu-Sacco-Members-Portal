<?php

namespace app\controllers;
use app\models\SecurityManagementCard;
use phpDocumentor\Reflection\Types\Object_;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\filters\ContentNegotiator;
use Yii;
use yii\helpers\Html;

class LoanSecuritiesManagementController extends \yii\web\Controller
{
    public function behaviors(){
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => [
                    'get-substituition-requests',
                     'create','index',
                     'submit', 'get-substituition-requests',
                     'update', 'create' ,
                    ],
                'rules' => [
                   
                     [
                        'actions' => [
                            'get-substituition-requests',
                            'create','index',
                            'submit', 'get-substituition-requests',
                            'update', 'create' ,
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
                'only' => ['get-substituition-requests'],
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

    public function actionApproved(){
        return $this->render('approved');
    }

    public function GetLoanDetails($LoanKey){
        $model = new LoanApplicationHeader();
        $service = Yii::$app->params['ServiceName']['LoanApplicationHeader'];
        $result = Yii::$app->navhelper->readByKey($service, urldecode($LoanKey));
       return $model = $this->loadtomodel($result,$model);
    }

    public function actionPaymentSchedule(){

        $CodeUnitService = Yii::$app->params['ServiceName']['PortalReports'];
        $service = Yii::$app->params['ServiceName']['LoanApplicationHeader'];
        $result = Yii::$app->navhelper->readByKey($service, urldecode(Yii::$app->request->get('Key')));
        $data = [
            'loanN' =>$result->Application_No ,
            ];
        $path = Yii::$app->navhelper->PortalReports($CodeUnitService,$data,'IanGenerateLoanAppraisal');
        exit('Work in Progress .............');
        // Yii::$app->recruitment->printrr($path);
        if(is_file($path['return_value'])){
            $binary = file_get_contents($path['return_value']);
            $content = chunk_split(base64_encode($binary));
            //delete the file after getting it's contents --> This is some house keeping
            //unlink($path['return_value']);
            return $this->render('loan-appraisal',[
                'report' => true,
                'content' => $content,
           ]);
        }

        Yii::$app->session->setFlash('error', 'Unable Generate Loan Appraisal Report. Try Again Later ');
        return $this->redirect(Yii::$app->request->referrer);

    }

    public function actionSubmit($DocNum){
        $model = new SecurityManagementCard();
        // echo '<pre>';
        // print_r($LoanModel);
        // exit;
            $service = Yii::$app->params['ServiceName']['PortalFactory'];
            $data = [
                'no' => $DocNum,
            ];

            $result = Yii::$app->navhelper->PortalWorkFlows($service,$data,'IanchangeSecurityStatus');
            
            if(!is_string($result)){
                Yii::$app->session->setFlash('success', 'Loan Submitted  Successfully.', true);
                return $this->redirect(['index']);
            }else{

                Yii::$app->session->setFlash('error', $result);
                // return $this->redirect(['view','No' => $No]);
                return $this->redirect(['index']);
            }

    }

    public function SendSMS($Message, $PhoneNo){
        //Todo: Clean The Phone Number to Form 07... 0r 2547....
        // exit($PhoneNo);
        $url =Yii::$app->params['SMS']['BaseURL'];
        $ch = curl_init($url);
        $BearerToken =Yii::$app->params['SMS']['AcessToken'];

        $data = [
            'sender'=> 'MHASIBU',
            'message'=> $Message,
            'phone'=> $PhoneNo,
        ];
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: Bearer  '. $BearerToken));
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        $response = curl_exec($ch);
        $result = json_decode($response);
        curl_close($ch); // Close the connection
        if(empty($result->status)){ //Error
            Yii::$app->session->setFlash('error', 'Unable to Send a Message to the ');
            return $this->redirect(Yii::$app->request->referrer);
        }
        return true;

    }

    public function NotifyGuarantors($LoanNo){
       $Guarantors = $this::getGuarantors($LoanNo);
        if($Guarantors){
                return $Guarantors;
        }
        return false;
    }
    static function getGuarantors($LoanNo){
        $service = Yii::$app->params['ServiceName']['OnllineGuarantorRequests'];
        $filter = [
            'Loan_No' => $LoanNo,
        ];
        $Guarantors = Yii::$app->navhelper->getData($service,$filter);
        if(!is_object($Guarantors)){
            return $Guarantors;
        }
        return false;
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

   
    public function actionGetSubstituitionRequests(){
        $service = Yii::$app->params['ServiceName']['SecurityManagements'];

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
                $updateLink = Html::a('View Details',['update','Key'=> urlencode($loan->Key) ],['class'=>'btn btn-info btn-md']);
                $result['data'][] = [
                    'index' => $count,
                    'Type' => @$loan->Type,
                    'Created_On' => @$loan->Created_On,
                    'Created_By' => !empty($loan->Created_By)?$loan->Created_By:'',
                    'Update_Action' => $updateLink,
                ];
            }
        
        }
           
      

        return $result;
    }

    public function actionUpdate(){
        
        $service = Yii::$app->params['ServiceName']['SecurityManagementCard'];
        $filter = [
            'Key' => urldecode(Yii::$app->request->get('Key')),
        ];
        $result = Yii::$app->navhelper->readByKey($service, urldecode(Yii::$app->request->get('Key')));

        $model = new SecurityManagementCard();

        //load nav result to model

        if(Yii::$app->request->post() && $this->loadpost(Yii::$app->request->post()['SecurityManagementCard'],$model)){       
            // $model->Application_Category = 0;
            $result = Yii::$app->navhelper->updateData($service,$model);
            // echo '<pre>';
            // print_r($model);
            // exit;
            if(is_string($result)){
                Yii::$app->session->setFlash('error',$result);
                return $this->redirect(Yii::$app->request->referrer);
            }else{
                Yii::$app->session->setFlash('success','Details Saved Successfully',true);
                return $this->redirect(Yii::$app->request->referrer);
            }

        }
            $model = $this->loadtomodel($result,$model);
            return $this->render('update', [
                'model' => $model,
                'MemberLoans'=>$this->getMemberLoans(),
            ]);

    }

    public function actionSubSectors($id){
        $service = Yii::$app->params['ServiceName']['SubSectorNames'];
        $filter = [
            'Sector_Code' => urldecode($id),
        ];
        $res = [];
        $SubSectorNames = \Yii::$app->navhelper->getData($service, $filter);
        echo "<option value=''>-- Select Option --</option>";
        foreach($SubSectorNames as $SubSector){
            if(!empty($SubSector->Subsector_Code || $SubSector->Subsector_Name)){
                echo "<option value='".$SubSector->Subsector_Code."'>".$SubSector->Subsector_Name."</option>";
            }
        }
    }

    public function actionSubSubSectors($SubSectorCode){
        $service = Yii::$app->params['ServiceName']['SubSubSectors'];
        $filter = [
            'Subsector_Code' => urldecode($SubSectorCode),
        ];
        $res = [];
        $SubSubSectors = \Yii::$app->navhelper->getData($service, $filter);
        echo "<option value=''>-- Select Option --</option>";
        foreach($SubSubSectors as $SubSubSector){
            if(!empty($SubSubSector->Sub_Subsector_Code || $SubSubSector->Subsector_Name)){
                echo "<option value='".$SubSubSector->Sub_Subsector_Code."'>".$SubSubSector->Subsector_Name."</option>";
            }
        }
    }


    public function actionCreate(){
        $service = Yii::$app->params['ServiceName']['SecurityManagementCard'];
        $model = new SecurityManagementCard();
        $model->Portal_Status = 'New';

        if(Yii::$app->request->post() && $this->loadpost(Yii::$app->request->post()['SecurityManagementCard'],$model)){
            $model->Member_No = Yii::$app->user->identity->{'No_'};
            $result = Yii::$app->navhelper->postData($service,$model);
            if(is_object($result)){
                Yii::$app->session->setFlash('success','Added Successfully',true);
                return $this->redirect(['update', 'Key'=>$result->Key]);
            }else{
                Yii::$app->session->setFlash('error',$result,true);
                return $this->redirect(Yii::$app->request->referrer);
            }           
        }

        if(Yii::$app->request->isAjax){
            return $this->renderAjax('create', [
                'model' => $model,
                'MemberLoans'=>$this->getMemberLoans(),
            ]);
        }
    }

    public function getMemberLoans(){
        $service = Yii::$app->params['ServiceName']['LoansLookup'];
        $filter = [
            'Status'=>'Disbursed',
            'Member_No'=>Yii::$app->user->identity->{'No_'},
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
                'Name' => $LoanProduct->Application_No . ' || '. number_format($LoanProduct->Applied_Amount)
            ];
        }

        return $res;
    }

    public function getEconomicSectors(){
        $service = Yii::$app->params['ServiceName']['SubsectorClassifications'];
        $res = [];
        $EconomicSectors = \Yii::$app->navhelper->getData($service);
        if(is_object($EconomicSectors)){
            return $res;
        }
        foreach($EconomicSectors as $EconomicSector){
            if(!empty($EconomicSector->Sector_Code || $EconomicSector->Sector_Name))
            $res[] = [
                'Code' => $EconomicSector->Sector_Code,
                'Name' => $EconomicSector->Sector_Name
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
