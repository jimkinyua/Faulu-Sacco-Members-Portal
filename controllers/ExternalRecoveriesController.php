<?php
namespace app\controllers;
use app\models\LoanExtRecoveries;
use app\models\LoanApplicationHeader;
use Yii;
use yii\filters\AccessControl;
use yii\filters\ContentNegotiator;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\Controller;
use yii\web\UploadedFile;
use yii\web\Response;


class ExternalRecoveriesController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['create', 'delete','index','update'],
                'rules' => [
                     [
                        'actions' => ['create', 'delete','index','update'],
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
                'only' => ['getkins', 'get-members', 'get-loan-securities'],
                'formatParam' => '_format',
                'formats' => [
                    'application/json' => Response::FORMAT_JSON,
                    //'application/xml' => Response::FORMAT_XML,
                ],
            ]
        ];
    }

    public function actionAccountTypes() {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $out = [];
    

        if (isset($_POST['depdrop_parents'])) {
            $parents = $_POST['depdrop_parents'];
            if ($parents != null) {
                $cat_id = $parents[0];
                $cat_id = $parents[0];

                if($cat_id == 'Loan'){
                    $out = $this->getLoanProducts(); 
                }

                if($cat_id == 'Share_Capital_Boost'){
                    $out = $this->getAccountsSetUp(); 
                }
                if($cat_id == 'NWD_Deposit'){
                    $out = $this->getNWDAccounts(); 
                }
                return ['output'=>$out, 'selected'=>''];
            }
        }
        return ['output'=>'', 'selected'=>''];
    }

    public function actionAccountNos() {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $out = [];

        if (isset($_POST['depdrop_parents'])) {
            $parents = $_POST['depdrop_parents'];
            if ($parents != null) {
                $cat_id = $parents[1];
                // if($cat_id == 'Loan'){
                //     $out = $this->getMemberLoans(); 
                // }

                // if($cat_id == 'Share_Capital_Boost'){
                //     $out = $this->getVendors($parents[0]); 
                // }
                if($cat_id == 'NWD_Deposit'){
                    $out = $this->getVendors($parents[0]); 
                }
                return ['output'=>$out, 'selected'=>''];
            }
        }
        return ['output'=>'', 'selected'=>''];
    }


    public function getLoanProducts(){
        $service = Yii::$app->params['ServiceName']['LoanProducts'];
        $res = [];
        $selected  = null;
        $LoanProducts = \Yii::$app->navhelper->getData($service);
        foreach ($LoanProducts as $i => $account) {
            $out[] = ['id' => $account->Product_Code, 'name' => $account->Product_Description];
            if ($i == 0) {
                $selected = $account->Product_Code;
            }
        }
        return  $out;
        return $res;
    }

        
    public function getAccountsSetUp(){
        $service = Yii::$app->params['ServiceName']['AccountTypes'];
        $filter = [
            'Share_Capital'=>1
        ];
        $AccountTypes = \Yii::$app->navhelper->getData($service, $filter);
        foreach ($AccountTypes as $i => $account) {
            $out[] = ['id' => $account->Code, 'name' => $account->Description];
        }
        return  $out;
    }

    public function getVendors($AccountType){
        $service = Yii::$app->params['ServiceName']['Vendors'];
        $filter = [
            'Account_Type'=>$AccountType,
            'Member_No'=>Yii::$app->user->identity->{'No_'},
        ];
        $AccountTypes = \Yii::$app->navhelper->getData($service, $filter);
        if(is_object($AccountTypes)){
            $out[] = ['id' => '', 'name' =>'No Data Available'];
        }else{
            foreach ($AccountTypes as $i => $account) {
                $out[] = ['id' => $account->No, 'name' => $account->Name];
            }
        }
       
        return  $out;
    }

    public function getMemberLoans(){
        $service = Yii::$app->params['ServiceName']['LoanApplications'];
        $filter = [
            'Member_No'=>Yii::$app->user->identity->{'No_'},
        ];
        $LoanApplications = \Yii::$app->navhelper->getData($service, $filter);
        foreach ($LoanApplications as $i => $LoanApplication) {
            $out[] = ['id' => $LoanApplication->Application_No, 'name' => $LoanApplication->Product_Description];
        }
        return  $out;
    }

    public function getNWDAccounts(){
        $service = Yii::$app->params['ServiceName']['AccountTypes'];
        $filter = [
            'NWD_Account'=>1
        ];
        $AccountTypes = \Yii::$app->navhelper->getData($service, $filter);
        foreach ($AccountTypes as $i => $account) {
            $out[] = ['id' => $account->Code, 'name' => $account->Description];
        }
        return  $out;
    }

    public function actionCreate($LoanNo){

        $model = new LoanExtRecoveries();
        $service = Yii::$app->params['ServiceName']['LoanExtRecoveries'];
        $model->Application_No = $LoanNo;
        if(Yii::$app->request->post() && $this->loadpost(Yii::$app->request->post()['LoanExtRecoveries'],$model)){
            $model->FileName = 'External Recovery';
            $model->docFile = UploadedFile::getInstance($model, 'docFile');
            if($model->upload() == true){
                Yii::$app->session->setFlash('success','Added Successfully');
                return $this->redirect(Yii::$app->request->referrer);
            }
            return $this->redirect(Yii::$app->request->referrer);
        }

        if(Yii::$app->request->isAjax){
            return $this->renderAjax('create', [
                'model' => $model,
                'ExternalRecoveries' => ArrayHelper::map($this->ExternalRecoveries(),'Code','Description'),

            ]);
        }
    }

    public function GetLoanDetails($LoanKey){
        $model = new LoanApplicationHeader();
        $service = Yii::$app->params['ServiceName']['LoanApplicationHeader'];
        $result = Yii::$app->navhelper->readByKey($service, urldecode($LoanKey));
       return $model = $this->loadtomodel($result,$model);
    }
    public function actionIndex($Key){
        $model = new LoanExtRecoveries();
        $LoanModel = $this->GetLoanDetails($Key);
        return $this->render('index', ['model' => $model, 'LoanModel'=>$LoanModel]);

    }

    public function actionUpdate(){
        $model = new LoanExtRecoveries();
        $service = Yii::$app->params['ServiceName']['LoanExtRecoveries'];
        $filter = [
            'Key' => urldecode(Yii::$app->request->get('Key')),
        ];
        if(Yii::$app->request->post() && $this->loadpost(Yii::$app->request->post()['LoanExtRecoveries'],$model)){
            $refresh = Yii::$app->navhelper->readByKey($service, urldecode(Yii::$app->request->get('Key')));

            $model->FileName = 'External Recovery';
            $model->docFile = UploadedFile::getInstance($model, 'docFile');
            if($model->upload() == true){
                Yii::$app->session->setFlash('success','Added Successfully');
                return $this->redirect(Yii::$app->request->referrer);
            }
            return $this->redirect(Yii::$app->request->referrer);

        }
        $result = Yii::$app->navhelper->readByKey($service, urldecode(Yii::$app->request->get('Key')));

        if(Yii::$app->request->isAjax){
            $model = $this->loadtomodel($result,$model);
            return $this->renderAjax('update', [
                'model' => $model,
                'ExternalRecoveries' => ArrayHelper::map($this->ExternalRecoveries(),'Code','Description'),
            ]);
        }

 
    }


    public function actionDelete(){
        $service = Yii::$app->params['ServiceName']['LoanExtRecoveries'];
        $result = Yii::$app->navhelper->deleteData($service,urldecode(Yii::$app->request->get('Key')));
        if(!is_string($result)){
            Yii::$app->session->setFlash('success','Removed Successfully .');
            return $this->redirect(Yii::$app->request->referrer);
        }else{
            Yii::$app->session->setFlash('error',$result);
            return $this->redirect(Yii::$app->request->referrer);
        }
    }

    public function ExternalRecoveries(){
        $service = Yii::$app->params['ServiceName']['ExternalRecoveriesSetup'];
        $filter = [];
        $arr = [];
        $i = 0;
        $result = \Yii::$app->navhelper->getData($service,$filter);
        foreach($result as $res){
            if(isset($res->Code)){
                ++$i;
                $arr[$i] = [
                    'Code' => @$res->Code,
                    'Description' => @$res->Description
                ];
            }
                
        }
        return $arr;
    }


    public function actionGetkins(){
        $service = Yii::$app->params['ServiceName']['MemberApplication_KINs'];
        $filter = [
            'App_No' => Yii::$app->user->identity->ApplicationId,
        ];
        $kins = Yii::$app->navhelper->getData($service,$filter);

        // echo '<pre>';
        // print_r($kins);
        // exit;


        $result = [];
        $count = 0;
      
        if(!is_object($kins)){
            foreach($kins as $kin){

                if(empty($kin->First_Name) && empty($kin->Last_Name) && $kin->Type == '_blank_' ){ //Useless KIn this One
                    continue;
                }
                ++$count;
                $link = $updateLink =  '';
                if(Yii::$app->user->identity->memberApplicationStatus == 'New'){
                    $updateLink = Html::a('Edit Kin',['update','Key'=> urlencode($kin->Key) ],['class'=>'update btn btn-info btn-md']);
                    $link = Html::a('Remove Kin',['delete','Key'=> urlencode($kin->Key) ],['class'=>'btn btn-danger btn-md']);
                }else{
                    $updateLink = Html::a('Edit Kin',['#','Key'=> urlencode($kin->Key) ],['class'=>'btn btn-info btn-md']);
                    $link = '';
                }
                $result['data'][] = [
                    'index' => $count,
                    'Type' => $kin->Type,
                    'First_Name' => !empty($kin->First_Name)?$kin->First_Name:'',
                    'Middle_Name' => !empty($kin->Middle_Name)?$kin->Middle_Name:'',
                    'DOB' => !empty($kin->DOB)?$kin->DOB:'',
                    'Allocation_Percent' => !empty($kin->Middle_Name)?$kin->Allocation_Percent:'',
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