<?php
namespace app\controllers;
use app\models\CRBClearanceCeriticates;
use app\models\LoanApplicationHeader;
use Yii;
use yii\filters\AccessControl;
use yii\filters\ContentNegotiator;
use yii\filters\VerbFilter;
use yii\helpers\Html;
use yii\web\Controller;
use yii\web\Response;
use yii\web\UploadedFile;


class ClearanceCeriticateController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['update', 'create','index', 'read', 'delete', 'list'],
                'rules' => [
                     [
                        'actions' => ['update', 'create','index', 'read', 'delete', 'list'],
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
                'only' => ['getkins'],
                'formatParam' => '_format',
                'formats' => [
                    'application/json' => Response::FORMAT_JSON,
                    //'application/xml' => Response::FORMAT_XML,
                ],
            ]
        ];
    }

 

    public function actionIndex($Key){
        $model = new CRBClearanceCeriticates();
        $service = Yii::$app->params['ServiceName']['CRBClearanceCeriticates'];
        $LoanModel = $this->GetLoanDetails($Key);
        $filter = [
            'LoanNo' => $LoanModel->Application_No
        ];

        $CRBAttachements= Yii::$app->navhelper->getData($service, $filter);
        $model->LoanNo = $LoanModel->Application_No;
        $model->MemberNo = $LoanModel->Member_No;

        if (Yii::$app->request->isPost) {
            $model->File_Name = Yii::$app->request->post()['Essfile']['File_Name'];
            $model->attachmentfile = UploadedFile::getInstance($model, 'docFile');
            if ($model->upload()) {
                // file is uploaded successfully
                return $this->redirect(['index', 'Key'=>$LoanModel->Key]);
            }
        }

        $this->loadtomodel($CRBAttachements,$model);
         return $this->render('index', ['model' => $model, 'LoanModel'=>$LoanModel]);


    }

    public function GetLoanDetails($LoanKey){
        $model = new LoanApplicationHeader();
        $service = Yii::$app->params['ServiceName']['LoanApplicationHeader'];
        $result = Yii::$app->navhelper->readByKey($service, urldecode($LoanKey));
       return $model = $this->loadtomodel($result,$model);
    }

    public function actionUpdate(){
        $service = Yii::$app->params['ServiceName']['ESS_Files'];
        $filter = [
            'Key' => urldecode(Yii::$app->request->get('Key')),
        ];
        $result = Yii::$app->navhelper->readByKey($service, urldecode(Yii::$app->request->get('Key')));
        $ApplicationData = $this->ApplicantDetailWithDocNum($result->Docnum);


        $model = new Attachement();
        //load nav result to model
        $model = $this->loadtomodel($result,$model);
        $model->isNewRecord = false;

        if (Yii::$app->request->isPost) {
            $model->File_Name = Yii::$app->request->post()['Attachement']['File_Name'];
            $model->docFile = UploadedFile::getInstance($model, 'docFile');
            if ($model->updateAttachement()) {
                // file is uploaded successfully
                return $this->redirect(['index', 'Key'=>$ApplicationData->Key]);
            }else{
                return $this->redirect(['index', 'Key'=>$ApplicationData->Key]);
            }
        }

        if(Yii::$app->request->isAjax){
            return $this->renderAjax('update', [
                'model' => $model,
                'RequiredDocuments'=>$this->getRequiredDocuments($model->Member_Category)
            ]);
        }

 
    }



    public function ApplicantDetailWithDocNum($Docnum){
        $model = new LoanApplicationHeader();
        $service = Yii::$app->params['ServiceName']['LoanApplicationHeader'];
        $filter = [
            'Application_No'=>$Docnum
        ];
        $memberApplication = Yii::$app->navhelper->getData($service,$filter);
       return $model = $this->loadtomodel($memberApplication[0],$model);
    }



    public function getRequiredDocuments(){
            $res[] = [
                'Code' => 'CRBClearance',
                'Name' => 'CRB Clearance'
            ];
       
        return $res;
    }

    
    public function actionCreate($Key){

        $model = new CRBClearanceCeriticates();
        $LoanModel = $this->GetLoanDetails($Key);
        $model->LoanNo = $LoanModel->Application_No;
        $model->MemberNo = $LoanModel->Member_No;

        if (Yii::$app->request->isPost) {
            $model->File_Name = Yii::$app->request->post()['CRBClearanceCeriticates']['File_Name'];
            $model->docFile = UploadedFile::getInstance($model, 'docFile');
            if ($model->upload()) {
                // file is uploaded successfully
                return $this->redirect(['index', 'Key'=>$LoanModel->Key]);
            }else{
                return $this->redirect(['index', 'Key'=>$LoanModel->Key]);

            }
            
        }

        if(Yii::$app->request->isAjax){
            return $this->renderAjax('create', [
                'model' => $model,
                'RequiredDocuments'=>$this->getRequiredDocuments()
            ]);
        }
    }


    public function actionRead($No)
    {
       $model = new CRBClearanceCeriticates();
       $content = $model->read($No);
       $ApplicationData = $this->GetLoanDetails(urldecode(Yii::$app->request->get('Key')));


       /*print '<pre>';
       print_r($content);
       exit;*/

       return $this->render('read',['content' => $content, 'ApplicationData'=>$ApplicationData]);
    }

    public function actionDelete(){
        $service = Yii::$app->params['ServiceName']['CRBClearanceCeriticates'];
        $result = Yii::$app->navhelper->readByKey($service, urldecode(Yii::$app->request->get('Key')));
        $ApplicationData = $this->ApplicantDetailWithDocNum($result->LoanNo);
 

        $result = Yii::$app->navhelper->deleteData($service,Yii::$app->request->get('Key'));
        if(!is_string($result)){
            Yii::$app->session->setFlash('success', 'Document Deleted Successfully.');
            return $this->redirect(['index', 'Key'=>$ApplicationData->Key]);
        }else{
           Yii::$app->session->setFlash('error', $result);
           return $this->redirect(['index', 'Key'=>$ApplicationData->Key]);
        }
    }

    public function GetApplicationDetails($ApplicationNo){
        $model = new MemberApplicationCard();
        $filter = [
            'Application_No' => $ApplicationNo
        ];
        $service = Yii::$app->params['ServiceName']['MemberApplicationCard'];
        $result = Yii::$app->navhelper->getData($service, $filter);

       return $model = $this->loadtomodel($result[0],$model);
    }


    public function actionList($AppNo){
        $model = new Attachement();
        $results = $model->getAttachments($AppNo);
     
        $result = [];
        if(is_array($results)){
            foreach($results as $item){
           
                if(empty($item->File_Name))
                {
                    continue;
                }
            
                $data = $this->GetApplicationDetails($item->Docnum);

                $link = Html::a('View Attachement',['read','No'=> $item->Line_No ],['title'=>'Read File','class'=>'btn btn-primary btn-md']);
                // $deleteLink = Html::a('Delete Attachement',['delete','Key'=> $item->Key],['class'=>'btn btn-danger btn-md']);
                $editLink = Html::a('Edit Attachement',['update','Key'=> $item->Key],['class'=>'btn btn-success btn-md update']);

                if($data->Portal_Status == 'New'){
                    $link = Html::a('View Attachement',['read','No'=> $item->Line_No ],['title'=>'Read File','class'=>'btn btn-primary btn-md']);
                    $deleteLink = Html::a('Delete Attachement',['delete','Key'=> $item->Key],['class'=>'btn btn-danger btn-md']);
                }else{
                    $link = Html::a('View Attachement',['read','No'=> $item->Line_No ],['title'=>'Read File','class'=>'btn btn-primary btn-md']);
                    $deleteLink = '';
                }

                 $docLink = Html::a($item->File_Name,['read','No'=> $item->Line_No],['class'=>'btn btn-success btn-xs','target'=> '_blank']);
            

            $result['data'][] = [
                'Key' => $item->Key,
                'File_Name' => $item->File_Name,
                'view' => $link.' '.$editLink
            ];
        }
        }
       

        Yii::$app->response->format = Response::FORMAT_JSON;

        return $result;
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