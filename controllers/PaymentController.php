<?php
namespace app\controllers;
use app\models\MpesaDeposit;
use Yii;
use yii\filters\AccessControl;
use yii\filters\ContentNegotiator;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\Controller;
use frontend\models\Leave;
use yii\web\Response;


class PaymentController extends Controller
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
                'only' => ['getkins'],
                'formatParam' => '_format',
                'formats' => [
                    'application/json' => Response::FORMAT_JSON,
                    //'application/xml' => Response::FORMAT_XML,
                ],
            ]
        ];
    }

    public function actionIndex(){
      
        return $this->render('index');

    }

    public function actionDeposit(){

        $model = new MpesaDeposit();
        $model->Application_No = Yii::$app->user->identity->ApplicationId;
        $model->Mobile_Phone_No = Yii::$app->user->identity->phoneNo;
        $service = Yii::$app->params['ServiceName']['MemberApplication_KINs'];

        if(Yii::$app->request->post() && $this->loadpost(Yii::$app->request->post()['MemberApplication_KINs'],$model)){

            $model->App_No = Yii::$app->user->identity->ApplicationId;

            $result = Yii::$app->navhelper->postData($service,$model);

            if(is_object($result)){

                Yii::$app->session->setFlash('success','Kin Added Successfully',true);
                return $this->redirect(['index']);

            }else{

                Yii::$app->session->setFlash('error','Kin Adding Hobby: '.$result,true);
                return $this->redirect(['index']);

            }

        }

        if(Yii::$app->request->isAjax){
            return $this->renderAjax('create', [
                'model' => $model,
            ]);
        }
    }
}