<?php
namespace app\controllers;
use app\models\GuarantorReplacements;
use app\models\LoanApplicationHeader;
use Yii;
use yii\filters\AccessControl;
use yii\filters\ContentNegotiator;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\Controller;
use yii\web\Response;
use app\models\AcceptRejectGuarantorshipForm;
use yii\web\UploadedFile;

class GuarantorReplacementsController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => [
                'create', 'create-security',
                'get-loan-securities', 'accep', 'accept',
                'reject', 'guarantorship-requests' ,'update',
                'update-security', 'delete', 'get-loan-securities',
                'get-members', 'view'
            ],
                'rules' => [
                     [
                        'actions' => [
                            'create', 'create-security',
                            'get-loan-securities', 'accep', 'accept',
                            'reject', 'guarantorship-requests' ,'update',
                            'update-security', 'delete', 'get-loan-securities',
                            'get-members', 'view'
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
                'only' => ['getkins', 'get-members', 'get-loan-securities'],
                'formatParam' => '_format',
                'formats' => [
                    'application/json' => Response::FORMAT_JSON,
                    //'application/xml' => Response::FORMAT_XML,
                ],
            ]
        ];
    }

 

    public function actionCreate($DocNum, $OutGoing){

        $model = new GuarantorReplacements();
        $service = Yii::$app->params['ServiceName']['OnlineGuarantorSubRequests'];
        $model->Document_No =urldecode( $DocNum);
        if(Yii::$app->request->post() && $this->loadpost(Yii::$app->request->post()['GuarantorReplacements'],$model)){
            $model->Document_No = $DocNum;
            $model->Guarantor_No = urldecode($OutGoing);

            $result = Yii::$app->navhelper->postData($service,$model);
            if(is_object($result)){
                Yii::$app->session->setFlash('success','Guarantor Added Successfully',true);
                return $this->redirect(Yii::$app->request->referrer);
            }else{
                Yii::$app->session->setFlash('error',$result,true);
                return $this->redirect(Yii::$app->request->referrer);

            }

        }

        if(Yii::$app->request->isAjax){
            return $this->renderAjax('create', [
                'model' => $model,
            ]);
        }
    }

    public function actionCreateSecurity($DocNum, $OutGoing){

        $model = new GuarantorReplacements();
        $service = Yii::$app->params['ServiceName']['GuarantorReplacements'];
        $model->Document_No = $DocNum;
        $model->Type == 'Security';
        $model->isNewRecord = true;
        $content = '';
        if(Yii::$app->request->post() && $this->loadpost(Yii::$app->request->post()['GuarantorReplacements'],$model)){
            
         
            $model->Document_No = $DocNum;
            $model->Type = 'Security';
            $model->FileName = $model->Replace_With;
            $model->Member_No = urldecode($OutGoing);
            $replaceWith = Yii::$app->request->post()['GuarantorReplacements']['Replace_With'];
            // echo '<pre>';
            // print_r(Yii::$app->request->post()['GuarantorReplacements']);
            // exit;
            $model->docFile = UploadedFile::getInstance($model, 'docFile');
            if($model->upload() == true){
                $model->Document_No = $DocNum;
                $model->Replace_With = null;
                $result = Yii::$app->navhelper->postData($service,$model);
                if(is_object($result)){
                    $model->Document_No = $DocNum;
                    $model->Replace_With = $replaceWith;
                    $model->Key = $result->Key;
                    $updateResult = Yii::$app->navhelper->updateData($service,$model);
                    // echo '<pre>';
                    // print_r($updateResult);
                    // exit;
                    if(is_object($updateResult)){
                        Yii::$app->session->setFlash('success','Sucurity Added Successfully',true);
                        return $this->redirect(Yii::$app->request->referrer);
                    }
                }else{
                    Yii::$app->session->setFlash('error',$result,true);
                    return $this->redirect(Yii::$app->request->referrer);
    
                }
            }
            return $this->redirect(Yii::$app->request->referrer);

        }

        if(Yii::$app->request->isAjax){
            return $this->renderAjax('create-security', [
                'model' => $model,
                'LoanSecurities'=>$this->GetLoanSecurities(),
                'content'=>$content
            ]);
        }
    }

    public function GetLoanSecurities(){
        $service = Yii::$app->params['ServiceName']['LoanSecurities'];
        $result = \Yii::$app->navhelper->getData($service);
        foreach($result as $LoanProduct){
            if(!empty($LoanProduct->Code))
            $res[] = [
                'Code' => $LoanProduct->Code,
                'Name' => $LoanProduct->Description
            ];
        }
        return $res;
    }

    public function Accep($No){
        $service = Yii::$app->params['ServiceName']['PortalFactory'];

        $data = [
            'loanNo' => $No,
            'sendMail' => 1,
            'approvalUrl' => '',
        ];

        Yii::$app->navhelper->PortalWorkFlows($service,$data,'LoanPaymentSchedule');
        return true;
    }

    public function actionAccept($Key){
        $model = new AcceptRejectGuarantorshipForm();
        $service = Yii::$app->params['ServiceName']['OnllineGuarantorRequests'];
        $result = Yii::$app->navhelper->readByKey($service, urldecode($Key));
        $model->Accepted = true;

        if(Yii::$app->request->post() && $this->loadpost(Yii::$app->request->post()['AcceptRejectGuarantorshipForm'],$model)){
       
            
            $service = Yii::$app->params['codeUnits']['CreditPortalManagement'];
            $data = [
                'loanNo' => $model->Loan_No,
                'memberNo' => Yii::$app->user->identity->{'No_'},
                'amount' =>$model->Loan_Principal,
                'accepted'=>1,
                'rejected'=>0,
                'responseCode'=>'',
                'responseMessage'=>''
            ];

            // echo '<pre>';
            // print_r($data);
            // exit;

            $PostResult = Yii::$app->navhelper->PortalWorkFlows($service,$data,'ProcessOnlineGuarantorRequest');

            if(!is_string($PostResult)){
                if($PostResult['responseCode'] == '01'){ //Error Manenos
                    Yii::$app->session->setFlash('error',$PostResult['responseMessage']);
                    return $this->redirect(Yii::$app->request->referrer);
                }
                $Message = ' Hello '. $model->Member_Name .' We have Received Your Acceptance To Guarantee '. $model->Loan_Principal;
                $this->SendSMS($Message, $model->PhoneNo);
                Yii::$app->session->setFlash('success', 'Guarantorship Accepted Succesfully', true);
                return $this->redirect(Yii::$app->request->referrer);
            }

         
          

        }

        if(Yii::$app->request->isAjax){
            $model = $this->loadtomodel($result,$model);
            return $this->renderAjax('accept-reject', [
                'model' => $model,
            ]);
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

    
    public function actionReject($Key){
        $model = new AcceptRejectGuarantorshipForm();
        $service = Yii::$app->params['ServiceName']['OnllineGuarantorRequests'];
        $result = Yii::$app->navhelper->readByKey($service, urldecode($Key));
        $model->Accepted = false;
        if(Yii::$app->request->post() && $this->loadpost(Yii::$app->request->post()['AcceptRejectGuarantorshipForm'],$model)){
       
            $service = Yii::$app->params['codeUnits']['CreditPortalManagement'];
            $data = [
                'loanNo' => $model->Loan_No,
                'memberNo' => Yii::$app->user->identity->{'No_'},
                'amount' =>$model->Loan_Principal,
                'accepted'=>0,
                'rejected'=>1,
                'responseCode'=>'',
                'responseMessage'=>''
            ];

            // echo '<pre>';
            // print_r($data);
            // exit;

            $PostResult = Yii::$app->navhelper->PortalWorkFlows($service,$data,'ProcessOnlineGuarantorRequest');

            if(!is_string($PostResult)){
                if($PostResult['responseCode'] == '01'){ //Error Manenos
                    Yii::$app->session->setFlash('error',$PostResult['responseMessage']);
                    return $this->redirect(Yii::$app->request->referrer);
                }
                $Message = ' Hello '. $model->Member_Name .' We have Received Your Rejectance To Guarantee ';
                $this->SendSMS($Message, $model->PhoneNo);
                Yii::$app->session->setFlash('success', 'Guarantorship Rejected Succesfully', true);
                return $this->redirect(['index', 'Key'=>$model->Key]);
            }

         
          

        }

        if(Yii::$app->request->isAjax){
            $model = $this->loadtomodel($result,$model);
            return $this->renderAjax('accept-reject', [
                'model' => $model,
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
        $model = new OnllineGuarantorRequests();
        $LoanModel = $this->GetLoanDetails($Key);
        return $this->render('index', ['model' => $model, 'LoanModel'=>$LoanModel]);

    }

    public function actionGuarantorshipRequests(){
        $model = new OnllineGuarantorRequests();
        $service = Yii::$app->params['ServiceName']['OnllineGuarantorRequests'];
        $filter = [
            'Member_No' => Yii::$app->user->identity->{'No_'},
            'Loan_Submitted'=>1,
            'Status'=>'New',

        ];
        $GuarantorshipRequests = \Yii::$app->navhelper->getData($service,$filter);
        return $this->render('guarantorship-requests', ['model' => $model, 'GuarantorshipRequests'=>$GuarantorshipRequests]);

    }

    public function actionUpdate(){
        $model = new GuarantorReplacements();
        $service = Yii::$app->params['ServiceName']['OnlineGuarantorSubRequests'];
        $filter = [
            'Key' => urldecode(Yii::$app->request->get('Key')),
        ];
        if(Yii::$app->request->post() && $this->loadpost(Yii::$app->request->post()['GuarantorReplacements'],$model)){
            $result = Yii::$app->navhelper->updateData($service,$model);
            if(is_object($result)){
                Yii::$app->session->setFlash('success','Updated Successfully');
                return $this->redirect(Yii::$app->request->referrer);
            }else{
                Yii::$app->session->setFlash('error',$result);
                return $this->redirect(Yii::$app->request->referrer);
            }

        }
        $result = Yii::$app->navhelper->readByKey($service, urldecode(Yii::$app->request->get('Key')));

        if(Yii::$app->request->isAjax){
            $model = $this->loadtomodel($result,$model);
            return $this->renderAjax('update', [
                'model' => $model,

            ]);
        }

 
    }
    public function actionUpdateSecurity(){
        $model = new GuarantorReplacements();
        $service = Yii::$app->params['ServiceName']['GuarantorReplacements'];
        $filter = [
            'Key' => urldecode(Yii::$app->request->get('Key')),
        ];
        if(Yii::$app->request->post() && $this->loadpost(Yii::$app->request->post()['LoanAppSecurities'],$model)){
            $result = Yii::$app->navhelper->updateData($service,$model);
            if(is_object($result)){
                Yii::$app->session->setFlash('success','Updated Successfully');
                return $this->redirect(Yii::$app->request->referrer);
            }else{
                Yii::$app->session->setFlash('error',$result);
                return $this->redirect(Yii::$app->request->referrer);
            }

        }
        $result = Yii::$app->navhelper->readByKey($service, urldecode(Yii::$app->request->get('Key')));
        $content = $model->read($result->Document_No);

        $model->isNewRecord = false;
        if(Yii::$app->request->isAjax){
            $model = $this->loadtomodel($result,$model);
            return $this->renderAjax('update-security', [
                'model' => $model,
                'LoanSecurities'=>$this->GetLoanSecurities(),
                'content'=>$content,
            ]);
        }

 
    }


    public function actionDelete(){
        $service = Yii::$app->params['ServiceName']['OnlineGuarantorSubRequests'];
        $result = Yii::$app->navhelper->deleteData($service,urldecode(Yii::$app->request->get('Key')));
        if(!is_string($result)){
            Yii::$app->session->setFlash('success','Removed Successfully .');
            return $this->redirect(Yii::$app->request->referrer);
        }else{
            Yii::$app->session->setFlash('error',$result);
            return $this->redirect(Yii::$app->request->referrer);
        }
    }

    public function actionGetLoanSecurities($SecurityType){
        $service = Yii::$app->params['ServiceName']['LoanSecurities'];
        $filter = [];
        $arr = [];
        $i = 0;
        $result = \Yii::$app->navhelper->getData($service,$filter);
        foreach($result as $res){
            if(isset($res->Code)){
                ++$i;
                $arr[] = [
                    'Code' => @$res->Code,
                    'Description' => @$res->Description
                ];
            }
                
        }
        return $arr;
    }

    public function actionGetMembers($SecurityType){
        $service = Yii::$app->params['ServiceName']['Members'];
        $filter = [];
        $arr = [];
        $i = 0;
        $result = \Yii::$app->navhelper->getData($service,$filter);
        foreach($result as $res){
                ++$i;
                $arr[$i] = [
                    'Code' => $res->No,
                    'Description' => $res->Name
                ];
        }
        return $arr;
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
        $leaveModel = new MemberApplication_KINs();
        $model = $this->loadtomodel($leave[0],$leaveModel);


        return $this->render('view',[
            'model' => $model,
            'leaveTypes' => ArrayHelper::map($leaveTypes,'Code','Description'),
            'relievers' => ArrayHelper::map($employees,'No','Full_Name'),
        ]);
    }

    public function actionGetkins(){
        $service = Yii::$app->params['ServiceName']['MemberApplication_KINs'];
        $filter = [
            'App_No' => Yii::$app->user->identity->ApplicationId,
        ];
        $kins = Yii::$app->navhelper->getData($service,$filter);

        // echo '<pre>';
        // print_r($kins);
        // exit;


        $result = [];
        $count = 0;
      
        if(!is_object($kins)){
            foreach($kins as $kin){

                if(empty($kin->First_Name) && empty($kin->Last_Name) && $kin->Type == '_blank_' ){ //Useless KIn this One
                    continue;
                }
                ++$count;
                $link = $updateLink =  '';
                if(Yii::$app->user->identity->memberApplicationStatus == 'New'){
                    $updateLink = Html::a('Edit Kin',['update','Key'=> urlencode($kin->Key) ],['class'=>'update btn btn-info btn-md']);
                    $link = Html::a('Remove Kin',['delete','Key'=> urlencode($kin->Key) ],['class'=>'btn btn-danger btn-md']);
                }else{
                    $updateLink = Html::a('Edit Kin',['#','Key'=> urlencode($kin->Key) ],['class'=>'btn btn-info btn-md']);
                    $link = '';
                }
                $result['data'][] = [
                    'index' => $count,
                    'Type' => $kin->Type,
                    'First_Name' => !empty($kin->First_Name)?$kin->First_Name:'',
                    'Middle_Name' => !empty($kin->Middle_Name)?$kin->Middle_Name:'',
                    'DOB' => !empty($kin->DOB)?$kin->DOB:'',
                    'Allocation_Percent' => !empty($kin->Middle_Name)?$kin->Allocation_Percent:'',
                    'Update_Action' => $updateLink,
                    'Remove' => $link
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