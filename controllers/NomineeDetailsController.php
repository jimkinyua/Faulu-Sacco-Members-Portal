<?php
namespace app\controllers;
use app\models\NomineeDetails;
use app\models\NomineeAttachements;
use app\models\MemberApplicationCard;
use Yii;
use yii\filters\AccessControl;
use yii\filters\ContentNegotiator;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\Controller;
use frontend\models\Leave;
use yii\web\Response;
use yii\web\UploadedFile;


class NomineeDetailsController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'user'=>'applicant', // this user object defined in web.php
                'only' => ['getnominees', 'create', 'read', 'update', 'delete', 'update', 'index'],
                'rules' => [
                     [
                        'actions' => ['getnominees', 'create', 'read', 'update', 'delete', 'update', 'index'],
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
                'only' => ['getnominees'],
                'formatParam' => '_format',
                'formats' => [
                    'application/json' => Response::FORMAT_JSON,
                    //'application/xml' => Response::FORMAT_XML,
                ],
            ]
        ];
    }


    public function actionIndex($Key){
        $this->layout = 'applicant-main';
        $model = new NomineeDetails();
        $ApplicantData = $this->ApplicantDetails($Key);
        $model->Member_Category = $ApplicantData->Member_Category;
        return $this->render('index', ['model' => $model,
         'Applicant'=>$ApplicantData,
        ]);
    }

    public function ApplicantDetails($Key){
        $model = new MemberApplicationCard();
        $service = Yii::$app->params['ServiceName']['MemberApplicationCard'];
        $memberApplication = Yii::$app->navhelper->readByKey($service, urldecode($Key));
       return $model = $this->loadtomodel($memberApplication,$model);
    }

    public function ApplicantDetailWithDocNum($Docnum){
        $model = new MemberApplicationCard();
        $service = Yii::$app->params['ServiceName']['MemberApplicationCard'];
        $filter = [
            'Application_No'=>$Docnum
        ];
        $memberApplication = Yii::$app->navhelper->getData($service,$filter);
       return $model = $this->loadtomodel($memberApplication[0],$model);
    }

    

    public function actionCreate($Key){

        $model = new NomineeDetails();
        $NomineeAttachementModel = new NomineeAttachements();
        $service = Yii::$app->params['ServiceName']['NomineeDetails'];
        $ApplicantionData = $this->ApplicantDetails($Key);
        $attachements =false;

        if(Yii::$app->request->post() && $this->loadpost(Yii::$app->request->post()['NomineeDetails'],$model)){
            $model->Application_No = $ApplicantionData->Application_No;
            $NomineeAttachementModel->CreateNomineeOnEDMS($model);

            $NomineeAttachementModel->DocNum = $model->Application_No;
            $NomineeAttachementModel->IdentificationNo = $model->National_ID_No;
            foreach($this->getNomineeAttachements() as $requiredDoc){
                $parameterName = str_replace(' ', '_', $requiredDoc['Name']);
                $parameterName = str_replace('.', '_', $parameterName);
                $NomineeAttachementModel->uploadFilesArray = UploadedFile::getInstancesByName($parameterName);
                
                foreach($NomineeAttachementModel->uploadFilesArray as $File){
                    $NomineeAttachementModel->docFile = $File;
                    $NomineeAttachementModel->FileName =  $requiredDoc['Name'];
                    $NomineeAttachementModel->upload();
                }
            }

            $result = Yii::$app->navhelper->postData($service,$model);

            if(is_object($result)){

                Yii::$app->session->setFlash('success','Nominee Added Successfully',true);
                return $this->redirect(['index', 'Key'=>$ApplicantionData->Key]);

            }else{

                Yii::$app->session->setFlash('error','Nominee Adding Hobby: '.$result,true);
                return $this->redirect(['index', 'Key'=>$ApplicantionData->Key]);

            }

        }

        if(Yii::$app->request->isAjax){
            return $this->renderAjax('create', [
                'model' => $model,
                'NomineeAttachementModel'=>$NomineeAttachementModel,
                'RequiredAttachements'=>$this->getNomineeAttachements(),
                'MyAttachedDocs'=>$attachements
            ]);
        }
    }

    public function getNomineeAttachements(){
        $res = [
            ['Id' => 1,'Name' => 'Copy of nominee’s ID/Passport/Birth Certificate if a minor.'],
        ];
        return $res;
    }

    public function actionUpdate(){
        $this->layout = 'applicant-main';
        $NomineeAttachementModel = new NomineeAttachements();
        $model = new NomineeDetails();

        $service = Yii::$app->params['ServiceName']['NomineeDetails'];
        $filter = [
            'Key' => urldecode(Yii::$app->request->get('Key')),
        ];
        $result = Yii::$app->navhelper->readByKey($service, urldecode(Yii::$app->request->get('Key')));
        $ApplicationData = $this->ApplicantDetailWithDocNum($result->Application_No);
        //load nav result to model
        $model = $this->loadtomodel($result,$model);
        $attachements = $NomineeAttachementModel->getAttachments( $ApplicationData->Application_No);

        if(Yii::$app->request->post() && $this->loadpost(Yii::$app->request->post()['NomineeDetails'],$model)){
            
            $NomineeAttachementModel->DocNum = $model->Application_No;
            $NomineeAttachementModel->IdentificationNo = $model->National_ID_No;
            foreach($this->getNomineeAttachements() as $requiredDoc){
                $parameterName = str_replace(' ', '_', $requiredDoc['Name']);
                $parameterName = str_replace('.', '_', $parameterName);
                $NomineeAttachementModel->uploadFilesArray = UploadedFile::getInstancesByName($parameterName);
                
                foreach($NomineeAttachementModel->uploadFilesArray as $File){
                    $NomineeAttachementModel->docFile = $File;
                    $NomineeAttachementModel->FileName =  $requiredDoc['Name'];
                    $NomineeAttachementModel->upload();
                }
            }

            $result = Yii::$app->navhelper->updateData($service,$model);
            if(!empty($result)){
                Yii::$app->session->setFlash('success','Kin Updated Successfully',true);
                return $this->redirect(['index', 'Key'=>$ApplicationData->Key]);
            }else{
                Yii::$app->session->setFlash('error','Error Updating Kin : '.$result,true);
                return $this->redirect(['index', 'Key'=>$ApplicationData->Key]);
            }

        }

        if(Yii::$app->request->isAjax){
            return $this->renderAjax('update', [
                'model' => $model,
                'NomineeAttachementModel'=>$NomineeAttachementModel,
                'RequiredAttachements'=>$this->getNomineeAttachements(),
                'MyAttachedDocs'=>$attachements
            ]);
        }


    }

    
    public function actionRead($Key){
        $this->layout = 'applicant-main';
        $model = new NomineeAttachements();
        $content = $model->read($Key);
     //    print '<pre>';
     //    print_r($content); exit;
        return $this->render('read',['content' => $content]);
     }

    public function actionDelete(){
        $service = Yii::$app->params['ServiceName']['NomineeDetails'];
        $result = Yii::$app->navhelper->readByKey($service, urldecode(Yii::$app->request->get('Key')));

        $result = Yii::$app->navhelper->deleteData($service,urldecode(Yii::$app->request->get('Key')));
        if(!is_string($result)){
            Yii::$app->session->setFlash('success','Nominee Deleted Successfully .',true);
            return $this->redirect(Yii::$app->request->referrer);
            
        }else{
            Yii::$app->session->setFlash('error','Nominee Deleted Successfully: '.$result,true);
            return $this->redirect(Yii::$app->request->referrer);
        }
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



    public function actionGetnominees($AppNo){
        $service = Yii::$app->params['ServiceName']['NomineeDetails'];
        $filter = [
            'Application_No' => $AppNo,
        ];
        $ApplicantData = $this->ApplicantDetailWithDocNum($AppNo);

        $Nominees = Yii::$app->navhelper->getData($service,$filter);

        // echo '<pre>';
        // print_r($Nominees);
        // exit;


        $result = [];
        $count = 0;
      
        if(!is_object($Nominees)){
            foreach($Nominees as $Nominee){

                if(empty($Nominee->FullName) ){ //Useless Nominee this One
                    continue;
                }
                ++$count;
                $link = $updateLink =  '';
                if($ApplicantData->Portal_Status == 'New'){
                    $updateLink = Html::a('Edit',['update','Key'=> urlencode($Nominee->Key) ],['class'=>'update btn btn-info btn-md']);
                    $link = Html::a('Remove',['delete','Key'=> urlencode($Nominee->Key) ],['class'=>'btn btn-danger btn-md']);
                }else{
                    $updateLink = '';
                    $link = '';
                }
                $result['data'][] = [
                    'index' => $count,
                    'Relationship' => $Nominee->Relationship,
                    'FullName' => !empty($Nominee->FullName)?$Nominee->FullName:'',
                    'National_ID_No' => !empty($Nominee->National_ID_No)?$Nominee->National_ID_No:'',
                    'Email' => !empty($Nominee->Email)?$Nominee->Email:'',
                    'Percent_Allocation' => !empty($Nominee->Percent_Allocation)?$Nominee->Percent_Allocation:'',
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