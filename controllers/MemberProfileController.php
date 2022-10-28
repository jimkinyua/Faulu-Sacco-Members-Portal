<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\filters\VerbFilter;
use app\models\MemberProfileCard;


class MemberProfileController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => [
                    'index'
                ],
                'rules' => [
                    [
                        'actions' => ['index'],
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

    public function getMemberDetails($Key){
        $model = new MemberProfileCard();
        $service = Yii::$app->params['ServiceName']['CustomerCard'];
        $memberApplication = Yii::$app->navhelper->readByKey($service, urldecode($Key));
       return $model = $this->loadtomodel($memberApplication,$model);
    }

    public function actionIndex($Key)
    {
        $model = new MemberProfileCard();
        $service = Yii::$app->params['ServiceName']['CustomerCard'];
        $MmeberData = Yii::$app->navhelper->readByKey($service, urldecode($Key));
        $model = $this->loadtomodel($MmeberData,$model);
        return $this->render('index', [
            'model' => $model,
            'Applicant'=>$this->getMemberDetails($Key),
            'MembershipTypes'=>$this->getMembershipTypes(),
            'groupTypes'=>[], //$this->getGroupTypes(),
            'constituencies'=> [], //$this->getConstituencies(),

        ]);
    }
    public function getMembershipTypes(){
        $service = Yii::$app->params['ServiceName']['MemberCategories'];
        $res = [];
        $MemberCategories = \Yii::$app->navhelper->getData($service);
        foreach($MemberCategories as $MemberCategory){
            if(!empty($MemberCategory->Code))
            $res[] = [
                'Code' => $MemberCategory->Code,
                'Name' => $MemberCategory->Description
            ];
        }

        return $res;
    }

  
    
    public function getGroupTypes(){
        $service = Yii::$app->params['ServiceName']['GroupCategories'];
        $res = [];
        $GroupCategories = \Yii::$app->navhelper->getData($service);
        foreach($GroupCategories as $GroupCategry){
            if(!empty($GroupCategry->Group_Type))
            $res[] = [
                'Code' => $GroupCategry->Group_Type,
                'Name' => $GroupCategry->Group_Description
            ];
        }

        return $res;
    }

    

    public function getConstituencies(){
        $service = Yii::$app->params['ServiceName']['ConstituenciesList'];
        $res = [];
        $Constituencies = \Yii::$app->navhelper->getData($service);
        foreach($Constituencies as $Constituency){
            if(!empty($Constituency->No))
            $res[] = [
                'Code' => $Constituency->No,
                'Name' => $Constituency->Description
            ];
        }

        return $res;
    }

    public function actionGetSubConstituency(){
        $service = Yii::$app->params['ServiceName']['SubConstituenciesLines'];
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $out = [];
        if (isset($_POST['depdrop_parents'])) {
            $parents = $_POST['depdrop_parents'];
            if ($parents != null) {
                $cat_id = $parents[0];
                $filter = [
                    'Constituency_Code'=>$cat_id
                ];
                $SubConstituencies = \Yii::$app->navhelper->getData($service, $filter);
                if(is_object($SubConstituencies)){
                  $out[] = ['id' => '', 'name' =>''];
                }else{
                    foreach ($SubConstituencies as $i => $account) {
                        $out[] = ['id' => $account->No, 'name' => $account->Description];
                    }
                }
                return ['output'=>$out, 'selected'=>''];
            }
        }

    }


    public function actionDeclaration($Key){
         $model = new MemberProfileCard();
        $service = Yii::$app->params['ServiceName']['CustomerCard'];
        $memberApplication = Yii::$app->navhelper->readByKey($service, urldecode($Key));

        $model = $this->loadtomodel($memberApplication,$model);

        if($model->load(Yii::$app->request->post()) && Yii::$app->request->post()){
         
            $service = Yii::$app->params['ServiceName']['PortalFactory'];

            $data = [
                'applicationNo' =>  $memberApplication->Application_No,
                'sendMail' => 1,
                'approvalUrl' => '',
            ];
       
            $result = Yii::$app->navhelper->PortalWorkFlows($service,$data,'Submitmemberprofile');
            //       echo '<pre>';
            // print_r($result);
            // exit;
            

            if(!is_string($result)){
                Yii::$app->session->setFlash('success', 'Your Application has been successfully submitted. Thank you for choosing us', true);
                return $this->redirect(['site/index']);
            }else{
    
                Yii::$app->session->setFlash('error', $result);
                return $this->redirect(['index', 'Key'=>$memberApplication->Key]);
    
            }
        }

        return $this->render('declaration', [
            'model' => $model,
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
