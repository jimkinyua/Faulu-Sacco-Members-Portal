<?php
namespace app\controllers;
use app\models\AccountSignatoriesList;
use Yii;
use yii\filters\AccessControl;
use yii\filters\ContentNegotiator;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\Controller;
use frontend\models\Leave;
use yii\web\Response;
use app\models\MemberApplicationCard;
use app\models\GroupSigatoriesAttachements;
use yii\web\UploadedFile;

class SignatoriesController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'user'=>'applicant', // this user object defined in web.php
                'only' => ['logout', 'signup','index'],
                'rules' => [
                    [
                        'actions' => ['signup'],
                        'allow' => true,
                        'roles' => ['?'],
                    ],
                     [
                        'actions' => ['logout','index'],
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
                'only' => ['getsignatories'],
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
        $model = new MemberApplicationCard();
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

        $model = new AccountSignatoriesList();
        $GroupSigatoriesAttachemeModel = new GroupSigatoriesAttachements();
        $service = Yii::$app->params['ServiceName']['AccountSignatoriesList'];
        $ApplicantionData = $this->ApplicantDetails($Key);
        $model->Application_No = $ApplicantionData->Application_No;
        $attachements =false;

        if(Yii::$app->request->post() && $this->loadpost(Yii::$app->request->post()['AccountSignatoriesList'],$model)){
            
            $upres = $GroupSigatoriesAttachemeModel->CreateSignatoryOnEDMS($model);
           
            if($upres == false){//EDMS Down
             Yii::$app->session->setFlash('error','We are unable  the attachments at the moment. kindly try again after a few minutes');
                 return $this->redirect(['index', 'Key'=>$ApplicantionData->Key]);
            }

            if($upres == true){ //Try Updating
                $upres = $GroupSigatoriesAttachemeModel->UpdateSignatoryDetailsOnEDMS($model);
            }

             $GroupSigatoriesAttachemeModel->DocNum = $model->Application_No;
             $GroupSigatoriesAttachemeModel->IdentificationNo = $model->ID_No;
             foreach($this->getGroupSigatoriesAtttachements() as $requiredDoc){
                 $parameterName = str_replace(' ', '_', $requiredDoc['Name']);
                 $parameterName = str_replace('.', '_', $parameterName);
                 $GroupSigatoriesAttachemeModel->uploadFilesArray = UploadedFile::getInstancesByName($parameterName);
                 
                 foreach($GroupSigatoriesAttachemeModel->uploadFilesArray as $File){
                     $GroupSigatoriesAttachemeModel->docFile = $File;
                     $GroupSigatoriesAttachemeModel->FileName =  $requiredDoc['Name'];
                     $GroupSigatoriesAttachemeModel->upload();
                 }
             }

            $model->Application_No = $ApplicantionData->Application_No;
            $result = Yii::$app->navhelper->postData($service,$model);

            if(is_object($result)){

                Yii::$app->session->setFlash('success','Signatory Added Successfully',true);
                return $this->redirect(['index','Key'=> $ApplicantionData->Key]);

            }else{

                Yii::$app->session->setFlash('error',$result,true);
                return $this->redirect(['index','Key'=> $ApplicantionData->Key]);

            }

        }

        if(Yii::$app->request->isAjax){
            return $this->renderAjax('create', [
                'model' => $model,
                'Members'=>$this->getMembers(),
                'GroupSigatoriesAttachemeModel'=>$GroupSigatoriesAttachemeModel,
                'RequiredAttachements'=>$this->getGroupSigatoriesAtttachements(),
                'MyAttachedDocs'=>$attachements

            ]);
        }
    }

    public function getGroupSigatoriesAtttachements(){
        $res = [
            ['Id' => 2,'Name' => 'Copy of National I.D'],
            ['Id' => 3,'Name' => 'Coloured Passport photograph'],
            ['Id' => 4,'Name' => 'Copy of KRA PIN'],
        ];
        return $res;
    }

    public function getMembers(){
        $service = Yii::$app->params['ServiceName']['Members'];
        $res = [];
        $Members = \Yii::$app->navhelper->getData($service);
        foreach($Members as $Member){
            if(!empty($Member->No))
            $res[] = [
                'Code' => $Member->No,
                'Name' => $Member->Name
            ];
        }

        return $res;
    }

    public function actionUpdate(){
        $model = new AccountSignatoriesList();
        $GroupSigatoriesAttachemeModel = new GroupSigatoriesAttachements();
        $service = Yii::$app->params['ServiceName']['AccountSignatoriesList'];
        $filter = [
            'Key' => urldecode(Yii::$app->request->get('Key')),
        ];
        $result = Yii::$app->navhelper->readByKey($service, urldecode(Yii::$app->request->get('Key')));
        $ApplicationData = $this->ApplicantDetailWithDocNum($result->Application_No);
        //load nav result to model
        $model = $this->loadtomodel($result,$model);
        $attachements = $GroupSigatoriesAttachemeModel->getAttachments( $model->Application_No, $model->ID_No);

        if(Yii::$app->request->post() && $this->loadpost(Yii::$app->request->post()['AccountSignatoriesList'],$model)){

            $upres = $GroupSigatoriesAttachemeModel->UpdateSignatoryDetailsOnEDMS($model);
            if($upres == false){//EDMS Down
             Yii::$app->session->setFlash('error','We are unable  the attachments at the moment. kindly try again after a few minutes');
                 return $this->redirect(['index', 'Key'=>$ApplicationData->Key]);
            }

            

            $GroupSigatoriesAttachemeModel->DocNum = $model->Application_No;
            $GroupSigatoriesAttachemeModel->IdentificationNo = $model->ID_No;
            foreach($this->getGroupSigatoriesAtttachements() as $requiredDoc){
                $parameterName = str_replace(' ', '_', $requiredDoc['Name']);
                $parameterName = str_replace('.', '_', $parameterName);
                $GroupSigatoriesAttachemeModel->uploadFilesArray = UploadedFile::getInstancesByName($parameterName);
                
                foreach($GroupSigatoriesAttachemeModel->uploadFilesArray as $File){
                    $GroupSigatoriesAttachemeModel->docFile = $File;
                    $GroupSigatoriesAttachemeModel->FileName =  $requiredDoc['Name'];
                    $GroupSigatoriesAttachemeModel->upload();
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
                'Members'=>$this->getMembers(),
                'GroupSigatoriesAttachemeModel'=>$GroupSigatoriesAttachemeModel,
                'RequiredAttachements'=>$this->getGroupSigatoriesAtttachements(),
                'MyAttachedDocs'=>$attachements
            ]);
        }

 
    }

    public function actionRead($Key){
        $model = new GroupSigatoriesAttachements();
        $content = $model->read($Key);
     //    print '<pre>';
     //    print_r($content); exit;
        return $this->render('read',['content' => $content]);
     }
 

    public function actionDelete(){
        $service = Yii::$app->params['ServiceName']['AccountSignatoriesList'];
        $result = Yii::$app->navhelper->deleteData($service,urldecode(Yii::$app->request->get('Key')));
        if(!is_string($result)){
            Yii::$app->session->setFlash('success','Signatory Removed Successfully .',true);
            return $this->redirect(Yii::$app->request->referrer);
        }else{
            Yii::$app->session->setFlash('error','Signatory Removed Successfully: '.$result,true);
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
        $leaveModel = new AccountSignatoriesList();
        $model = $this->loadtomodel($leave[0],$leaveModel);


        return $this->render('view',[
            'model' => $model,
            'leaveTypes' => ArrayHelper::map($leaveTypes,'Code','Description'),
            'relievers' => ArrayHelper::map($employees,'No','Full_Name'),
        ]);
    }



    public function actionGetsignatories($AppNo){
        $service = Yii::$app->params['ServiceName']['AccountSignatoriesList'];
        $filter = [
            'Application_No' => $AppNo,
        ];
        $signatories = Yii::$app->navhelper->getData($service,$filter);

        // echo '<pre>';
        // print_r($signatories);
        // exit;


        $result = [];
        $count = 0;
      
        if(!is_object($signatories)){
            foreach($signatories as $kin){

                if(empty($kin->First_Name) && empty($kin->Last_Name)){ //Useless KIn this One
                    continue;
                }
                ++$count;
                $link = $updateLink =  '';
                $data = $this->ApplicantDetailWithDocNum($kin->Application_No);


                if($data->Portal_Status == 'New'){
                    $updateLink = Html::a('Edit',['update','Key'=> urlencode($kin->Key) ],['class'=>'update btn btn-info btn-md']);
                    $link = Html::a('Remove',['delete','Key'=> urlencode($kin->Key) ],['class'=>'btn btn-danger btn-md']);
                }else{
                    $updateLink = '';
                    $link = '';
                }

                $result['data'][] = [
                    'index' => $count,
                    'First_Name' => !empty($kin->First_Name)?$kin->First_Name:'',
                    'Middle_Name' => !empty($kin->Middle_Name)?$kin->Middle_Name:'',
                    'Date_of_Birth' => !empty($kin->Date_of_Birth)?$kin->Date_of_Birth:'',
                    'Must_Be_Present' => !empty($kin->Middle_Name)?'Yes':'No',
                    'Must_Sign' => !empty($kin->Must_Sign)?'Yes':'No',
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