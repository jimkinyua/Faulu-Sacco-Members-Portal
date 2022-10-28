<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\filters\VerbFilter;
use app\models\MemberApplicationCard;
use yii\helpers\ArrayHelper;

class DownloadsController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['index', 'get-bank-branches'],
                'rules' => [
                    [
                        'actions' => ['index', 'get-bank-branches'],
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


    public function actionIndex()
    {

        // $data = [
        //     'IDnumber' => Yii::$app->user->identity->{'National ID No'},
        //     'MemberName' => Yii::$app->user->identity->{'Full Name'},
        // ];

        // Yii::$app->recruitment->LogActionHAsBeenAccessed($data, Yii::$app->user->identity->{'Full Name'} . ' Viewed the Downloads');


        $dir = new \DirectoryIterator(Yii::$app->params['SystemConfigs']['DownloadsFolder']);
        $Documents = [];
        foreach ($dir as $fileinfo) {
            if (!$fileinfo->isDot()) {
                $Documents[] = [
                    'Path' => $fileinfo->getpathName(),
                    'Name' => $fileinfo->getFilename(),
                ];
            }
        }
        return $this->render('index', ['Documents' => $Documents]);
    }

    public function actionRead($No)
    {
        $Path = Yii::getAlias(Yii::$app->params['SystemConfigs']['DownloadsFolder'] . '\\'.urldecode($No));
        // exit($Path);
        if (is_file($Path)) {
            return Yii::$app->response->sendFile($Path, $No, ['', 'inline' => true]);
        }
    }



    public function ApplicantDetails($Key)
    {
        $model = new MemberApplicationCard();
        $service = Yii::$app->params['ServiceName']['MemberApplicationCard'];
        $memberApplication = Yii::$app->navhelper->readByKey($service, urldecode($Key));
        return $model = $this->loadtomodel($memberApplication, $model);
    }

    public function ApplicantDetailWithDocNum($Docnum)
    {
        $model = new MemberApplicationCard();
        $service = Yii::$app->params['ServiceName']['MemberApplicationCard'];
        $filter = [
            'Application_No' => $Docnum
        ];
        $memberApplication = Yii::$app->navhelper->getData($service, $filter);
        return $model = $this->loadtomodel($memberApplication[0], $model);
    }


    public function getBanks()
    {
        $service = Yii::$app->params['ServiceName']['KenyaBanks'];
        $filter = [];

        $res = [];
        $Banks = \Yii::$app->navhelper->getData($service, $filter);
        if (is_object($Banks)) {
            return $res[] = [
                'Code' => '',
                'Name' => ''
            ];
        }
        foreach ($Banks as $Bank) {
            if (!empty($Bank->Bank_Code))
                $res[] = [
                    'Code' => $Bank->Bank_Code,
                    'Name' => $Bank->Bank_Name
                ];
        }

        return $res;
    }

    public function actionGetBankBranches()
    {
        $service = Yii::$app->params['ServiceName']['BankBranches'];

        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $out = [];


        if (isset($_POST['depdrop_parents'])) {
            $parents = $_POST['depdrop_parents'];
            if ($parents != null) {
                $cat_id = $parents[0];
                $filter = [
                    'Bank_Code' => $cat_id
                ];
                $AccountTypes = \Yii::$app->navhelper->getData($service, $filter);
                if (is_object($AccountTypes)) {
                    $out[] = ['id' => '', 'name' => ''];
                } else {
                    foreach ($AccountTypes as $i => $account) {
                        $out[] = ['id' => $account->Branch_Code, 'name' => $account->Branch_Name];
                    }
                }
                return ['output' => $out, 'selected' => ''];
            }
        }
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
