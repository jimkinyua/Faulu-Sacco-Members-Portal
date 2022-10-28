<?php

namespace app\controllers;

use app\models\DeclarationModel;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\filters\VerbFilter;
use app\models\MemberApplicationCard;
use app\models\GeneralInformationModel;
use yii\helpers\Html;



class ProfileController extends Controller
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

    public function ApplicantDetails($Key)
    {
        $model = new MemberApplicationCard();
        $service = Yii::$app->params['ServiceName']['MemberApplicationSingle'];
        $memberApplication = Yii::$app->navhelper->readByKey($service, urldecode($Key));
        return $model = $this->loadtomodel($memberApplication, $model);
    }

    public function actionIndex($Key)
    {
        $this->layout = 'applicant-main';
        $model = new MemberApplicationCard();
        $service = Yii::$app->params['ServiceName']['MemberApplicationSingle'];
        $memberApplication = Yii::$app->navhelper->readByKey($service, urldecode($Key));

        if (Yii::$app->request->isAjax) {
            if ($this->loadpost(Yii::$app->request->post()['MemberApplicationCard'], $model) && $model->validate()) {

                $filter = [
                    'No' => $memberApplication->No,
                ];
                // echo '<pre>';
                // print_r($model);
                // exit;
                $refresh = Yii::$app->navhelper->getData($service, $filter);
                $model->Key = $refresh[0]->Key;
                $model->Date_of_Birth = date('Y-m-d', strtotime($model->Date_of_Birth));
                // $model->First_Name = null;
                // $model->Second_Name = null;
                // $model->Last_Name = null;

                // $model->Date_of_Incoporation = date('Y-m-d', strtotime(@$model->Date_of_Incoporation));
                $result = Yii::$app->navhelper->updateData($service, $model);
                if (is_object($result)) {
                    // Yii::$app->session->setFlash('success','Member Data Added Successfully',true);
                    return $this->redirect(['/communication', 'Key' => $Key]);
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

        return $this->render('index', [
            'model' => $model,
            'Applicant' => $this->ApplicantDetails($Key),
            'MembershipTypes' => [], //$this->getMembershipTypes(),
            'groupTypes' => [], //$this->getGroupTypes(),
            'constituencies' => [], //$this->getConstituencies(),

        ]);
    }

    public function actionActivateMembership($Key)
    {
        $model = new MemberApplicationCard();
        $service = Yii::$app->params['ServiceName']['MemberApplicationSingle'];
        $memberApplication = Yii::$app->navhelper->readByKey($service, urldecode($Key));
        if (Yii::$app->request->post() && $this->loadpost(Yii::$app->request->post()['MemberApplicationSingle'], $model)) {
            $PhoneNo =  ltrim($model->STKpushNo, '+254');
            //Do STK Manenos
            $StkResult =  Yii::$app->MpesaIntergration->createPushSTKNotificationForMemberActivation($PhoneNo, $memberApplication);
            if (isset($StkResult->errorCode)) { //Error Occured
                Yii::$app->session->setFlash('error', 'We are unable to send a notificationto your mobile. Kindly Try again after sometime');
                return $this->redirect(Yii::$app->request->referrer);
            }
            Yii::$app->session->setFlash('success', 'We have sent a notification to the Mobile Phone');
            return $this->redirect(Yii::$app->request->referrer);
        }


        $model = $this->loadtomodel($memberApplication, $model);

        return $this->render('membership-activation', [
            'model' => $model,
            'Applicant' => $this->ApplicantDetails($Key),
        ]);
    }




    public function getMembershipTypes()
    {
        $service = Yii::$app->params['ServiceName']['MemberCategories'];
        $res = [];
        $MemberCategories = \Yii::$app->navhelper->getData($service);
        foreach ($MemberCategories as $MemberCategory) {
            if (!empty($MemberCategory->Code))
                $res[] = [
                    'Code' => $MemberCategory->Code,
                    'Name' => $MemberCategory->Description
                ];
        }

        return $res;
    }



    public function getGroupTypes()
    {
        $service = Yii::$app->params['ServiceName']['GroupCategories'];
        $res = [];
        $GroupCategories = \Yii::$app->navhelper->getData($service);
        foreach ($GroupCategories as $GroupCategry) {
            if (!empty($GroupCategry->Group_Type))
                $res[] = [
                    'Code' => $GroupCategry->Group_Type,
                    'Name' => $GroupCategry->Group_Description
                ];
        }

        return $res;
    }



    public function getConstituencies()
    {
        $service = Yii::$app->params['ServiceName']['ConstituenciesList'];
        $res = [];
        $Constituencies = \Yii::$app->navhelper->getData($service);
        foreach ($Constituencies as $Constituency) {
            if (!empty($Constituency->No))
                $res[] = [
                    'Code' => $Constituency->No,
                    'Name' => $Constituency->Description
                ];
        }

        return $res;
    }

    public function actionGetSubConstituency()
    {
        $service = Yii::$app->params['ServiceName']['SubConstituenciesLines'];
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $out = [];
        if (isset($_POST['depdrop_parents'])) {
            $parents = $_POST['depdrop_parents'];
            if ($parents != null) {
                $cat_id = $parents[0];
                $filter = [
                    'Constituency_Code' => $cat_id
                ];
                $SubConstituencies = \Yii::$app->navhelper->getData($service, $filter);
                if (is_object($SubConstituencies)) {
                    $out[] = ['id' => '', 'name' => ''];
                } else {
                    foreach ($SubConstituencies as $i => $account) {
                        $out[] = ['id' => $account->No, 'name' => $account->Description];
                    }
                }
                return ['output' => $out, 'selected' => ''];
            }
        }
    }


    public function actionDeclaration($Key)
    {
        $this->layout = 'applicant-main';
        $model = new DeclarationModel();

        $Updateservice = Yii::$app->params['ServiceName']['MemberApplicationSingle'];
        $memberApplication = Yii::$app->navhelper->readByKey($Updateservice, urldecode($Key));

        $model = $this->loadtomodel($memberApplication, $model);

        if (Yii::$app->request->isAjax) {
            if ($this->loadpost(Yii::$app->request->post()['DeclarationModel'], $model) && $model->validate()) {
                $model->Key = $memberApplication->Key;
                $model->PortalStus = 'Submitted';
                $result = Yii::$app->navhelper->updateData($Updateservice, $model);

                if (is_object($result)) {
                    Yii::$app->session->setFlash('success', 'Your Application has been successfully submitted. Thank you for choosing us', true);
                    Yii::$app->applicant->logout();
                    return $this->goHome();
                    return $this->redirect(['index', 'Key' => $memberApplication->Key]);
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

        return $this->render('declaration', [
            'model' => $model,
            'applicationForm' =>'',// $this->getMemberApplicationinPDF($model->No),
        ]);
    }



    public function getMemberApplicationinPDF($ApplicationNo)
    {

        $service = Yii::$app->params['ServiceName']['PortalIntegrations'];

        $data = [
            'applicationNo' => urldecode($ApplicationNo),
        ];
        $path = Yii::$app->navhelper->PortalReports($service, $data, 'GenerateMemberApplication');
        //  Yii::$app->recruitment->printrr($path);

        if (is_array($path)) {

            $imagePath = Yii::getAlias($path['return_value']);

            if (is_file($imagePath)) {
                $binary = file_get_contents($imagePath);
                $content = chunk_split(base64_encode($binary));
                return $content;
            }
        }

        Yii::$app->session->setFlash('error', $path);
        return $this->redirect(['index']);
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
