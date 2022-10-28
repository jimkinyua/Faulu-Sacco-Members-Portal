<?php

namespace app\controllers;

use app\models\CommunicationModel;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\filters\VerbFilter;
use app\models\MemberApplicationCard;
use yii\filters\ContentNegotiator;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\Response;



class CommunicationController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'user' => 'applicant', // this user object defined in web.php
                'only' => ['index'],
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

            'contentNegotiator' => [
                'class' => ContentNegotiator::class,
                'only' => ['sub-counties',],
                'formatParam' => '_format',
                'formats' => [
                    'application/json' => Response::FORMAT_JSON,
                    //'application/xml' => Response::FORMAT_XML,
                ],
            ]
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

    public function getCounties()
    {
        // $service = Yii::$app->params['ServiceName']['Counties'];
        $res = [];
        // $LoanProducts = \Yii::$app->navhelper->getData($service);
        // foreach ($LoanProducts as $LoanProduct) {
        //     if (isset($LoanProduct->County_Code))
        //         $res[] = [
        //             'Code' => @$LoanProduct->County_Code,
        //             'Name' => @$LoanProduct->Name
        //         ];
        // }

        return $res;
    }

    public function actionSubCounties()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $out = [];


        if (isset($_POST['depdrop_parents'])) {
            $parents = $_POST['depdrop_parents'];
            if ($parents != null) {
                $cat_id = $parents[0];
                $cat_id = $parents[0];

                return ['output' => $this->getSubCountiesFromNav($cat_id), 'selected' => ''];
            }
        }
        return ['output' => '', 'selected' => ''];
    }

    public function getSubCountiesFromNav($County)
    {
        $service = Yii::$app->params['ServiceName']['SubCounties'];
        $filter = [
            'County_Code' => $County
        ];
        $res = [];
        $out = [];
        $SubCounties = \Yii::$app->navhelper->getData($service, $filter);
        if (is_array($SubCounties)) {
            foreach ($SubCounties as $i => $account) {
                $out[] = ['id' => $account->Sub_County_Code, 'name' => $account->Sub_County_Name];
            }
        }

        return  $out;
    }

    public function getSubCounties()
    {
        $service = Yii::$app->params['ServiceName']['Counties'];
        $res = [];
        $LoanProducts = \Yii::$app->navhelper->getData($service);
        foreach ($LoanProducts as $LoanProduct) {
            if (isset($LoanProduct->County_Code))
                $res[] = [
                    'Code' => @$LoanProduct->County_Code,
                    'Name' => @$LoanProduct->Name
                ];
        }

        return $res;
    }

    public function actionIndex($Key)
    {
        $this->layout = 'applicant-main';
        $model = new CommunicationModel();
        $service = Yii::$app->params['ServiceName']['MemberApplicationSingle'];
        $memberApplication = Yii::$app->navhelper->readByKey($service, urldecode($Key));


        if (Yii::$app->request->isAjax) {
            if ($this->loadpost(Yii::$app->request->post()['CommunicationModel'], $model) && $model->validate()) {
                $filter = [
                    'No' => $memberApplication->No,
                ];
                $refresh = Yii::$app->navhelper->getData($service, $filter);
                $model->Key = $refresh[0]->Key;
                $model->First_Name = null;
                $model->Second_Name = null;
                $model->Last_Name = null;

                $result = Yii::$app->navhelper->updateData($service, $model);

                if (is_object($result)) {
                    // Yii::$app->session->setFlash('success','Communication Information Data Added Successfully',true);
                    return $this->redirect(['/kin', 'Key' => $Key]);
                } else {
                    return $this->asJson(['error' => $result]);
                }
            }

            $result = [];
            // The code below comes from ActiveForm::validate(). We do not need to validate the model
            // again, as it was already validated by save(). Just collect the messages.
            foreach ($model->getErrors() as $attribute => $errors) {
                $result[Html::getInputId($model, $attribute)] = $errors;
            }

            return $this->asJson(['validation' => $result]);
        }

        $model = $this->loadtomodel($memberApplication, $model);
        return $this->render('index', ['model' => $model, 'Counties' => ArrayHelper::map($this->getCounties(), 'Code', 'Name')]);
    }


    public function loadtomodel($obj, $model)
    {

        if (!is_object($obj)) {
            return false;
        }
        $modeldata = (get_object_vars($obj));
        foreach ($modeldata as $key => $val) {
            if (is_object($val)) continue;
            $model->$key = $val;
        }

        return $model;
    }

    public function loadpost($post, $model)
    { // load model with form data


        $modeldata = (get_object_vars($model));

        foreach ($post as $key => $val) {

            $model->$key = $val;
        }

        return $model;
    }
}
