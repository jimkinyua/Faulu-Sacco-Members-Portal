<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ApplicantLogin;
use app\models\ContactForm;
use app\models\Register;
use app\models\VerifyEmailForm;
use app\models\VerifyApplicantPhoneForm;
use app\models\MemberStatistics;
use app\models\MemberStatement;
use yii\helpers\Url;
use app\models\ApplicantUser;
use app\models\NavisionMemberApplication;
use yii\bootstrap4\Html;


class ApplicationController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'user' => \Yii::$app->applicant, // this user object defined in web.php
                'only' => ['logout', 'index', 'register', 'verify-phone'],
                'rules' => [

                    [
                        'actions' => ['logout', 'index',],
                        'allow' => true,
                        'roles' => ['@'],
                    ],

                    [
                        'actions' => ['register', 'verify-phone', 'login'],
                        'allow' => true,
                        'roles' => ['?'],
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

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        $model  = new MemberStatistics();
        $service = Yii::$app->params['ServiceName']['MemberStatistics'];
        // echo '<pre>';
        // print_r(Yii::$app->applicant->identity);
        // exit;
        return $this->redirect(['/profile', 'Key' => Yii::$app->applicant->identity->getApplicantData()->Key]);
    }

    public function actionError()
    {
        $exception = Yii::$app->errorHandler->exception;
        if ($exception !== null) {
            return $this->render('error', ['exception' => $exception]);
        }
    }

    /**
     * Login action.
     *
     * @return Response|string
     */
    public function actionLogin()
    {
        if (!Yii::$app->applicant->isGuest) {
            return $this->goHome();
        }
        $this->layout = 'applicant-login';
        $model = new ApplicantUser();
        if ($model->load(Yii::$app->request->post())) {
            if ($model->getUser()) {
                // echo '<pre>';
                // print_r($model);
                // exit;

                $token = rand(12453, 4795236);
                $model->getUser()->verification_token = $token;
                if ($model->getUser()->save(false)) {
                    $smsResult = $this->SendSMS($token, $model->getUser()->phoneNo);
                    if ($smsResult == true) {
                        Yii::$app->session->set('PhoneNumber', $model->getUser()->phoneNo);
                        Yii::$app->session->set('MembershipType', $model->getUser()->{'Member Category'});
                        // +254710467646
                        $maskedPhone = substr($model->getUser()->phoneNo, 0, 4) . "****" . substr($model->getUser()->phoneNo, 10, 12);
                        Yii::$app->session->setFlash('success', 'We Have Sent a Verification Code to The Phone No ' . $maskedPhone);
                        return $this->redirect('/verify-phone');
                    }
                    Yii::$app->session->setFlash('error', $smsResult);
                    return $this->redirect(Yii::$app->request->referrer);
                }
            }
            Yii::$app->session->setFlash('error', 'The Member Does not Exist');
            return $this->goBack();
        }

        $model->password = '';
        return $this->render('login', [
            'model' => $model,
        ]);
    }

    public function actionApplicantLogin()
    {
        if (!Yii::$app->applicant->isGuest) {
            return $this->goHome();
        }
        $this->layout = 'applicant-login';
        $model = new ApplicantLogin();
        if ($model->load(Yii::$app->request->post())) {
            if ($model->getApplicant()) {
                $token = 9999; // rand(12453, 4795236);
                $model->getApplicant()->verification_token = $token;
                if ($model->getApplicant()->save(false)) {
                    //if($this->SendSMS($token,$model->getApplicant()->phoneNo) === true){
                    Yii::$app->session->set('PhoneNumber', $model->getApplicant()->phoneNo);
                    Yii::$app->session->set('MembershipType', $model->getApplicant()->memebershipType);
                    $maskedPhone = substr($model->getApplicant()->phoneNo, 0, 4) . "****" . substr($model->getApplicant()->phoneNo, 7, 4);
                    Yii::$app->session->setFlash('success', 'We Have Sent a Verification Code to The Phone No ' . $maskedPhone);
                    return $this->redirect(['verify-applicant-phone']);
                    // }
                }
            }
            Yii::$app->session->setFlash('error', 'The Applicant Does not Exist');
            return $this->redirect(Yii::$app->request->referrer);
        }

        return $this->render('applicant-login', [
            'model' => $model,
            'MembershipTypes' => $this->getMembershipTypes()
        ]);
    }

    public function actionStatement($Account, $Key)
    {

        $service = Yii::$app->params['ServiceName']['PortalReports'];

        $data = [
            'accountNo' => urldecode($Account),
            'memberNo' => Yii::$app->user->identity->{'No_'},
        ];
        $path = Yii::$app->navhelper->PortalReports($service, $data, 'IanGenerateAccountStatement');
        //  Yii::$app->recruitment->printrr($path);

        if (is_array($path)) {

            if (file_exists($path['return_value'])) {
                return Yii::$app->response->sendFile($path['return_value'], 'Account Statement', ['', 'inline' => true]);
            }

            Yii::$app->session->setFlash('error', 'We are unable to generate the report right now. Kindly try again after a while');
            return $this->redirect(['index']);
        }

        Yii::$app->session->setFlash('error', $path);
        return $this->redirect(['index']);
    }


    public function actionShareCapitalStatement()
    {

        $service = Yii::$app->params['ServiceName']['PortalReports'];

        $data = [
            'memberno' => Yii::$app->user->identity->{'No_'}
        ];
        $path = Yii::$app->navhelper->PortalReports($service, $data, 'IanGeneratePayslip');
        // Yii::$app->recruitment->printrr($path);
        if (is_array($path)) {
            if (is_file($path['return_value'])) {
                $binary = file_get_contents($path['return_value']);
                $content = chunk_split(base64_encode($binary));
                //delete the file after getting it's contents --> This is some house keeping
                //unlink($path['return_value']);
                return $this->render('read', [
                    'report' => true,
                    'content' => $content,
                ]);
            }
        }

        Yii::$app->session->setFlash('error', $path);
        return $this->redirect(['index']);
    }


    public function actionGuaranteedLoansStatements()
    {

        $model = new MemberStatement();
        $service = Yii::$app->params['ServiceName']['PortalReports'];

        // if(Yii::$app->request->post() && $model->load(Yii::$app->request->post())){  

        $data = [
            // 'startDate' => date('Y-m-d', strtotime(date(''))),
            // 'endDate'=> date('Y-m-d', strtotime($model->endDate)),
            'memberNo' => Yii::$app->user->identity->{'No_'}
        ];

        $path = Yii::$app->navhelper->PortalReports($service, $data, 'IanGenerateLoanGuaranteedReport');
        // Yii::$app->recruitment->printrr($path);

        if (is_array($path)) {
            if (file_exists($path['return_value'])) {
                return Yii::$app->response->sendFile($path['return_value'], 'Guaranteed Loan Statement', ['', 'inline' => true]);
            }
        }

        Yii::$app->session->setFlash('error', $path);
        return $this->redirect(Yii::$app->request->referrer);

        // }
        return $this->render('member-statement', ['model' => $model, 'Title' => 'Guaranteed Loan Report']);
    }

    public function actionDividendSlip()
    {

        $service = Yii::$app->params['ServiceName']['PortalReports'];

        $data = [
            'memberno' => Yii::$app->user->identity->{'No_'}
        ];
        $path = Yii::$app->navhelper->PortalReports($service, $data, 'IanGeneratePayslip');
        // Yii::$app->recruitment->printrr($path);
        if (is_array($path)) {
            if (is_file($path['return_value'])) {
                $binary = file_get_contents($path['return_value']);
                $content = chunk_split(base64_encode($binary));
                //delete the file after getting it's contents --> This is some house keeping
                //unlink($path['return_value']);
                return $this->render('read', [
                    'report' => true,
                    'content' => $content,
                ]);
            }
        }

        Yii::$app->session->setFlash('error', $path);
        return $this->redirect(['index']);
    }

    public function actionMemberStatement()
    {
        $model = new MemberStatement();
        $service = Yii::$app->params['ServiceName']['PortalReports'];

        if (Yii::$app->request->post() && $model->load(Yii::$app->request->post())) {

            $data = [
                'startDate' => date('Y-m-d', strtotime($model->startDate)),
                'endDate' => date('Y-m-d', strtotime($model->endDate)),
                'memberNo' => Yii::$app->user->identity->{'No_'}
            ];

            $path = Yii::$app->navhelper->PortalReports($service, $data, 'IanGenerateMemberStatement');
            // Yii::$app->recruitment->printrr($path);

            if (is_array($path)) {
                if (file_exists($path['return_value'])) {
                    return Yii::$app->response->sendFile($path['return_value'], 'Account Statement', ['', 'inline' => true]);
                }
            }

            Yii::$app->session->setFlash('error', $path);
            return $this->redirect(Yii::$app->request->referrer);
        }
        return $this->render('member-statement', ['model' => $model, 'Title' => 'Guaranteed Loan Report']);
    }


    public function actionVerifyPhone()
    {
        $this->layout = 'register';
        $model = new VerifyApplicantPhoneForm();
        if (Yii::$app->request->post()) {
            $model = new VerifyApplicantPhoneForm();
            $user = $model->verifyLoginToken(Yii::$app->request->post()['VerifyApplicantPhoneForm']['token']);

            if (isset($user[0]['description'])) {
                Yii::$app->session->setFlash('error', $user[0]['description']);
                return $this->redirect(Yii::$app->request->referrer);
            }

            if (Yii::$app->applicant->login($user)) {
                $re = $model->actionAddUserToDynamics($user);

                if (is_array($re)) {
                    if (isset($res[0]['error'])) {
                        Yii::$app->session->setFlash('error', $res[0]['error']);
                        Yii::$app->applicant->logout();
                        return $this->goHome();
                    }
                    Yii::$app->session->setFlash('error', 'UnKnown Error Occured');
                    Yii::$app->applicant->logout();
                    return $this->goHome();
                }
                // Yii::$app->session->setFlash('success', 'Welcome Back');
                return $this->redirect('index');
            }
        }
        return $this->render('verify-phone', ['model' => $model]);
    }

    public function actionVerifyApplicantPhone()
    {
        $this->layout = 'register';
        $model = new VerifyApplicantPhoneForm();
        if (Yii::$app->request->post()) {
            $model = new VerifyApplicantPhoneForm();
            $user = $model->verifyApplicantLoginToken(Yii::$app->request->post()['VerifyApplicantPhoneForm']['token']);

            if (isset($user[0]['description'])) {
                Yii::$app->session->setFlash('error', $user[0]['description']);
                return $this->redirect(Yii::$app->request->referrer);
            }

            if (Yii::$app->applicant->login($user)) {
                Yii::$app->session->setFlash('success', 'Welcome Back');
                return $this->redirect('index');
            }
        }
        return $this->render('verify-phone', ['model' => $model]);  //findApplicantByLoginToken      
    }




    /**
     * Logout action.
     *
     * @return Response
     */
    public function actionLogout()
    {
        Yii::$app->applicant->logout();

        return $this->goHome();
    }


    public function actionRegister()
    {
        $this->layout = 'register';

        $model = new Register();


        if (Yii::$app->request->isAjax) {
            if ($model->load(Yii::$app->request->post()) && $model->validate()) {

                //Check if an Application exists 

                $existsOnNavision =  $model->checkIfApplicationExistsOnNavision();

                if ($existsOnNavision) {
                    // if ($existsOnNavision->Processed == 1) {
                        // return $this->asJson(['error' => 'Your Membership application has been approved. You are No longer an Applicant']);
                    // }
                    if (Yii::$app->applicant->login($existsOnNavision)) {
                        return $this->redirect('index');
                    }
                }

              
                            $model->DateofBirth = isset($model->DateofBirth )
                                ? date('Y-m-d', strtotime($model->DateofBirth ))
                                : date('Y-m-d', strtotime('18 years ago'));
                            $user = $model->signup();
                            $addToNavResult = $model->actionAddUserToDynamics($user);
                            if($addToNavResult === true){
                                $userLoginData = $model->checkIfApplicationExistsOnNavision();
                                if ($userLoginData) {
                                    if (Yii::$app->applicant->login($userLoginData)) {
                                        // if (is_array($res)) {
                                        //     if (isset($res[0]['error'])) {
                                        //         Yii::$app->applicant->logout();
                                        //         return $this->asJson(['error' => $res[0]['error']]);
                                        //     }
                                        //     Yii::$app->applicant->logout();
                                        //     return $this->asJson(['error' => 'UnKnown Error Occured']);
                                        // }
                                        // Yii::$app->session->setFlash('success', 'Welcome Back');
                                        return $this->redirect('index');
                                    }
                                    return $this->asJson(['error' => 'Unable To Save Details. Kindly Try Registering Again']);
                                }
                                return $this->asJson(['error' => 'Unable to Find User']);
                            }else{
                                return $this->asJson(['error' => $addToNavResult]);
                            }
                           
                                           

                //             
                if (is_string($iprsresult)) {
                    return $this->asJson(['error' => $iprsresult]);
                }

                return $this->asJson(['error' => 'We are unable to process your request. Kindly try again after a while']);
            }

            $result = [];
            // The code below comes from ActiveForm::validate(). We do not need to validate the model
            // again, as it was already validated by save(). Just collect the messages.
            foreach ($model->getErrors() as $attribute => $errors) {
                $result[Html::getInputId($model, $attribute)] = $errors;
            }

            return $this->asJson(['validation' => $result]);
        }


        return $this->render('register', [
            'model' => $model,
            'MembershipTypes' => [], // $this->getMembershipTypes()

        ]);
    }

    public function FirstNamesMatch($IprsName, $TypedName)
    {
        if ($IprsName ===  $TypedName) {
            return true;
        }
        return false;
    }
    public function getDataFromIPRS($userData)
    {
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://test-api.ekenya.co.ke/Ushuru_APP_API/iprs',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($userData),
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json'
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        return $response;
    }

    public function validateIPRSData($model)
    {

        $service = Yii::$app->params['ServiceName']['PortalIntegrations'];

        $data = [
            'iDNo' => $model->NationalID,
            'firstName' => $model->firstName,
            'responseCode' => '',
            'responseMessage' => ''
        ];
        $response = Yii::$app->navhelper->PortalReports($service, $data, 'ValidateIPRSData');

        if (is_array($response)) {
            if (isset($response['responseCode']) && $response['responseCode'] == '00') { //image iko
                $image =  json_decode($response['responseMessage']);
                return $image->Image;
            } else {
                return false;
            }
        }
        if (is_string($response)) {
            return $response;
        }
    }





    public function SendSMS($token, $PhoneNo)
    {
        //Todo: Clean The Phone Number to Form 07... 0r 2547....

        $url = Yii::$app->params['SMS']['BaseURL'];
        $ch = curl_init($url);
        $BearerToken = Yii::$app->params['SMS']['AcessToken'];

        $data = [
            'sender' => 'MHASIBU',
            'message' => 'Your Verification Code is ' . $token,
            'phone' => $PhoneNo,
        ];
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: Bearer  ' . $BearerToken));
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        $response = curl_exec($ch);
        $result = json_decode($response);
        // echo '<pre>';
        // print_r($result);
        // exit;
        curl_close($ch); // Close the connection
        if (!$result->status) { //Error
            return [
                'message' => $result->message
            ];
        }
        return true;
    }


    public function getMembershipTypes()
    {
        $service = Yii::$app->params['ServiceName']['MemberCategories'];
        $filter = [
            // 'isChildAccount'=>0
        ];
        $res = [];
        $MemberCategories = \Yii::$app->navhelper->getData($service, $filter);
        foreach ($MemberCategories as $MemberCategory) {
            if (!empty($MemberCategory->Code))
                $res[] = [
                    'Code' => $MemberCategory->Code,
                    'Name' => @$MemberCategory->Description
                ];
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
