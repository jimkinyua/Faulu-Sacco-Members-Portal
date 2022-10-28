<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\filters\VerbFilter;
use app\models\MemberApplicationCard;
use app\models\RefferalModel;
use yii\bootstrap4\Html;
use yii\web\Response;
use yii\filters\ContentNegotiator;



class ReferralController extends Controller
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
                        'actions' => ['index', 'get-marketers'],
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
                'only' => ['getkins', 'get-marketers', 'get-loan-securities'],
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

    public function actionIndex($Key)
    {

        $this->layout = 'applicant-main';
        $model = new RefferalModel();
        $service = Yii::$app->params['ServiceName']['MemberApplicationCard'];
        $memberApplication = Yii::$app->navhelper->readByKey($service, urldecode($Key));


        if (Yii::$app->request->isAjax) {
            if ($this->loadpost(Yii::$app->request->post()['RefferalModel'], $model) && $model->validate()) {
                // echo '<pre>';
                // print_r($model);
                // exit;

                $filter = [
                    'No' => $memberApplication->No,
                ];
                $refresh = Yii::$app->navhelper->getData($service, $filter);
                $model->Key = $refresh[0]->Key;
                $result = Yii::$app->navhelper->updateData($service, $model);

                if (is_object($result)) {
                    // Yii::$app->session->setFlash('success','Refferal Information Data Added Successfully',true);
                    return $this->redirect(['/profile/declaration', 'Key' => $Key]);
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
        return $this->render('index', ['model' => $model]);
    }

    public function actionGetMarketers()
    {
        $service = Yii::$app->params['ServiceName']['SalesPeople'];
        $filter = [];
        $arr = [];
        $i = 0;
        $result = \Yii::$app->navhelper->getData($service);
        if (is_object($result)) {
        } else {
            foreach ($result as $res) {
                if (isset($res->Code)) {
                    ++$i;
                    $arr[$i] = [
                        'Code' => @$res->Code,
                        'Description' => @$res->Name
                    ];
                }
            }
        }

        return $arr;
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


        if (isset($model->Relationship_Officer)) {
            // $model->refferedBy = 'Mhasibu_Staff';
            // $model->Referrer =$model->Relationship_Officer;
        } else {
            // $model->refferedBy ='Member';
            // $model->Referrer =$model->Member_No;
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
