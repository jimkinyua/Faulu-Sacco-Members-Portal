<?php

namespace app\controllers;
use app\models\DividendAdvance;
use yii\filters\AccessControl;
use yii\helpers\Url;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\filters\ContentNegotiator;
use Yii;
use yii\helpers\Html;
class DividendAdvanceController extends \yii\web\Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['index', 'apply','get-dividend-qualifications'],
                'rules' => [
                     [
                        'actions' => ['index', 'apply', 'get-dividend-qualifications'],
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
                'only' => ['get-dividend-qualifications'],
                'formatParam' => '_format',
                'formats' => [
                    'application/json' => Response::FORMAT_JSON,
                    //'application/xml' => Response::FORMAT_XML,
                ],
            ]
        ];
    }

    public function actionIndex()
    {
        return $this->render('index');
    }
 
    public function actionGetDividendQualifications(){
        $service = Yii::$app->params['ServiceName']['DividendAdvance'];
        $filter = [
            'Member_No' => Yii::$app->user->identity->{'No_'},
        ];
        $DividendAdvances = Yii::$app->navhelper->getData($service,$filter);
        // echo '<pre>';
        // print_r($DividendAdvance);
        // exit;
        $result = [];
        $count = 0;
      
        if(!is_object($DividendAdvances)){
            foreach($DividendAdvances as $changeRequest){

                // if(empty($loan->First_Name) && empty($loan->Last_Name) && $loan->Type == '_blank_' ){ //Useless loan this One
                //     continue;
                // }
                ++$count;
                $link = $updateLink =  '';
                // $updateLink = Html::a('Edit',['update','Key'=> urlencode($changeRequest->Key) ],['class'=>'update btn btn-info btn-md']);
                $updateLink = Html::a('Apply For Dividend Advance',['apply','Key'=> urlencode($changeRequest->Key) ],['class'=>'update btn btn-info btn-md']);


                //Html::a('Apply For Dividend Advance',['apply','Key'=> urlencode($changeRequest->Key) ],['class'=>'create btn btn-info btn-md']);
                            //   Html::a('Edit Kin',['update','Key'=> urlencode($kin->Key) ],['class'=>'update btn btn-info btn-md']);
                $result['data'][] = [
                    'index' => $count,
                    'Period_Code' => !empty($changeRequest->Period_Code)?$changeRequest->Period_Code:'',
                    'Total_Savings' => !empty($changeRequest->Total_Savings)?number_format($changeRequest->Total_Savings):'',
                    'Qualified_Amount' => !empty($changeRequest->Qualified_Amount)?number_format($changeRequest->Qualified_Amount):'',
                    'Total_Interest_Earned' => !empty($changeRequest->Total_Interest_Earned)?number_format($changeRequest->Total_Interest_Earned):'',
                    'Update_Action' => $updateLink,
                ];
            }
        
        }
           
      

        return $result;
    }



    public function actionApply($Key){

        $model = new DividendAdvance();
        $service = Yii::$app->params['ServiceName']['DividendAdvance'];
        $result = Yii::$app->navhelper->readByKey($service, urldecode(Yii::$app->request->get('Key')));
        $model = $this->loadtomodel($result,$model);
         
        if(Yii::$app->request->post() && $this->loadpost(Yii::$app->request->post()['DividendAdvance'],$model)){
            $service = Yii::$app->params['codeUnits']['CreditPortalManagement'];
            $data = [
                'periodCode' => $model->Period_Code,
                'memberNo' => Yii::$app->user->identity->{'No_'},
                'amountApplied' =>(int)$model->AppliedAmount,
                'responseCode'=>'',
                'responseMessage'=>''
            ];

            // echo '<pre>';
            // print_r($model);
            // exit;

            $PostResult = Yii::$app->navhelper->PortalWorkFlows($service,$data,'SubmitDividedAdvanceApplication');

            if(!is_string($PostResult)){
                if($PostResult['responseCode'] == '01'){ //Error Manenos
                    Yii::$app->session->setFlash('error',$PostResult['responseMessage']);
                    return $this->redirect(['index']);
                }
                // $Message = ' Hello '. $model->Member_Name .' We have Received Your Acceptance To Guarantee '. $model->Loan_Principal;
                // $this->SendSMS($Message, $model->PhoneNo);
                Yii::$app->session->setFlash('success', 'Dividend Advance Submitted Successfuly', true);
                return $this->redirect(['index']);
            }

         
          

        }
        return $this->render('create', [
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
