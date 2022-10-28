<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\filters\VerbFilter;
use app\models\MemberApplicationCard;
use app\models\Attachement;
use yii\web\UploadedFile;
use app\models\LoanApplicationHeader;

class SignApplicationController extends Controller{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['index', 'download-application-form', 'read'],
                'rules' => [
                    [
                        'actions' => ['index', 'download-application-form', 'read'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                  
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    // 'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    public function GetLoanDetails($LoanKey)
    {
        $model = new LoanApplicationHeader();
        $service = Yii::$app->params['ServiceName']['LoanApplicationCard'];
        $result = Yii::$app->navhelper->readByKey($service, urldecode($LoanKey));
        return $model = $this->loadtomodel($result, $model);
    }

    public function actionIndex($Key)
    {
        $attachementModel = new Attachement();
        $service = Yii::$app->params['ServiceName']['LoanApplicationCard'];

        $LoanModel = $this->GetLoanDetails($Key);
        $attachementModel->Docnum = $LoanModel->Loan_No;

        if(Yii::$app->request->post() && $this->loadpost(Yii::$app->request->post()['Attachement'],$attachementModel)){

            $filter = [
                'Loan_No' =>$LoanModel->Loan_No,
            ]; 
            $refresh = Yii::$app->navhelper->getData($service,$filter);
           
            $attachementModel->Key = $refresh[0]->Key;
            $result = Yii::$app->navhelper->updateData($service,$attachementModel);
            if(is_object($result)){
                $attachementModel->PayslipDetails = UploadedFile::getInstance($attachementModel, 'PayslipDetails');
                $attachementModel->ApplicationForm = UploadedFile::getInstance($attachementModel, 'ApplicationForm');
                 
        //         echo '<pre>';
        // print_r($attachementModel);
        // exit;
                if ($attachementModel->upload($attachementModel)) {
                        // file is uploaded successfully
                        return $this->redirect(['loan/send-for-approval', 'Key'=>$attachementModel->Key]);
                    }
                

            }else{
                Yii::$app->session->setFlash('error', $result,true);
                return $this->redirect(['index']);
            }
        }//End Saving Profile Gen data

        return $this->render('index', [
            'model' => $LoanModel, 
            'SignedApplication'=> '',//$this->getLoanApplicationinPDF($LoanModel->Loan_No),
            'attachementModel'=>$attachementModel
        ]);
    }
    public function ApplicantionDetails($Key){
        $model = new MemberApplicationCard();
        $service = Yii::$app->params['ServiceName']['MemberApplicationCard'];
        $memberApplication = Yii::$app->navhelper->readByKey($service, urldecode($Key));
       return $model = $this->loadtomodel($memberApplication,$model);
    }

    public function actionRead($No)
    {
        $this->layout = 'applicant-main';
       $model = new Attachement();
       $content = $model->read($No);

       /*print '<pre>';
       print_r($content);
       exit;*/

       return $this->render('read',['content' => $content]);
    }

    public function getLoanApplicationinPDF($ApplicationNo)
    {

        $service = Yii::$app->params['ServiceName']['PortalReports'];

        $data = [
            'loanNo' => urldecode($ApplicationNo),
        ];
        $path = Yii::$app->navhelper->PortalReports($service, $data, 'GenerateLoanForm');
        // Yii::$app->recruitment->printrr($path);

        if (is_array($path)) {

            $imagePath = Yii::getAlias($path['return_value']);
            // Yii::$app->recruitment->printrr($imagePath);

            if (is_file($imagePath)) {
                $binary = file_get_contents($imagePath);
                $content = chunk_split(base64_encode($binary));
                return $content;
            }
            return '';
        }

        Yii::$app->session->setFlash('error', $path);
        return $this->redirect(['index']);
    }

    public function actionDownloadApplicationForm($Key){
        $LoanModel = $this->GetLoanDetails($Key);
        return $this->render('read',[
            'content' => $this->getLoanApplicationinPDF($LoanModel->Loan_No),
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
