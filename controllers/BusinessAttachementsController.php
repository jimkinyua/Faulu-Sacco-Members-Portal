<?php

namespace app\controllers;
use app\models\LoanApplicationHeader;
use app\models\LoanBusinessAttachements;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\filters\ContentNegotiator;
use Yii;
use yii\helpers\Html;
use yii\web\UploadedFile;


class BusinessAttachementsController extends \yii\web\Controller{
    public function behaviors(){
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['getloans', 'create','index', 'read', 'delete-attachement', 'update' ],
                'rules' => [
                     [
                        'actions' => ['getloans', 'create','index', 'read', 'delete-attachement', 'update'],
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
        $model = new LoanBusinessAttachements();
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

   
    public function GetLoanDetails($LoanKey){
        $model = new LoanApplicationHeader();
        $service = Yii::$app->params['ServiceName']['LoanApplicationHeader'];
        $result = Yii::$app->navhelper->readByKey($service, urldecode($LoanKey));
       return $model = $this->loadtomodel($result,$model);
    }

    public function actionRead($Key)
    {
       $model = new LoanBusinessAttachements();
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



    public function getBusinessAttachements($category){
        $service = Yii::$app->params['ServiceName']['BusinessAttachements'];
        $filter = [
            'Is_CashFlow'=>0
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
