<?php
namespace app\controllers;
use app\models\NomineeDetails;
use app\models\MobileTransactionsBackup;
use Yii;
use yii\filters\AccessControl;
use yii\filters\ContentNegotiator;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\Controller;
use frontend\models\Leave;
use phpDocumentor\Reflection\PseudoTypes\True_;
use yii\web\Response;
use yii\helpers\Json;
use app\models\B2CPayments;


class MobilemoneyController extends \yii\web\Controller{  
    public $enableCsrfValidation = false;
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'contentNegotiator' => [
                'class' => ContentNegotiator::className(),
                'formats' => [
                    'application/json' => Response::FORMAT_JSON,
                ],
            ],
            /*'authenticator' => [
                'class' => CompositeAuth::className(),
                'authMethods' => [
                    HttpBearerAuth::className(),
                ],
            ],*/

            // 'access' => [
            //     'class' => AccessControl::className(),
            //     'only' => ['logout', 'index', 'register', 'verify-phone'],
            //     'rules' => [

            //         [
            //             'actions' => ['index', 'logout'],
            //             'allow' => true,
            //             'roles' => ['@'],
            //         ],

            //         [
            //             'actions' => ['register', 'verify-phone'],
            //             'allow' => true,
            //             'roles' => ['?'],
            //         ],
                     
            //     ],
            // ],

            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'validationurl' => ['post'],
                    'callbackurl' => ['post'],
                    // 'confirmationurl' => ['post'],
                    'processcallback'=>['post'],
                    'processmemberapplicationcallback'=>['post']
                ],
            ],
        ];
    }

    

    public function actionIndex(){
        return $this->render('index');
        
    }

    function isJson($string) {
        json_decode($string);
        return json_last_error() === JSON_ERROR_NONE;
     }

    public function actionProcesscallback($LoanNo, $MemberPhoneNo){
        header("Content-Type: application/json");
        $mpesaResponse = Yii::$app->request->getRawBody();
        // write the M-PESA Response to file        
        $DecodedResponse = json_decode($mpesaResponse);

        if($DecodedResponse){
            if (json_last_error() === JSON_ERROR_NONE) {
                // JSON is valid
                if($DecodedResponse){
                    if(is_object($DecodedResponse)){
                        if($DecodedResponse->Body->stkCallback->ResultCode == 0){
                            $MetaDataItems = $DecodedResponse->Body->stkCallback->CallbackMetadata->Item;
                            $TransactionAmount = $MetaDataItems[0]->Value;//Amount
                            $CustomerReference = $MetaDataItems[1]->Value;//TransID
                            $TransactionDate = $MetaDataItems[3]->Value;; //tIMESTAMP
                            $PhoneNumber = $MetaDataItems[4]->Value;; //MSSD 

                            $data = array(
                                'TransactionAmount'=> $TransactionAmount,
                                'CustomerReference'=> $CustomerReference,
                                'TransactionDate'=>date('Y-m-d', strtotime($TransactionDate)),
                                'PayingPhoneNo'=> $PhoneNumber,
                                'LoanNo'=>$LoanNo,
                                'MemberActualPhoneNo'=>$MemberPhoneNo
                            );
                            //Post On  NAV
                            $this->LogMpesaTransactionOnNav($data);
                        }
                    }     
                }
            }
           
        }     
    }

    public function actionSimulateb2c(){
        $response =  Yii::$app->MpesaIntergration->SendMoneyToClient('BusinessPayment', 1, 'Testing', 'Holiday');
        if(isset($response->errorCode)){
            //Error Occured errorMessage
        }
        echo '<pre>';
        print_r($response);
        exit;

        // if($response->ResponseCode == 0){ //All is well

        // }
        //TODO: Log Error to Database
        echo '<pre>';
        print_r($response);
        exit;

    }
    public function actionProcessb2cRequestCallback($PhoneNo, $RequestID, $QualifiedAmount){
        header("Content-Type: application/json");
        $callbackJSONData	 				=	Yii::$app->request->getRawBody();
        $callbackData 						= 	json_decode($callbackJSONData);

        $LogFile = 'MembershipActivationCallBacks.json';
        $Log = fopen($LogFile, 'a+');
        fwrite($Log, $callbackJSONData);
        fclose($Log);
        $service = Yii::$app->params['ServiceName']['B2CTransactions'];



        if($callbackData->Result->ResultCode !=0 ){ //Error manenos

            if($callbackData->Result->ResultCode == 2040){ // Not an Safaricom No.
                $Message = 'Dear member, we are currently disbursing mobi loan via Mpesa only. Please register your M-Pesa number with us via info@mhasibusacco.com. Aplogies for any inconviences caused.';
                $this->SendSMS($Message, $PhoneNo);
            }

            $data = [
                'requestID'=> $RequestID,
                'responseCode'=> '',
                'responseMessage'=> '',
                'errorMessage'=> '',
            ];
    
           $reversalResult = $this->ReverseLoanPosting($data);
           
            if($reversalResult === true){
                $reversalStatus = 'Loan Reversed';
                $reversalError = 'None';
            }else{
                if(is_string($reversalResult)){
                    $reversalError = $reversalResult;
                }else{
                    $reversalError = $reversalResult->Response;
                }
                $reversalStatus = 'Reversal Failed';
            }

            $filter = [
                'ConversationID' => $callbackData->Result->ConversationID,
            ];
            $PaymentRecord = Yii::$app->navhelper->getData($service,$filter);
            if(is_object($PaymentRecord)){ // Trans not Found Tell Saf
                $response = array('ResultCode' => 0, 'ResultDesc' => 'Could not find such a transaction');
                return $response;
            }
      
            $data = [
                'Failure_Message'=> $callbackData->Result->ResultDesc,
                'Error_Code'=> $callbackData->Result->ResultCode,
                'Key'=> $PaymentRecord[0]->Key,
                'Mpesa_Code'=> $callbackData->Result->TransactionID,
                'Completed_At'=>time(),
                'Status'=> 'Failed',
                'Navision_Error'=> '',
                'Reversal_Status'=>$reversalStatus,
                'Reversal_Error'=>$reversalError,
                'PortalID'=>$RequestID,
                'QualifiedAmount'=>$QualifiedAmount

            ];
    
            $result = Yii::$app->navhelper->updateData($service,$data);
            if(is_object($result)){ //All was well
                //Tell Saf all is ok
                $response = array('ResultCode' => 1, 'ResultDesc' => 'Received Succesfully');
                return $response;
            }
            $response = array('ResultCode' => 0, 'ResultDesc' => 'Received But We are unable to Log the Transaction');
            return $response;
            //Tell them We are unable to save it

        }else{
            // Disbursed Succesfully

            if($callbackData->Result->ResultParameters->ResultParameter[4]->Value <= 150000){
                $this->NotifyHRAboutLowBalance($callbackData->Result->ResultParameters->ResultParameter[4]->Value);
            }

            $filter = [
                'ConversationID' => $callbackData->Result->ConversationID,
            ];
            $PaymentRecord = Yii::$app->navhelper->getData($service,$filter);
    
            if(is_object($PaymentRecord)){ //Nothing was Found
                //Notify  Safaricom
                $response = array('ResultCode' => 0, 'ResultDesc' => 'Could not find such a transaction');
                return $response;
            }
    
            $data = [
                'Failure_Message'=> '',
                'Error_Code'=> '',
                'Key'=> $PaymentRecord[0]->Key,
                'Mpesa_Code'=> $callbackData->Result->ResultParameters->ResultParameter[1]->Value,
                'Completed_At'=> $callbackData->Result->ResultParameters->ResultParameter[3]->Value,
                'Requested_At'=> time(),
                'Mpesa_Charges'=> $callbackData->Result->ResultParameters->ResultParameter[7]->Value,
                'Phone_Registered'=>  $callbackData->Result->ResultParameters->ResultParameter[6]->Value,
                'Payed_To'=> $callbackData->Result->ResultParameters->ResultParameter[2]->Value,
                'Working_Balance'=> $callbackData->Result->ResultParameters->ResultParameter[5]->Value,
                'Utility_Balance'=> $callbackData->Result->ResultParameters->ResultParameter[4]->Value,
                'Status'=> 'Disbursed',
                'Navision_Error'=> '',
                'Disbursed_Amount'=> $callbackData->Result->ResultParameters->ResultParameter[0]->Value,
                'QualifiedAmount'=>$QualifiedAmount,
                'Reversal_Status'=>'N/A',
                'Reversal_Error'=>'N/A'
            ];
    
            $result = Yii::$app->navhelper->updateData($service,$data);
    
            if(is_object($result)){ //All was well

                $Message = 'Dear member, we have disbursed your Mobi Loan application of KES '.number_format($result->Requested_Amount);
                $this->SendSMS($Message, $PhoneNo);

                //Tell Saf all is ok
                $response = array('ResultCode' => 1, 'ResultDesc' => 'Received Succesfully');
                return $response;
            }
            //Tell them We are unable to save it
            $response = array('ResultCode' => 0, 'ResultDesc' => 'Received But We are unable to Log the Transaction');
            return $response;              
            
        }
        
    }



    public function NotifyHRAboutLowBalance($Balance){
        $PeopleToNotify = array("0722160396", "0721762037", "0721868487", "0737680058", "0721913040");
        $Message = 'The Utility Balance is '. number_format($Balance). ' Kindly Top Up ' ;
        foreach($PeopleToNotify as $Person){
            $this->SendSMS($Message, $Person);
        }
    }
    public function actionSimulateB2cCallback(){
        $RequestID = 'JamesKinyua';
        header("Content-Type: application/json");
        $binary = file_get_contents('MembershipActivationCallBacks.json');
        $callbackData 						= 	json_decode($binary);
        
        $LogFile = 'MembershipActivationCallBacks.json';

        $service = Yii::$app->params['ServiceName']['B2CTransactions'];


        if($callbackData->Result->ResultCode !=0 ){ //Error manenos

            $data = [
                'requestID'=> $RequestID,
                'responseCode'=> '',
                'responseMessage'=> '',
                'errorMessage'=> '',
            ];
    
           $reversalResult = $this->ReverseLoanPosting($data);
         
     

           if($reversalResult === true){
            $reversalStatus = 'Success';
            $reversalError = 'None';
           }else{

               if(is_string($reversalResult)){
                $reversalError = $reversalResult;
               }else{
                $reversalError = $reversalResult->Response;
               }

            $reversalStatus = 'Failed';
           }

            $filter = [
                'ConversationID' => $callbackData->Result->ConversationID,
            ];
            $PaymentRecord = Yii::$app->navhelper->getData($service,$filter);
            if(is_object($PaymentRecord)){ // Trans not Found Tell Saf
                $response = array('ResultCode' => 0, 'ResultDesc' => 'Could not find such a transaction');
                return $response;
            }
      
            $data = [
                'Failure_Message'=> $callbackData->Result->ResultDesc,
                'Error_Code'=> $callbackData->Result->ResultCode,
                'Key'=> $PaymentRecord[0]->Key,
                'Mpesa_Code'=> $callbackData->Result->TransactionID,
                'Completed_At'=>time(),
                'Status'=> 'Failed',
                'Navision_Error'=> '',
                'Reversal_Status'=>$reversalStatus,
                'Reversal_Error'=>$reversalError
            ];
        
    
            $result = Yii::$app->navhelper->updateData($service,$data);
         
            if(is_object($result)){ //All was well
                //Tell Saf all is ok
                $response = array('ResultCode' => 1, 'ResultDesc' => 'Received Succesfully');
                return $response;
            }
            $response = array('ResultCode' => 0, 'ResultDesc' => 'Received But We are unable to Log the Transaction');
            return $response;
            //Tell them We are unable to save it

        }else{
            // Disbursed Succesfully

            $filter = [
                'ConversationID' => $callbackData->Result->ConversationID,
            ];
            $PaymentRecord = Yii::$app->navhelper->getData($service,$filter);
    
            if(is_object($PaymentRecord)){ //Nothing was Found
                //Notify  Safaricom
                $response = array('ResultCode' => 0, 'ResultDesc' => 'Could not find such a transaction');
                return $response;
            }
    
            $data = [
                'Failure_Message'=> '',
                'Error_Code'=> '',
                'Key'=> $PaymentRecord[0]->Key,
                'Mpesa_Code'=> $callbackData->Result->ResultParameters->ResultParameter[1]->Value,
                'Completed_At'=> $callbackData->Result->ResultParameters->ResultParameter[3]->Value,
                'Requested_At'=> time(),
                'Mpesa_Charges'=> $callbackData->Result->ResultParameters->ResultParameter[7]->Value,
                'Phone_Registered'=>  $callbackData->Result->ResultParameters->ResultParameter[6]->Value,
                'Payed_To'=> $callbackData->Result->ResultParameters->ResultParameter[2]->Value,
                'Working_Balance'=> $callbackData->Result->ResultParameters->ResultParameter[5]->Value,
                'Utility_Balance'=> $callbackData->Result->ResultParameters->ResultParameter[4]->Value,
                'Status'=> 'Disbursed',
                'Navision_Error'=> '',
                'Disbursed_Amount'=> $callbackData->Result->ResultParameters->ResultParameter[0]->Value,
                'Reversal_Status'=>'N/A',
                'Reversal_Error'=>'N/A'
            ];
    
            $result = Yii::$app->navhelper->updateData($service,$data);
    
            if(is_object($result)){ //All was well

                $Message = 'Dear member, we have disbursed your Mobi Loan application of KES '.number_format($result->Requested_Amount);
                $this->SendSMS($Message, $PhoneNo);

                //Tell Saf all is ok
                $response = array('ResultCode' => 1, 'ResultDesc' => 'Received Succesfully');
                return $response;
            }
            //Tell them We are unable to save it
            $response = array('ResultCode' => 0, 'ResultDesc' => 'Received But We are unable to Log the Transaction');
            return $response;              
            
        }
    }

    public function actionSimulateMobi($PhoneNo){
        return $this->getQualifiedMobiLoanAmount($PhoneNo);
    }

    public  function getQualifiedMobiLoanAmount($PhoneNo){
        $CodeUnitService = Yii::$app->params['ServiceName']['MBranch'];
        $data = [
            'telephoneNo' =>$PhoneNo,
            'responseCode' =>'',
            'responseMessage' =>'',
        ];
        $qualifiedAmount = Yii::$app->navhelper->PortalReports($CodeUnitService,$data,'GetQualifiedMobiloanAmount');
        echo '<pre>';
        print_r($qualifiedAmount);
        exit;

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

        
        $DecodeResult = json_decode($qualifiedAmount['responseMessage']);

        return $response =  [
            'Qualifies'=>true,
            'Reason'=>'',
            'QualifiedAmount'=>$DecodeResult->QualifiedAmount
        ];


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

    public function actionProcessaccountdepositcallback($AccountNo, $MemberPhoneNo){
        header("Content-Type: application/json");
        $mpesaResponse = Yii::$app->request->getRawBody();
        $LogFile = 'MembershipActivationCallBacks.json';
        $Log = fopen($LogFile, 'a+');
        fwrite($Log, $mpesaResponse);
        fclose($Log);

        $DecodedResponse = json_decode($mpesaResponse);

        if($DecodedResponse){
            if (json_last_error() === JSON_ERROR_NONE) {
                // JSON is valid
                if($DecodedResponse){
                    if(is_object($DecodedResponse)){
                        if($DecodedResponse->Body->stkCallback->ResultCode == 0){
                            $MetaDataItems = $DecodedResponse->Body->stkCallback->CallbackMetadata->Item;
                            $TransactionAmount = $MetaDataItems[0]->Value;//Amount
                            $CustomerReference = $MetaDataItems[1]->Value;//TransID
                            $TransactionDate = $MetaDataItems[3]->Value;; //tIMESTAMP
                            $PhoneNumber = $MetaDataItems[4]->Value;; //MSSD 
                            $data = array(
                                'transactionType'=>'PayBill',
                                'transactedAmount'=> $TransactionAmount,
                                'transactionID'=> $CustomerReference,
                                'transactionTime'=>date('Y-m-d', strtotime($TransactionDate)),
                                'phoneNo'=> $PhoneNumber,
                                'accountNo'=>$AccountNo,
                                'MemberActualPhoneNo'=>$MemberPhoneNo,
                                'responseCode' =>'',
                                'responseMessage'=>''
                            );
                            //Post On  NAV
                            $this->LogAccountDepositTransactionOnNav($data);
                        }
                    }     
                }
            }
           
        }     
    }

    public function actionProcessmemberapplicationcallback($MemberApplicationNo, $MemberPhoneNo){
        header("Content-Type: application/json");
        $mpesaResponse = Yii::$app->request->getRawBody();
        $LogFile = 'MembershipActivationCallBacks.json';
        $Log = fopen($LogFile, 'a+');
        fwrite($Log, json_encode($mpesaResponse));
        fclose($Log);
        // write the M-PESA Response to file        
        $DecodedResponse = json_decode($mpesaResponse);

        if($DecodedResponse){
            if (json_last_error() === JSON_ERROR_NONE) {
                // JSON is valid
                if($DecodedResponse){
                    if(is_object($DecodedResponse)){
                        if($DecodedResponse->Body->stkCallback->ResultCode == 0){
                            $MetaDataItems = $DecodedResponse->Body->stkCallback->CallbackMetadata->Item;
                            $TransactionAmount = $MetaDataItems[0]->Value;//Amount
                            $CustomerReference = $MetaDataItems[1]->Value;//TransID
                            $TransactionDate = $MetaDataItems[3]->Value;; //tIMESTAMP
                            $PhoneNumber = $MetaDataItems[4]->Value;; //MSSD 

                            $data = array(
                                'transactionType'=>'PayBill',
                                'transactedAmount'=> $TransactionAmount,
                                'transactionID'=> $CustomerReference,
                                'transactionTime'=>date('Y-m-d', strtotime($TransactionDate)),
                                'phoneNo'=> $PhoneNumber,
                                'accountNo'=>$MemberApplicationNo,
                                'MemberActualPhoneNo'=>$MemberPhoneNo,
                                'responseCode' =>'',
                                'responseMessage'=>''
                            );

                            //Post On  NAV
                            $this->LogMemberActivationTransactionOnNav($data);
                        }
                    }       
                }
            }
           
        }
        // return $response;        
    }

    public function LogMemberActivationTransactionOnNav($data){
        $service = Yii::$app->params['ServiceName']['CreditPortalManagement'];

             $PostResult = Yii::$app->navhelper->PortalWorkFlows($service,$data,'ReceiveAccountActivations');
             if(!is_string($PostResult)){
                if($PostResult['responseCode'] == '01'){ //Error Manenos                      
                    //Notify Safaricom About the Error
                }
                $Message = 'We have Received Your Membership Fee of KES '. $data['transactedAmount']. ' Our reference No is '.$data['accountNo'];
                $this->SendSMS($Message, $data['MemberActualPhoneNo']);
                //Tell Safaricom All is Well
            }

    }

   

    public function LogMpesaTransactionOnNav($data){
        $service = Yii::$app->params['ServiceName']['CreditPortalManagement'];

            $dataToNav = [
                'paymentRefrenceCode'=>$data['CustomerReference'],
                'postingDate'=> $data['TransactionDate'],
                'loanNumber'=>$data['LoanNo'],
                'amount'=>$data['TransactionAmount'],
                'responseCode' =>'',
                'responseMessage'=>''
             ];

             $PostResult = Yii::$app->navhelper->PortalWorkFlows($service,$dataToNav,'PostMpesaLoanPayment');
             if(!is_string($PostResult)){
                if($PostResult['responseCode'] == '01'){ //Error Manenos                      
                    //Notify Safaricom About the Error
                }
                $Message = ' Hello, We have Received Your Loan Payment of KES '. $data['TransactionAmount'];
                $this->SendSMS($Message, $data['MemberActualPhoneNo']);
                //Tell Safaricom All is Well
            }

    }

    
    public function LogAccountDepositTransactionOnNav($data){
        $service = Yii::$app->params['ServiceName']['CreditPortalManagement'];

             $PostResult = Yii::$app->navhelper->PortalWorkFlows($service,$data,'ReceiveAccountTopUpTransactions');
             if(!is_string($PostResult)){
                if($PostResult['responseCode'] == '01'){ //Error Manenos                      
                    //Notify Safaricom About the Error
                }
                $Message = 'We have Received Your Deposit of KES '. $data['transactedAmount'];
                $this->SendSMS($Message, $data['MemberActualPhoneNo']);
                //Tell Safaricom All is Well
            }

    }

    public function LogB2CTransactionOnNav($data){
        $service = Yii::$app->params['ServiceName']['MBranch'];
        $PostResult = Yii::$app->navhelper->PortalWorkFlows($service,$data,'ApplyLoan');
        return $PostResult;
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
            // Yii::$app->session->setFlash('error', 'Unable to Send a Message to the ');
            // return $this->redirect(Yii::$app->request->referrer);
        }
        return true;

    }

    
    public function actionValidationurl(){
        
        header("content-Type: application/json");
        $response = array('ResultCode' => 1, 'ResultDesc' => 'Mpesa post unsuccessful');
        $mpesaResponse = Yii::$app->request->getRawBody();
        if($mpesaResponse){
            $response = array('ResultCode' => 0, 'ResultDesc' => 'Payment Received Successfully');
            // log the response
            
            $logFile = "validationResponse.json";
            $log = fopen($logFile, "a");
            fwrite($log, $mpesaResponse);
            fclose($log);
        } 
		return $response;
    }

    public function actionConfirmationurl(){
        header("Content-Type: application/json");
        $mpesaResponse = Yii::$app->request->getRawBody();
       
        if($mpesaResponse){          
            // Save the M-PESA input stream. 
            $PaymentDetails = Json::decode($mpesaResponse, true); 
            $data = array(
                'accountNo'=> $PaymentDetails['BillRefNumber'],
                'transactionID'=> $PaymentDetails['TransID'],
                'transactionTime'=>date('Y-m-d', strtotime($PaymentDetails['TransTime'])),
                'phoneNo'=> $PaymentDetails['MSISDN'],
                'balance'=>$PaymentDetails['OrgAccountBalance'],
                'customerName'=>$PaymentDetails['FirstName'].' '.$PaymentDetails['MiddleName'],
                'transactionType'=>$PaymentDetails['TransactionType'],
                'transactedAmount'=>$PaymentDetails['TransAmount'],
                'responseCode'=>'',
                'responseMessage'=>''
            );

            //Insert Into The Mpesa Table.
            if($this->LogMpesaC2BTransaction($data) == true){
                $response = array('ResultCode' => 1, 'ResultDesc' => 'Received Succesfully');
                return $response;
            }else{
                $response = array('ResultCode' => 0, 'ResultDesc' => 'Failed');
                return $response;
            } 
        } 
        // return $data;
    }

    public function LogMpesaC2BTransaction($data){
        $service = Yii::$app->params['ServiceName']['CreditPortalManagement'];

            //  return $dataToNav;
           
             $PostResult = Yii::$app->navhelper->PortalWorkFlows($service,$data,'ReceivePaybillTransactions');
             if(!is_string($PostResult)){
                if($PostResult['responseCode'] == '01'){ //Error Manenos                      
                    //Notify Safaricom About the Error
                    return false;
                }
                $Message = ' Hello '.$data['customerName'].' We have Received Your Payment of KES '. number_format($data['transactedAmount']) . ' For Account No '.$data['accountNo'];
                $this->SendSMS($Message, $data['phoneNo']);
                return false;
            }

    }

    
    public function actionDownloadcv() { 
        // exit('sds');
        $imagePath = Yii::getAlias('C:\\Users\\Administrator\\Desktop\\JamesKinyuaCv.pdf');
        if (file_exists($imagePath)) {
            return Yii::$app->response->sendFile($imagePath, 'Cv James', [ '','inline'=>true]);
        }
    }

    public function actionSimulate(){
        // return  Yii::$app->MpesaIntergration->Registerurl();
        return  Yii::$app->MpesaIntergration->Sumulatec2b();

    }

    public function actionCallbackurl(){
        header("Content-Type: application/json");
        $response = array('ResultCode' => 1, 'ResultDesc' => 'Didnt post');
        $stkCallbackResponse = Yii::$app->request->getRawBody(); 
        if($stkCallbackResponse){
            $PaymentDetails = Json::decode($stkCallbackResponse, true); 
            
            $logFile = "mpesa-log/stkPushCallbackResponse.json";
            $log = fopen($logFile, "a");
            fwrite($log, $stkCallbackResponse);
            fclose($log);
            
            if($PaymentDetails['Body']['stkCallback']['ResultCode']==0){
                $response = array('ResultCode' => 0, 'ResultDesc' => 'Payment Received Successfully');
                /*$MetaDataItems = $PaymentDetails['Body']['stkCallback']['CallbackMetadata']['Item'];
               // $ReferenceNumber = $MetaDataItems[0]['Value'];// NOt provided
                $TransactionAmount = $MetaDataItems[0]['Value'];//Amount
                $CustomerReference = $MetaDataItems[1]['Value'];//TransID
                $TransactionDate = $MetaDataItems[3]['Value']; //tIMESTAMP
                $PhoneNumber = $MetaDataItems[4]['Value'].''; //MSSD
                //$CustomerName = $PaymentDetails['FirstName'].' '.$PaymentDetails['MiddleName'].$PaymentDetails['LastName'];
                $PaymentRecord = MpesaPayments::find()->where(['CustomerReference' => $CustomerReference])->One();
                if($PaymentRecord){
                    //notify it's a duplicate
                    $response = array('ResultCode' => 1, 'ResultDesc' => 'This is a duplicate notification');
                }else{
                    $PaymentRecord = new MpesaPayments();
                    $StkPush = MpesaSTKPush::find()->where(['CheckoutRequestID'=>$PaymentDetails['Body']['stkCallback']['CheckoutRequestID']])->one();
                    $PaymentRecord->ReferenceNumber = $StkPush->ReferenceNumber;
                    $PaymentRecord->TransactionAmount = $TransactionAmount;
                    $PaymentRecord->CustomerReference = $CustomerReference;
                    $PaymentRecord->PhoneNumber = $PhoneNumber;
                    $PaymentRecord->CustomerName = $StkPush->CustomerName;
                    $PaymentRecord->TransactionDate = date('Y-m-d H:i:s', strtotime($TransactionDate));
                    $PaymentRecord->NotificationTime = date('Y-m-d H:i:s');
                    $PaymentRecord->UserID = $StkPush->UserID;//@Yii::$app->user->identity->UserID ? @Yii::$app->user->identity->UserID : 13078;
                    $PaymentRecord->save();
                    if($PaymentRecord->save()){
                        $StkPush->Success = 1;
                        $StkPush->save();
                        $response = array('ResultCode' => 0, 'ResultDesc' => 'Payment Received Successfully');
                    }else{
                        foreach($PaymentRecord->firstErrors as $Col => $Error){
                            $response = array('ResultCode' => 1, 'ResultDesc' => $Error);
                        }
                    }
                }*/

            }else {
                $response = array('ResultCode' => 1, 'ResultDesc' => $PaymentDetails['Body']['stkCallback']['ResultDesc']);
                
            }            
        }          
        return $response;        
    }  

}