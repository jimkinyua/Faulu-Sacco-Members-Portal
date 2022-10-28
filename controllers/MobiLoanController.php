<?php

namespace app\controllers;
use app\models\LoanApplicationHeader;
use phpDocumentor\Reflection\Types\Object_;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\filters\ContentNegotiator;
use Yii;
use yii\helpers\Html;
use app\models\MobiLoanForm;
use app\models\B2CPayments;
use app\models\user;
use app\models\ValidateTransaction;
use yii\widgets\ActiveForm;
class MobiLoanController extends \yii\web\Controller
{
    public function behaviors(){
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => [
                    'getloans', 'create','index',
                    'approved', 'payment-schedule',
                    'send-for-approval', 'update',
                    'sub-sectors','sub-sub-sectors',
                    'set-loan-product', 'set-loan-applied-amount',
                    'set-loan-repayment-period','get-approved-loans'
                ],
                'rules' => [
                     [
                        'actions' => [
                            'getloans', 'create','index',
                            'approved', 'payment-schedule',
                            'send-for-approval', 'update',
                            'sub-sectors','sub-sub-sectors',
                            'set-loan-product', 'set-loan-applied-amount',
                            'set-loan-repayment-period','get-approved-loans'
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
                'only' => ['getloans', 'get-approved-loans'],
                'formatParam' => '_format',
                'formats' => [
                    'application/json' => Response::FORMAT_JSON,
                    //'application/xml' => Response::FORMAT_XML,
                ],
            ]
        ];
    }

    public function DisburseLoan($model, $RequestID){

        // exit('Here');

        $Remarks = 'Mobi Loan Disbursement';
        $Ocassion = 'Loan Disbursement';
        $PhoneNo =  ltrim(Yii::$app->user->identity->{'Phone No_'}, '+254');
        $charges = $this->getProcessingCharges('L25', $model->{'Requested Amount'});
        $QualifiedAmount = isset($model->{'Qualified Amount'})?$model->{'Qualified Amount'}:'';
      

        $AmountToDisburse = number_format(($model->{'Requested Amount'} - $charges['return_value']), 2);
    

        $SendMoneyRequest =  Yii::$app->MpesaIntergration->SendMoneyToClient($PhoneNo, $AmountToDisburse,$Remarks,$Ocassion,$RequestID, $QualifiedAmount);
          
        

        if(isset($SendMoneyRequest->ConversationID) && $SendMoneyRequest->ResponseCode == 0){ //Request was Accepted
           $result =  $this->LogTransaction($SendMoneyRequest, $model);
            if(is_object($result) ){
                Yii::$app->session->setFlash('success', 'We have received your Mobi Loan Application. We will disburse shortly');
                return $this->goHome();
            }else{
                Yii::$app->session->setFlash('error', $result);
                return $this->goHome();
            }

        }

        if(isset($SendMoneyRequest->errorCode)){
            //Reverse Loan Posting
            $data = [
                'requestID'=> $RequestID,
                'responseCode'=> '',
                'responseMessage'=> '',
                'errorMessage'=> '',
            ];
    
           $this->ReverseLoanPosting($data);

            Yii::$app->session->setFlash('error', 'We are experiencing a techincal issue with the Mpesa. Kindly try again after a while');
            return $this->goHome();

        }

    }

    public function ReverseLoanPosting($data){
        $service = Yii::$app->params['ServiceName']['MBranch'];

        $PostResult = Yii::$app->navhelper->PortalWorkFlows($service,$data,'ProcessReversal');
        if(!is_string($PostResult)){
           if($PostResult['responseCode'] != '00'){ //Error Manenos   
            return json_decode($PostResult['responseMessage']);                   
           }
           return true;
       }
       return $PostResult;                   
    }

    public function PostLoanApplicationOnNavision($model){
        $data = array(
            'requestid'=>$model->{'Req ID'},
            'phoneNo'=> Yii::$app->user->identity->{'Phone No_'},
            'eLoanCode'=> 'L25',
            'amount'=> $model->{'Requested Amount'},
            'installments'=> 1,
            'responseCode'=>'',
            'responseMessage'=>'',
            'errorMessage' =>'',
        );

        $service = Yii::$app->params['ServiceName']['MBranch'];
        $PostResult = Yii::$app->navhelper->PortalWorkFlows($service,$data,'ApplyLoan');
    

        if(!is_string($PostResult)){
            if($PostResult['responseCode'] != '00'){ //Error Manenos                      
                Yii::$app->session->setFlash('error', $PostResult['errorMessage']);
                $Message = 'An Error has occured during posting of a loan'. $PostResult['errorMessage'];
                $this->SendSMS($Message, '0710467646');
                return $this->goHome();
            }else{
                //We Can disburse.
                
                return $this->DisburseLoan($model, $model->{'Req ID'});
            }
            
        }

        Yii::$app->session->setFlash('error', $PostResult);
        return $this->goHome();

    }

    public function LogTransaction($SendMoneyRequest, $model){
        $service = Yii::$app->params['ServiceName']['B2CTransactions'];

        $data = [
            'ConversationID'=> $SendMoneyRequest->ConversationID,
            'PhoneNo'=> Yii::$app->user->identity->{'Phone No_'},
            'QualifiedAmount'=> (int)isset($model->{'Qualified Amount'})?$model->{'Qualified Amount'}:0,
            'Requested_Amount'=> $model->{'Requested Amount'},
            'MemberNo'=> Yii::$app->user->identity->{'No_'},
            'Failure_Message'=> '',
            'Error_Code'=> '',
            'Mpesa_Code'=> '',
            'Completed_At'=> '',
            'Requested_At'=> time(),
            'Mpesa_Charges'=> 0,
            'Phone_Registered'=> '',
            'Payed_To'=> '',
            'Working_Balance'=> '',
            'Utility_Balance'=> '',
            'Status'=> '',
            'Navision_Error'=> '',
            'Disbursed_Amount'=> '',
        ];

        // $model->Member_No = Yii::$app->user->identity->{'No_'};
        return Yii::$app->navhelper->postData($service,$data);
    
    }

    public function actionValidateOtp($VtyieYETRg){
        $ValidateTransactionModel = new ValidateTransaction();

        if (Yii::$app->request->isAjax) {
            
            if ( $this->loadpost(Yii::$app->request->post()['ValidateTransaction'],$ValidateTransactionModel) && $ValidateTransactionModel->validate()) {
                $user = $this->verifyTransactionToken($ValidateTransactionModel->Code);
                if (isset($user[0]['description'])) {
                    return $this->asJson(['error' => true, 'message'=>$user[0]['description']]);
                }
                
                $B2CPayments = new B2CPayments();
                $payment = $B2CPayments::findOne([
                    'MemberNo'=>Yii::$app->user->identity->{'No_'},
                    'id'=>urldecode($VtyieYETRg)
                ]);
                return  $this->PostLoanApplicationOnNavision($payment);
            }
            $result = [];
            // The code below comes from ActiveForm::validate(). We do not need to validate the model
            // again, as it was already validated by validate(). Just collect the messages.
            foreach ($ValidateTransactionModel->getErrors() as $attribute => $errors) {
                $result[yii\helpers\Html::getInputId($ValidateTransactionModel, $attribute)] = $errors;
            }
            return $this->asJson(['validation' => $result]);

        }
        return $this->render('validate-otp', [
            'ValidationModel'=>$ValidateTransactionModel,
            'VtyieYETRg'=>urldecode($VtyieYETRg)
        ]);

        


     

       

    }

    public function verifyTransactionToken($token){

        $_user = User::verifyTransactionToken($token);
        
        if (!$_user) {
            return [
                [
                    'error'=>1,
                    'description'=>'The Token You Have Provided is Incorrect.'
                ]
            ];
        }

        if($_user->{'Transaction OTP Expiry'} < time() ){ //Token Expired
            return [
                [
                    'error'=>1,
                    'description'=>'Your token has expired. Kindly generate another one'
                ]
            ];
        }



       
    }
    
    

    public function actionIndex(){
        $model = new MobiLoanForm();

        $result =  $this->getQualifiedMobiLoanAmount(Yii::$app->user->identity->{'Phone No_'});

        if(is_string($result)){
            Yii::$app->session->setFlash('error', $result);
            return $this->redirect(['/member-profile', 'Key'=>Yii::$app->user->identity->getMemberData()->Key]);
        }

        if($result['Qualifies'] == false){
            Yii::$app->session->setFlash('error', $result['Reason']);
            return $this->redirect(['/member-profile', 'Key'=>Yii::$app->user->identity->getMemberData()->Key]);
        }

        $model->QualifiedAmount =  intval( str_replace( ',', '', $result['QualifiedAmount'] ) );

        
        if (Yii::$app->request->isAjax) {

                if ($this->loadpost(Yii::$app->request->post()['MobiLoanForm'], $model) && $model->validate()) {

                    $RequestID = 'Mobi-REQ-'.time();
                    //Save Trans for later 
                    $B2CPayments = new B2CPayments();
                    $B2CPayments->PhoneNo = Yii::$app->user->identity->{'Phone No_'};
                    $B2CPayments->{'Requested Amount'} = $model->AppliedAmount;
                    $B2CPayments->{'MemberNo'} = Yii::$app->user->identity->{'No_'};
                    $B2CPayments->{'Status'} = 'Requested';
                    $B2CPayments->{'Requested At'} = time();
                    $B2CPayments->{'Req ID'} = $RequestID; 
                    $B2CPayments->{'Qualified Amount'} = $model->QualifiedAmount;
                    
                    if($model->AppliedAmount > $model->QualifiedAmount){
                        Yii::$app->session->setFlash('error', 'You Do not Qualify for '. number_format($model->AppliedAmount));
                        return $this->redirect(['/member-profile', 'Key'=>Yii::$app->user->identity->getMemberData()->Key]);
                    }
                    
                    if($B2CPayments->save()){
                        if($this->SendOTP($model) == true){
                            return $this->asJson(
                                [
                                    'success' => true,
                                    'VtyieYETRg'=>$B2CPayments->id
                                ]
                            );

                            //Redirect To Validate OTP Page
                            return $this->redirect(['validate-otp', 
                            'VtyieYETRg'=>$B2CPayments->id,
                            ]);
                        }
                    }

                    return $this->asJson(['success' => true]);
                }
        
                $result = [];
                // The code below comes from ActiveForm::validate(). We do not need to validate the model
                // again, as it was already validated by validate(). Just collect the messages.
                foreach ($model->getErrors() as $attribute => $errors) {
                    $result[yii\helpers\Html::getInputId($model, $attribute)] = $errors;
                }
        
                return $this->asJson(['validation' => $result]);



            $this->loadpost(Yii::$app->request->post()['MobiLoanForm'],$model);
            Yii::$app->response->format = Response::FORMAT_JSON;
            if (ActiveForm::validate($model)){
                return ActiveForm::validate($model);
            }else{

                $charges=  $this->loadpost(Yii::$app->request->post()['MobiLoanForm'],$model);
        

            }
        }

     

        return $this->render('index', [
            'model'=> $model
        ]);
        
    }

    public function LogFailedB2CAttemptToERP($data){
        $service = Yii::$app->params['ServiceName']['B2CAttempts'];
        Yii::$app->navhelper->postData($service,$data);
    }

    public function SendOTP($model){
        $user = new user();
        $m = $user::findOne(['No_' => Yii::$app->user->identity->{'No_'}]);
        Yii::$app->session->set('MemberData', $m);
        if($user){
            $token = rand(1245, 5456);
            $m->{'Transaction OTP'} = $token ;
            $m->{'Transaction OTP Created At'}= time();
            $m->{'Transaction OTP Expiry'} =  time() + (2 * 60); // 2 Mins.
            if($m->save()){
                $message = 'Dear member, your OTP to for Mobi Loan on the portal is '. $token . '. Use this token to validate your request.';
                $PhoneNo = $m->{'Phone No_'};
                $this->SendSMS($message, $PhoneNo);
                return true;
            }
            return false;
        }

        return false;

        
    }

    public function getProcessingCharges($LoanProduct, $AppliedAmount){
        $CodeUnitService = Yii::$app->params['ServiceName']['MBranch'];
        $data = [
            'productCode' =>$LoanProduct,
            'appliedAmount' =>$AppliedAmount,
        ];
        return  Yii::$app->navhelper->PortalReports($CodeUnitService,$data,'GetProcessingCharges');
    }

    public  function getQualifiedMobiLoanAmount($PhoneNo){
        $CodeUnitService = Yii::$app->params['ServiceName']['MBranch'];
        $data = [
            'telephoneNo' =>$PhoneNo,
            'responseCode' =>'',
            'responseMessage' =>'',
        ];
        $qualifiedAmount = Yii::$app->navhelper->PortalReports($CodeUnitService,$data,'GetQualifiedMobiloanAmount');
        // echo '<pre>';
        // print_r($qualifiedAmount);
        // exit;

        // $response = [];
        if( isset($qualifiedAmount['responseCode'])  ){ //Do Not Qualify!

           if($qualifiedAmount['responseCode'] != 00){

                $Message = isset($qualifiedAmount['responseMessage'])?$qualifiedAmount['responseMessage']:'Unfortunately, you do not qualify for Mobi Loan. Contact Customer to get more information about this';

                $now = new \DateTime('Africa/Nairobi');
                $Timestamp = $now->format('Y-m-d-H:i:s');
                $data = [
                    'Member_No'=>Yii::$app->user->identity->{'No_'},
                    'Requested_At'=>$Timestamp,
                    'Rejection_Message'=>$Message
                ];

                $this->LogFailedB2CAttemptToERP($data);

                return $response =  [
                    'Qualifies'=>false,
                    'Reason'=>$Message
                ];

           } 
        }

        if(is_string($qualifiedAmount)){
            $now = new \DateTime('Africa/Nairobi');
            $Timestamp = $now->format('Y-m-d-H:i:s');

            $data = [
                'Member_No'=>Yii::$app->user->identity->{'No_'},
                'Requested_At'=>$Timestamp,
                'Rejection_Message'=>$qualifiedAmount
            ];

            $this->LogFailedB2CAttemptToERP($data);
            return $response =  [
                'Qualifies'=>false,
                'Reason'=>$qualifiedAmount
            ];
        }

        
        $DecodeResult = json_decode($qualifiedAmount['responseMessage']);

        return $response =  [
            'Qualifies'=>true,
            'Reason'=>'',
            'QualifiedAmount'=>$DecodeResult->QualifiedAmount
        ];


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

    public function actionSendForApproval($Key){
        $model = new LoanApplicationHeader();
        $LoanModel = $this->GetLoanDetails($Key);
        $service = Yii::$app->params['ServiceName']['LoanApplicationHeader'];


        if(Yii::$app->request->post() && $this->loadpost(Yii::$app->request->post()['LoanApplicationHeader'],$model)){
            
            //Check If Deliquency Status of Applicant
            // if($this->CheckDeliquencyStatus($LoanModel) === true){
            //     $LoanModel->HasDefaultRecordInCRB = 1;
            // }else{
            //     $LoanModel->HasDefaultRecordInCRB = 0;
            // }
            
            $LoanModel->AgreedToTerms = Yii::$app->request->post()['LoanApplicationHeader']['AgreedToTerms'];
            $result = Yii::$app->navhelper->updateData($service,$LoanModel);
            // echo '<pre>';
            // print_r($result);
            // exit;
            if(is_string($result)){
                Yii::$app->session->setFlash('error',$result);
                return $this->redirect(Yii::$app->request->referrer);
            }
            // If Listed, Tell Them to Attach the CRB Clearance Cert
            // if($result->HasDefaultRecordInCRB == 1 && empty($LoanModel->getCRBClearanceCertificates())){
            //     return $this->redirect(['clearance-ceriticate/index', 'Key'=>$result->Key]);
            // }
            

            // exit('ut');
            $service = Yii::$app->params['ServiceName']['PortalFactory'];
            $data = [
                'loanno' => $LoanModel->Application_No,
                'sendMail' => 1,
                'approvalUrl' => '',
            ];

            $result = Yii::$app->navhelper->PortalWorkFlows($service,$data,'Changelloanstatus');
            
            if(!is_string($result)){
                Yii::$app->session->setFlash('success', 'Loan Sent To The Guarantors  Successfully.', true);
                $guarantors =  $this->NotifyGuarantors($LoanModel->Application_No);
                $ApplicantData = Yii::$app->user->identity->getApplicantData();
                if($guarantors){
                    foreach($guarantors as $guarantor){
                        // echo '<pre>';
                        // print_r($guarantor);
                        // exit;
                        $Message = ' Hello '. $guarantor->Member_Name . '. Member '. $ApplicantData->Full_Names . ' Has Added You as a Guarantor for their loan Application. Kindly Log on to the Members Portal (http://197.248.217.154:8060/site/login) To Guarantee The Loan.';
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

    public function CheckDeliquencyStatus($LoanData){
        Yii::$app->params['MetroPol']['BaseURL'];
        $IndividualDeliquencyStatus = Yii::$app->MetroPolIntergration->actionMetroPolCheck($LoanData, Yii::$app->user->identity->getMemberData(), Yii::$app->params['MetroPol']['IdentityTypes']['NationalID']);
        
        if($LoanData->Application_Category == 'Business' || $LoanData->Application_Category == 'salary_and_Business'){ //Has Applied Loan For Loan
            if(!empty(Yii::$app->user->identity->getApplicantData()->Pin_Number)){ //registered Business This One
                $BusinessDeliquencyStatus = Yii::$app->MetroPolIntergration->actionMetroPolCheck($LoanData, Yii::$app->user->identity->getMemberData(), Yii::$app->params['MetroPol']['IdentityTypes']['BusinessRegistrationNo']);
            }else{
                $BusinessDeliquencyStatus = (object)[];
                $BusinessDeliquencyStatus->delinquency_code = '004'; //Can't Be Found Because It's not Registered
            }  
        }else{
            $BusinessDeliquencyStatus = (object)[];
            $BusinessDeliquencyStatus->delinquency_code = '004'; //Can't Be Found Because It's not Registered
        }

        // echo '<pre>';
        // print_r($BusinessDeliquencyStatus);
        // print_r($IndividualDeliquencyStatus);

        // exit;

        if($IndividualDeliquencyStatus->delinquency_code == '004' || $IndividualDeliquencyStatus->delinquency_code == '005' || 
        $BusinessDeliquencyStatus->delinquency_code == '004' || $BusinessDeliquencyStatus->delinquency_code == '005'){ //Defaulter Detected
            return true;
        }
        return false; //Has Never Defaulted
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

    public function actionGetloans(){
        $service = Yii::$app->params['ServiceName']['LoanApplications'];
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
                    'Loan_Product' => @$loan->Product_Description,
                    'Status' => @$loan->Portal_Status,
                    'Application_Date' => !empty($loan->Application_Date)?$loan->Application_Date:'',
                    'Applied_Amount'=> !empty($loan->Applied_Amount)?number_format($loan->Applied_Amount):'', 
                    'Update_Action' => $updateLink,
                ];
            }
        
        }
           
      

        return $result;
    }

    public function actionGetApprovedLoans(){
        $service = Yii::$app->params['ServiceName']['ApprovedLoans'];
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
                    'Loan_Product' => @$loan->Product_Description,
                    'Status' => @$loan->Approval_Status,
                    'Application_Date' =>!empty($loan->Application_Date)?date_format( date_create($loan->Application_Date), 'l jS F Y'):'',
                    'Principle_Amount'=> !empty($loan->Principle_Amount)?number_format($loan->Principle_Amount, 2):0, 
                    'Update_Action' => $updateLink,
                ];
            }
        
        }
           
      

        return $result;
    }

    public function actionUpdate(){
        
        $service = Yii::$app->params['ServiceName']['LoanApplicationHeader'];
        $filter = [
            // 'Key' => urldecode(Yii::$app->request->get('Key')),
            'Application_No'=>urldecode(Yii::$app->request->get('DocumentNo')),
        ];

        $model = new LoanApplicationHeader();
        $model->isNewRecord = false;
        $LoanApplication = \Yii::$app->navhelper->getData($service, $filter);
        $result = Yii::$app->navhelper->readByKey($service, $LoanApplication[0]->Key);

        //load nav result to model

        if(Yii::$app->request->post() && $this->loadpost(Yii::$app->request->post()['LoanApplicationHeader'],$model)){
            $LoanApplication = \Yii::$app->navhelper->getData($service, $filter);
            $result = Yii::$app->navhelper->readByKey($service, $LoanApplication[0]->Key);

              //Check If Member has a Registered business with Us
            if (empty(Yii::$app->user->identity->getMemberData()->Pin_Number)){ //Has No Registered Business
                $model->HasRegisteredBusiness = 0;
            }else{
                $model->HasRegisteredBusiness = 1;
            }
            $model->Applied_Amount =(int)str_replace(',', '',  $model->Applied_Amount);
            $model->Repayment_Period_M =(int)str_replace(',', '',  $model->Repayment_Period_M);
            $model->Principle_Amount =(int)str_replace(',', '',  $model->Principle_Amount);
            $model->Total_Interest_Repayment =(int)str_replace(',', '',  $model->Total_Interest_Repayment);
            $model->Total_Loan_Repayment =(int)str_replace(',', '',  $model->Total_Loan_Repayment);
            $model->Monthly_Installment =(int)str_replace(',', '',  $model->Monthly_Installment);
            $model->Total_Principle_Repayment =(int)str_replace(',', '',  $model->Total_Principle_Repayment);
            $model->Key = $result->Key;
            
            // $model->Application_Category = 0;
            $result = Yii::$app->navhelper->updateData($service,$model);
            // echo '<pre>';
            // print_r($result);
            // exit;
            //Save To EDMS 
            Yii::$app->Mfiles->CreateLoan($result);
            if(is_string($result)){
                Yii::$app->session->setFlash('error',$result);
                return $this->redirect(Yii::$app->request->referrer);
            }else{
                Yii::$app->session->setFlash('success','Loan Details Saved Successfully',true);
                return $this->redirect(Yii::$app->request->referrer);
            }

        }
            $model = $this->loadtomodel($result,$model);
            return $this->render('update', [
                'model' => $model,
                'loanProducts'=>$this->getLoanProducts(),
                'EconomicSectors'=>$this->getEconomicSectors()


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

        $model = new LoanApplicationHeader();
        $service = Yii::$app->params['ServiceName']['LoanApplicationHeader'];
            /*Do initial request */
        if(!isset(Yii::$app->request->post()['LoanApplicationHeader'])){
            //$now = date('Y-m-d');
            $model->Member_No = Yii::$app->user->identity->{'No_'};
            $result = Yii::$app->navhelper->postData($service,$model);
            if(is_object($result) )
            {
                return $this->redirect(['update','Key' => urlencode( $result->Key)]);
            }else{
                Yii::$app->session->setFlash('error', $result);
                return $this->redirect(['index']);
            }
        }

        if(Yii::$app->request->post() && $this->loadpost(Yii::$app->request->post()['LoanApplicationHeader'],$model)){

            $model->Member_No = Yii::$app->user->identity->{'No_'};
            
            $result = Yii::$app->navhelper->postData($service,$model);
            // echo '<pre>';
            // print_r($result);
            // exit;
            if(is_object($result)){
                $model->isNewRecord = false;
                Yii::$app->session->setFlash('success','Loan Created Successfully');
                return $this->redirect(['update', 'Key'=>$result->Key, 'DocumentNo'=>$result->Application_No]);

            }else{
                $model->isNewRecord = true;
                Yii::$app->session->setFlash('error',$result);
                return $this->redirect(['index']);

            }

        }

        return $this->render('create', [
            'model' => $model,
            'loanProducts'=>$this->getLoanProducts()
        ]);
       
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

        $request = Yii::$app->navhelper->updateData($service,$model);

        Yii::$app->response->format = \yii\web\response::FORMAT_JSON;
        if(is_array($request)){
            $ReturnData  = (object) [
                "Total_Principle_Repayment"=> isset($request[0]->Total_Principle_Repayment)?number_format($request[0]->Total_Principle_Repayment):'',
                "Total_Interest_Repayment"=> isset($request[0]->Total_Interest_Repayment)?number_format($request[0]->Total_Interest_Repayment):'',
                "Total_Loan_Repayment"=> isset($request[0]->Total_Loan_Repayment)?number_format($request[0]->Total_Loan_Repayment):'',
                "Key"=>isset($request[0]->Key)?$request[0]->Key:'',
                'Monthly_Installment'=> isset($request[0]->Monthly_Installment)?number_format($request[0]->Monthly_Installment):'', 
                ];
                return $ReturnData;
        }
        return $request;

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
            $newStr =  // If you want it to be "185345321"

            $model->Applied_Amount =(int)str_replace(',', '', Yii::$app->request->post('Applied_Amount'));
        }

        $result = Yii::$app->navhelper->updateData($service,$model);
        if(is_string($result)){
            return $result;
        }
        //refresh Here
        $this->PaymentSchedule(Yii::$app->request->post('LoanNo'));
        $request = Yii::$app->navhelper->getData($service, $filter);
       
        Yii::$app->response->format = \yii\web\response::FORMAT_JSON;

        if(is_array($request)){
            $ReturnData  = (object) [
                "Total_Principle_Repayment"=> isset($request[0]->Total_Principle_Repayment)?number_format($request[0]->Total_Principle_Repayment):'',
                "Total_Interest_Repayment"=> isset($request[0]->Total_Interest_Repayment)?number_format($request[0]->Total_Interest_Repayment):'',
                "Total_Loan_Repayment"=> isset($request[0]->Total_Loan_Repayment)?number_format($request[0]->Total_Loan_Repayment):'',
                "Key"=>isset($request[0]->Key)?$request[0]->Key:'',
                'Monthly_Installment'=> isset($request[0]->Monthly_Installment)?number_format($request[0]->Monthly_Installment):'', 
                ];
                return $ReturnData;
        }
        return $request;

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
            $model->Repayment_Period_M = (int)str_replace(',', '', Yii::$app->request->post('Repayment_Period_M'));
        }

        $result = Yii::$app->navhelper->updateData($service,$model);
        if(is_string($result)){
            return $result;
        }else{
              //refresh Here
        $this->PaymentSchedule(Yii::$app->request->post('LoanNo'));
        $request = Yii::$app->navhelper->getData($service, $filter);
        Yii::$app->response->format = \yii\web\response::FORMAT_JSON;
       
        if(is_array($request)){
            $ReturnData  = (object) [
                "Total_Principle_Repayment"=> isset($request[0]->Total_Principle_Repayment)?number_format($request[0]->Total_Principle_Repayment):'',
                "Total_Interest_Repayment"=> isset($request[0]->Total_Interest_Repayment)?number_format($request[0]->Total_Interest_Repayment):'',
                "Total_Loan_Repayment"=> isset($request[0]->Total_Loan_Repayment)?number_format($request[0]->Total_Loan_Repayment):'',
                "Key"=>isset($request[0]->Key)?$request[0]->Key:'',
                'Monthly_Installment'=> isset($request[0]->Monthly_Installment)?number_format($request[0]->Monthly_Installment):'', 
                ];
                return $ReturnData;
        }
        return $request;


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
