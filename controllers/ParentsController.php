<?php
namespace app\controllers;
use app\models\AccountSignatoriesList;
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
use app\models\ParentsDetails;


class ParentsController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
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
                        //'roles' => ['@'],
                        'matchCallback' => function($rule,$action){
                            return (Yii::$app->session->has('HRUSER') || !Yii::$app->user->isGuest);
                        },
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
                'only' => ['getparents'],
                'formatParam' => '_format',
                'formats' => [
                    'application/json' => Response::FORMAT_JSON,
                    //'application/xml' => Response::FORMAT_XML,
                ],
            ]
        ];
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

    public function actionIndex($Key){
        $model = new ParentsDetails();
        $ApplicantData = $this->ApplicantDetails($Key);
        $model->Member_Category = $ApplicantData->Member_Category;
        return $this->render('index', ['model' => $model,
         'Applicant'=>$ApplicantData,
    ]);

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

    public function actionCreate($Key){

        $model = new ParentsDetails();
        $service = Yii::$app->params['ServiceName']['ParentsDetails'];
        $ApplicantionData = $this->ApplicantDetails($Key);
        $model->Application_No = $ApplicantionData->Application_No;
        if(Yii::$app->request->post() && $this->loadpost(Yii::$app->request->post()['ParentsDetails'],$model)){

            // echo '<pre>';
            // print_r($model);
            // exit;

            $result = Yii::$app->navhelper->postData($service,$model);

            if(is_object($result)){

                Yii::$app->session->setFlash('success','Parent Added Successfully',true);
                return $this->redirect(['index', 'Key'=> $ApplicantionData->Key]);

            }else{
                Yii::$app->session->setFlash('error',$result,true);
                return $this->redirect(['index','Key'=> $ApplicantionData->Key]);

            }

        }

        if(Yii::$app->request->isAjax){
            return $this->renderAjax('create', [
                'model' => $model,
                'Members'=>$this->getMembers(),

            ]);
        }
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
        $service = Yii::$app->params['ServiceName']['ParentsDetails'];
        $filter = [
            'Key' => urldecode(Yii::$app->request->get('Key')),
        ];
        $result = Yii::$app->navhelper->readByKey($service, urldecode(Yii::$app->request->get('Key')));

        $model = new ParentsDetails();
        //load nav result to model
        $model = $this->loadtomodel($result,$model);

        if(Yii::$app->request->post() && $this->loadpost(Yii::$app->request->post()['ParentsDetails'],$model)){
            $result = Yii::$app->navhelper->updateData($service,$model);
            if(!empty($result)){
                Yii::$app->session->setFlash('success','Parent Updated Successfully',true);
                return $this->redirect(Yii::$app->request->referrer);
            }else{
                Yii::$app->session->setFlash('error',$result);
                return $this->redirect(Yii::$app->request->referrer);
            }

        }

        if(Yii::$app->request->isAjax){
            return $this->renderAjax('update', [
                'model' => $model,
                'Members'=>$this->getMembers(),
            ]);
        }

 
    }

    public function actionDelete(){
        $service = Yii::$app->params['ServiceName']['AccountSignatoriesList'];
        $result = Yii::$app->navhelper->deleteData($service,urldecode(Yii::$app->request->get('Key')));
        if(!is_string($result)){
            Yii::$app->session->setFlash('success','Signatory Removed Successfully .',true);
            return $this->redirect(['index']);
        }else{
            Yii::$app->session->setFlash('error','Signatory Removed Successfully: '.$result,true);
            return $this->redirect(['index']);
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



    public function actionGetparents($AppNo){
        $service = Yii::$app->params['ServiceName']['ParentsDetails'];
        if(empty(Yii::$app->session->get('ChildAccountNo'))){
            $filter = [
                'Application_No' => $AppNo
            ];

        }else{
            $filter = [
                'Application_No' => Yii::$app->session->get('ChildAccountNo')
            ];
        }
        $parents = Yii::$app->navhelper->getData($service,$filter);

        


        $result = [];
        $count = 0;
      
        if(!is_object($parents)){
            foreach($parents as $parent){
                if(empty($parent->First_Name) && empty($parent->Surname)){ //Useless KIn this One
                    continue;
                }
                $data = $this->GetApplicationDetails($parent->Application_No);
  
                ++$count;
                $link = $updateLink =  '';
                if($data->Portal_Status == 'New'){
                    $updateLink = Html::a('Edit',['update','Key'=> urlencode($parent->Key) ],['class'=>'update btn btn-info btn-md']);
                    $link = Html::a('Remove',['delete','Key'=> urlencode($parent->Key) ],['class'=>'btn btn-danger btn-md']);    
                }else{
                    $updateLink = ''; // Html::a('N\A',['#','Key'=> urlencode($parent->Key) ],['class'=>'btn btn-info btn-md']);
                    $link = ''; //Html::a('N\A',['#','Key'=> urlencode($parent->Key) ],['class'=>'btn btn-danger btn-md']); 
                }
               
                $result['data'][] = [
                    'index' => $count,
                    'First_Name' => !empty($parent->First_Name)?$parent->First_Name:'',
                    'Surname' => !empty($parent->Surname)?$parent->Surname:'',
                    'Other_Names' => !empty($parent->Other_Names)?$parent->Other_Names:'',
                    'Mobile_Phone' => !empty($parent->Mobile_Phone)?$parent->Mobile_Phone:'Not set',
                    'National_ID_No' => !empty($parent->National_ID_No)?$parent->National_ID_No:'Not set',
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