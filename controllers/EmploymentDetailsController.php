<?php

namespace app\controllers;

use app\models\EmploymentDetails;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\filters\VerbFilter;
use app\models\MemberApplicationCard;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

class EmploymentDetailsController extends Controller
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
        $model = new EmploymentDetails();

        $service = Yii::$app->params['ServiceName']['MemberApplicationCard'];
        $memberApplication = Yii::$app->navhelper->readByKey($service, urldecode($Key));

        if (Yii::$app->request->isAjax) {
            if ($this->loadpost(Yii::$app->request->post()['EmploymentDetails'], $model) && $model->validate()) {
                $filter = [
                    'Application_No' => $memberApplication->Application_No,
                ];
                $refresh = Yii::$app->navhelper->getData($service, $filter);
                $model->Key = $refresh[0]->Key;
                $result = Yii::$app->navhelper->updateData($service, $model);

                if (is_object($result)) {
                    // Yii::$app->session->setFlash('success','Employment Details Information Data Added Successfully',true);
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
        return $this->render('index', ['model' => $model, 'Employers' => ArrayHelper::map($this->getEmployers(), 'Code', 'Name')]);
    }

    public function getEmployers()
    {
        $service = Yii::$app->params['ServiceName']['Employers'];
        $res = [];
        $Employers = \Yii::$app->navhelper->getData($service);
        if (is_array($Employers)) {
            foreach ($Employers as $Employer) {
                if (isset($Employer->Code))
                    $res[] = [
                        'Code' => @$Employer->Code,
                        'Name' => @$Employer->Name
                    ];
            }
        }

        return $res;
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
