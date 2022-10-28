<?php

namespace app\controllers;
use app\models\LoanApplicationHeader;
use app\models\MemberApplicationCard;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\filters\ContentNegotiator;
use Yii;
use yii\helpers\Html;
class ChildAccountController extends \yii\web\Controller{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['get-child-accounts', 'create','index'],
                'rules' => [
                    [
                        'actions' => ['signup'],
                        'allow' => true,
                        'roles' => ['?'],
                    ],
                     [
                        'actions' => ['get-child-accounts','index', 'create'],
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
                'only' => ['get-child-accounts'],
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

    public function actionSendForApproval($Key){
        $model = new LoanApplicationHeader();
        $LoanModel = $this->GetLoanDetails($Key);
        // echo '<pre>';
        // print_r($LoanModel);
        // exit;
        if(Yii::$app->request->post() && $this->loadpost(Yii::$app->request->post()['LoanApplicationHeader'],$model)){
            $service = Yii::$app->params['ServiceName']['PortalFactory'];
            $data = [
                'loanno' => $LoanModel->Application_No,
                'sendMail' => 1,
                'approvalUrl' => '',
            ];

            $result = Yii::$app->navhelper->PortalWorkFlows($service,$data,'Changelloanstatus');
            
            if(!is_string($result)){
                Yii::$app->session->setFlash('success', 'Loan Sent for Approval Successfully.', true);
                $guarantors =  $this->NotifyGuarantors($LoanModel->Application_No);
                $ApplicantData = Yii::$app->user->identity->getApplicantData();
                if($guarantors){
                    foreach($guarantors as $guarantor){
                        // echo '<pre>';
                        // print_r($guarantor);
                        // exit;
                        $Message = ' Hello '. $guarantor->Member_Name . '. Member '. $ApplicantData->Full_Names . ' Has Added You as a Guarantor for their loan Application. Kindly Log on to the Members Portal (http://197.248.217.154:8060/site/login) To Approve The Loan.';
                        $this->SendSMS($Message, $guarantor->PhoneNo);
                    }
                }
                return $this->redirect(['index']);
            }else{

                Yii::$app->session->setFlash('error', $result);
                // return $this->redirect(['view','No' => $No]);
                return $this->redirect(['index']);
            }
        }

        return $this->render('Confirm', ['model' => $model, 'LoanModel'=>$LoanModel]);
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

    public function actionGetChildAccounts(){
        $service = Yii::$app->params['ServiceName']['MemberApplication'];
        $filter = [
            'MemberNo' => Yii::$app->user->identity->{'No_'},
            'Member_Category'=>Yii::$app->params['SystemConfigs']['ChildAccount']
        ];
        $childAccounts = Yii::$app->navhelper->getData($service,$filter);

        // echo '<pre>';
        // print_r($childAccounts);
        // exit;


        $result = [];
        $count = 0;
      
        if(!is_object($childAccounts)){
            foreach($childAccounts as $childAccount){

                // if(empty($loan->First_Name) && empty($loan->Last_Name) && $loan->Type == '_blank_' ){ //Useless loan this One
                //     continue;
                // }
                ++$count;
                $link = $updateLink =  '';
                if($childAccount->Portal_Status == 'New'){
                    $status = 'Application';
                }else{
                    $status = $childAccount->Portal_Status;
                }
                $updateLink = Html::a('View Details',['/profile','Key'=> urlencode($childAccount->Key) ],['class'=>'btn btn-info btn-md']);
                $result['data'][] = [
                    'index' => $count,
                    'First_Name' => !empty($childAccount->First_Name)?$childAccount->Last_Name:'',
                    'Last_Name'=> !empty($childAccount->Last_Name)?$childAccount->Last_Name:'', 
                    'Gender' => !empty($childAccount->Gender)?$childAccount->Gender:'',
                    'Status'=>$status,
                    'Update_Action' => $updateLink,
                ];
            }
        
        }
           
      

        return $result;
    }

    public function actionUpdate(){
        
        $service = Yii::$app->params['ServiceName']['MemberApplicationCard'];
        $filter = [
            'Key' => urldecode(Yii::$app->request->get('Key')),
        ];
        $result = Yii::$app->navhelper->readByKey($service, urldecode(Yii::$app->request->get('Key')));
        Yii::$app->session->set('ChildAccountNo',$result->Application_No);
        return $this->redirect(['/profile', 'Key'=>$result->Key]);

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

        $model = new MemberApplicationCard();
        $service = Yii::$app->params['ServiceName']['MemberApplicationCard'];


            $model->MemberNo = Yii::$app->user->identity->{'No_'};
            $model->Member_Category = Yii::$app->params['SystemConfigs']['ChildAccount'];

            $result = Yii::$app->navhelper->postData($service,$model);

            if(is_object($result)){

                Yii::$app->session->setFlash('success','Added Successfully',true);
                return $this->redirect(['update', 'Key'=>$result->Key]);

            }else{
                Yii::$app->session->setFlash('error',$result,true);
                return $this->redirect(Yii::$app->request->referrer);

            }

        if(Yii::$app->request->isAjax){
            return $this->renderAjax('create', [
                'model' => $model,
                'Members'=>$this->getMembers(),

            ]);
        }
    }


    public function actionSetLoanProduct(){
        $model = new LoanApplicationHeader();
        $service = Yii::$app->params['ServiceName']['LoanApplicationHeader'];

        $filter = [
            'Application_No' => Yii::$app->request->post('LoanNo')
        ];
        $request = Yii::$app->navhelper->getData($service, $filter);

        if(is_array($request)){
            Yii::$app->navhelper->loadmodel($request[0],$model);
            $model->Key = $request[0]->Key;
            $model->Loan_Product = Yii::$app->request->post('LoanProduct');
        }

        $result = Yii::$app->navhelper->updateData($service,$model);

        Yii::$app->response->format = \yii\web\response::FORMAT_JSON;

        return $result;

    }

    public function actionSetLoanAppliedAmount(){
        $model = new LoanApplicationHeader();
        $service = Yii::$app->params['ServiceName']['LoanApplicationHeader'];

        $filter = [
            'Application_No' => Yii::$app->request->post('LoanNo')
        ];
        $request = Yii::$app->navhelper->getData($service, $filter);

        if(is_array($request)){
            Yii::$app->navhelper->loadmodel($request[0],$model);
            $model->Key = $request[0]->Key;
            $model->Applied_Amount = Yii::$app->request->post('Applied_Amount');
        }

        $result = Yii::$app->navhelper->updateData($service,$model);
    
        //refresh Here
        $this->PaymentSchedule(Yii::$app->request->post('LoanNo'));
        $request = Yii::$app->navhelper->getData($service, $filter);


        Yii::$app->response->format = \yii\web\response::FORMAT_JSON;

        return $request[0];

    }

    public function actionSetLoanRepaymentPeriod(){
        $model = new LoanApplicationHeader();
        $service = Yii::$app->params['ServiceName']['LoanApplicationHeader'];

        $filter = [
            'Application_No' => Yii::$app->request->post('LoanNo')
        ];
        $request = Yii::$app->navhelper->getData($service, $filter);

        if(is_array($request)){
            Yii::$app->navhelper->loadmodel($request[0],$model);
            $model->Key = $request[0]->Key;
            $model->Repayment_Period_M = Yii::$app->request->post('Repayment_Period_M');
        }

        $result = Yii::$app->navhelper->updateData($service,$model);
        if(is_string($result)){
            return $result;
        }else{
              //refresh Here
        $this->PaymentSchedule(Yii::$app->request->post('LoanNo'));
        $request = Yii::$app->navhelper->getData($service, $filter);
        Yii::$app->response->format = \yii\web\response::FORMAT_JSON;
        return $request[0];
        }
      

    }

    public function getLoanProducts(){
        $service = Yii::$app->params['ServiceName']['LoanProducts'];
        $res = [];
        $LoanProducts = \Yii::$app->navhelper->getData($service);
        if(is_object($LoanProducts)){
            return $res;
        }
        foreach($LoanProducts as $LoanProduct){
            if(!empty($LoanProduct->Product_Code || $LoanProduct->Product_Description))
            $res[] = [
                'Code' => $LoanProduct->Product_Code,
                'Name' => $LoanProduct->Product_Description
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
