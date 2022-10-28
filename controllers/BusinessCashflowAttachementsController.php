<?php

namespace app\controllers;
use app\models\LoanApplicationHeader;
use app\models\BusinessCashFlowAttachements;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\filters\ContentNegotiator;
use Yii;
use yii\helpers\Html;
use yii\web\UploadedFile;


class BusinessCashflowAttachementsController extends \yii\web\Controller{
    public function behaviors(){
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['getloans', 'create','index'],
                'rules' => [
                    [
                        'actions' => ['signup'],
                        'allow' => true,
                        'roles' => ['?'],
                    ],
                     [
                        'actions' => ['getloans','index', 'create'],
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

    public function actionIndex($Key){
        $model = new BusinessCashFlowAttachements();
        $LoanModel = $this->GetLoanDetails($Key);
        if($LoanModel->HasRegisteredBusiness == true){
            $cartegory = 'Registered_Business'; //
        }else{
            $cartegory = '_Unregistered_Business'; 
        }
        $attachements = $model->getAttachments($LoanModel->Application_No);
        // echo '<pre>';
        // print_r($attachements);
        // exit;

        if (Yii::$app->request->isPost) {
            $model->DocNum = $LoanModel->Application_No;
            $model->Type = 'Business Loan Application Attachement';
            foreach($this->getBusinessAttachements($cartegory) as $requiredDoc){
                $parameterName = str_replace(' ', '_', $requiredDoc['Description']);
                $parameterName = str_replace('.', '_', $parameterName);
                $model->uploadFilesArray = UploadedFile::getInstancesByName($parameterName);

                foreach($model->uploadFilesArray as $File){
                    $model->docFile = $File;
                    $model->FileName =  $requiredDoc['Description'];

                    // echo '<pre>';
                    // print_r($model);
                    // exit;
                    $FileName = $model->upload();
                }
            }
            return $this->redirect(['index', 'Key'=>$LoanModel->Key]);

        }
        return $this->render('index',['LoanModel'=>$LoanModel, 
            'model'=>$model,
            'RequiredAttachements'=>$this->getBusinessAttachements($cartegory),
            // 'Unregistered'=>$this->getUnlicecensedBusinessAttachement($cartegory),
            'MyAttachedDocs'=>$attachements
        ]);
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

    public function actionRead($Key)
    {
       $model = new BusinessCashFlowAttachements();
       $content = $model->read($Key);
    //    print '<pre>';
    //    print_r($content); exit;
       return $this->render('read',['content' => $content]);
    }

    public function actionDeleteAttachement(){
        $service = Yii::$app->params['ServiceName']['ESS_Files'];
        $result = Yii::$app->navhelper->readByKey($service, urldecode(Yii::$app->request->get('Key')));
        $ApplicationData = $this->ApplicantDetailWithDocNum($result->Docnum);
        $result = Yii::$app->navhelper->deleteData($service,Yii::$app->request->get('Key'));
        if(!is_string($result)){
            Yii::$app->session->setFlash('success', 'Document Deleted Successfully.');
            return $this->redirect(['index', 'Key'=>$ApplicationData->Key]);
        }else{
           Yii::$app->session->setFlash('error', $result);
           return $this->redirect(['index', 'Key'=>$ApplicationData->Key]);
        }
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
            if($this->CheckDeliquencyStatus($LoanModel) === true){
                $LoanModel->HasDefaultRecordInCRB = 1;
            }else{
                $LoanModel->HasDefaultRecordInCRB = 0;
            }
            // $model->Key= $LoanModel->Key;
            $LoanModel->AgreedToTerms = Yii::$app->request->post()['LoanApplicationHeader']['AgreedToTerms'];
            $result = Yii::$app->navhelper->updateData($service,$LoanModel);
            
            if(is_string($result)){
                Yii::$app->session->setFlash('error',$result);
                return $this->redirect(Yii::$app->request->referrer);
            }
            // If Listed, Tell Them to Attach the CRB Clearance Cert
            if($result->HasDefaultRecordInCRB == 1 && empty($LoanModel->getCRBClearanceCertificates())){
                return $this->redirect(['clearance-ceriticate/index', 'Key'=>$result->Key]);
            }
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

        if($LoanData->Application_Category == 'Business'){ //Has Applied Loan For Loan
            if(!empty(Yii::$app->user->identity->getApplicantData()->Pin_Number)){ //registered Business This One
                $BusinessDeliquencyStatus = Yii::$app->MetroPolIntergration->actionMetroPolCheck($LoanData, Yii::$app->user->identity->getMemberData(), Yii::$app->params['MetroPol']['IdentityTypes']['BusinessRegistrationNo']);
            }  
        }else{
            $BusinessDeliquencyStatus = (object)[];
            $BusinessDeliquencyStatus->delinquency_code = '001'; //Can't Be Found Because It's not Registered
        }

        if($IndividualDeliquencyStatus->delinquency_code == '004' || $IndividualDeliquencyStatus->delinquency_code == '005' || $BusinessDeliquencyStatus->delinquency_code == '004' || $BusinessDeliquencyStatus->delinquency_code == '005'){ //Defaulter Detected
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

    public function actionUpdate(){
        
        $service = Yii::$app->params['ServiceName']['LoanApplicationHeader'];
        $filter = [
            'Key' => urldecode(Yii::$app->request->get('Key')),
        ];
        $result = Yii::$app->navhelper->readByKey($service, urldecode(Yii::$app->request->get('Key')));

        $model = new LoanApplicationHeader();
        $model->isNewRecord = false;

        //load nav result to model

        if(Yii::$app->request->post() && $this->loadpost(Yii::$app->request->post()['LoanApplicationHeader'],$model)){
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
            
            // $model->Application_Category = 0;
            $result = Yii::$app->navhelper->updateData($service,$model);
            // echo '<pre>';
            // print_r($model);
            // exit;
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
                return $this->redirect(['update', 'Key'=>$result->Key]);

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

    public function getBusinessAttachements($category){
        $service = Yii::$app->params['ServiceName']['BusinessAttachements'];
        $filter = [
            'Is_CashFlow'=>1
        ];
        $res = [];
        $BusinessAttachements = \Yii::$app->navhelper->getData($service, $filter);
        if(is_object($BusinessAttachements)){
            return $res;
        }
        foreach($BusinessAttachements as $BusinessAttachement){
            if(!empty($BusinessAttachement->Id || $BusinessAttachement->Description))
            $res[] = [
                'Id' => $BusinessAttachement->Id,
                'Name' => $BusinessAttachement->DocumentName,
                'Description' => $BusinessAttachement->Description,

            ];
        }

        return $res;
    }

    public function getUnlicecensedBusinessAttachement($category){
        $service = Yii::$app->params['ServiceName']['BusinessAttachements'];
        $filter = [
            'Category'=>' Unregistered Business'
        ];
        $res = [];
        $BusinessAttachements = \Yii::$app->navhelper->getData($service, $filter);
        if(is_object($BusinessAttachements)){
            return $res;
        }
        foreach($BusinessAttachements as $BusinessAttachement){
            if(!empty($BusinessAttachement->Id || $BusinessAttachement->Description))
            $res[] = [
                'Id' => $BusinessAttachement->Id,
                'Name' => $BusinessAttachement->DocumentName,
                'Description' => $BusinessAttachement->Description,

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
