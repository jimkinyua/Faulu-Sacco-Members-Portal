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
use app\models\CreatePasswordModel;
use app\models\LoginAttempt;
use app\models\LoginWithPasswordForm;
use app\models\Register;
use app\models\VerifyEmailForm;
use app\models\VerifyPhoneForm;
use app\models\MemberStatistics;
use app\models\MemberStatement;
use yii\helpers\Url;
use app\models\MemberTypeAndPhoneNo;
use app\models\PasswordForm;
use app\models\PasswordResetRequestForm;
use app\models\user;
use app\models\ValidateTransaction;
use app\models\MemberStatisticsFactbox;
use app\models\OAuthAuthorise;
use app\models\OtherStatistics;
use app\models\ResetPasswordForm;
use app\models\SuccessLogin;

class SiteController extends Controller
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
                    'logout', 'index', 'register',
                    'verify-phone', 'applicant-Login',
                    'statement', 'share-capital-statement',
                    'guaranteed-loans-statements', 'dividend-slip',
                    'member-statement', 'verify-phone', 'verify-applicant-phone',
                    'logout', 'register', 'loan-arrears',
                ],
                'rules' => [

                    [
                        'actions' => [
                            'index', 'logout', 'statement',
                            'share-capital-statement', 'guaranteed-loans-statements',
                            'dividend-slip', 'member-statement', 'loan-arrears'
                        ],
                        'allow' => true,
                        'roles' => ['@'],
                    ],

                    [
                        'actions' => [
                            'register',  'applicant-Login', 'verify-phone',
                            'verify-applicant-phone', 'oauth-authorize'
                        ],
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

    public function beforeAction($action)
    {

        // your custom code here, if you want the code to run before action filters,
        // which are triggered on the [[EVENT_BEFORE_ACTION]] event, e.g. PageCache or AccessControl
        // if (!Yii::$app->user->isGuest && Yii::$app->recruitment->UserhasImageAndSignature() === false) {
        //     if ($action->id == 'logout' || $action->id == 'index') {
        //         return true;
        //     }
        //     Yii::$app->session->setFlash('error', 'Kindly Provide us with Your Passport and Signature. Seems they are missing from our records.');
        //     $this->redirect(['member-change-request/index']);
        //     return false;
        // }

        if (!parent::beforeAction($action)) {
            return false;
        }

        if (parent::beforeAction($action)) {
            //change layout for error action after 
            //checking for the error action name 
            //so that the layout is set for errors only
            if ($action->id == 'verify-phone') {
                $this->layout = 'error';
            }
            return true;
        }
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

        $data = [
            'IDnumber' => Yii::$app->user->identity->{'ID No_'},
            'MemberName' => Yii::$app->user->identity->{'Name'},
        ];

        // Yii::$app->recruitment->LogActionHAsBeenAccessed($data, Yii::$app->user->identity->{'Full Name'} . ' Accessed the Dashboard');
        $model  = new MemberStatisticsFactbox();
        $OtherStatisticsModel = new OtherStatistics();
        $memberAccounts = Yii::$app->user->identity->getAccounts();
        $OtherStatistics = Yii::$app->user->identity->getCustomerCardDetails();
        $MyAccounts = Yii::$app->user->identity->getMyAccounts();
        
    

        if (is_object($OtherStatistics)) {
            $OtherStatisticsModel = $this->loadtomodel($OtherStatistics, $OtherStatisticsModel);
        }

        // if (is_string($memberStatistics)) {
        //     Yii::$app->session->setFlash('error', $memberStatistics);
        //     return $this->render('index', ['model' => $model, 'accounts' => [], 'image' => $this->getMemberImage()]);
        // }

        // if ($memberStatistics['responseCode'] != 00) { //Error Manenos
        //     return $this->render('index', ['model' => $model, 'accounts' => [], 'image' => $this->getMemberImage()]);
        // } elseif ($memberStatistics['responseCode'] == 00) {

            // echo '<pre>';
            // print_r(json_decode($memberStatistics['responseMessage']));
            // exit;
            // $data = json_decode($memberStatistics['responseMessage']);
            // $model->Total_Shares = @$data->Accounts[2]->Balance;
            // $model->Total_Deposits = @$data->Accounts[1]->Balance;
            // $model->Total_Deposits = @$data->Accounts[1]->Balance;
            // $model->Collections = @$data->Accounts[0]->Balance;

            // $model->Full_Name = $data->FullName;
            // $model->Email = $data->Email;
            // $model->Member_No = $data->MemberNo;
            // $model->SASAAccount = $data->SASAAccount;
            // $model->InvestmentAccount =  $data->InvestmentAccount;
            // if (isset($data->Accounts)) {
            //     // $model = $this->loadtomodel($memberStatistics[0],$model);
            //     return $this->render('index', ['model' => $model, 'accounts' => $data->Accounts, 'image' => $this->getMemberImage(), 'OtherStatisticsModel' => $OtherStatisticsModel]);
            // }
        // } else {
            return $this->render('index', ['model' => $model, 'MyAccounts'=>$MyAccounts, 'accounts' => $memberAccounts, 'image' => '', 'OtherStatisticsModel' => $OtherStatisticsModel]);
        // }
    }

    public function getMemberImage()
    {
        $service = Yii::$app->params['ServiceName']['PortalReports'];

        $data = [
            'memberNo' => Yii::$app->user->identity->{'Member No_'},
            'responseCode' => '',
            'responseMessage' => ''
        ];
        $response = Yii::$app->navhelper->PortalReports($service, $data, 'GetMemberImage');
        if (is_array($response)) {

            if (isset($response['responseCode']) && $response['responseCode'] == '00') { //image iko
                $image =  json_decode($response['responseMessage']);
                return $image->Image;
            } else {
                return false;
            }
        }
        return false;
    }


    public function getMemberSignature()
    {
        $service = Yii::$app->params['ServiceName']['PortalReports'];

        $data = [
            'memberNo' => Yii::$app->user->identity->{'Member No_'},
            'responseCode' => '',
            'responseMessage' => ''
        ];
        $response = Yii::$app->navhelper->PortalReports($service, $data, 'GetMemberSignature');
        if (is_array($response)) {

            if (isset($response['responseCode']) && $response['responseCode'] == '00') { //image iko
                $image =  json_decode($response['responseMessage']);
                return $image->Signature;
            } else {
                return false;
            }
        }
        return false;
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
        $Log = new LoginAttempt();
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }
        $this->layout = 'landing-page';
        $model = new LoginForm();

        if (Yii::$app->request->isPost && $model->load(Yii::$app->request->post())) {
            $User = $model->getUser();
            // $Log->LogSignInAttempt($model);
            if ($User) {
                Yii::$app->session->set('OauthData', $User);
                return $this->redirect(['oauth-authorize']);
            }
        }

        $model->password = '';
        return $this->render('login', [
            'model' => $model,
        ]);
    }

    public function LogLoginAttempt()
    {
    }

    public function actionLoginWithPassword()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }
        $this->layout = 'landing-page';
        $model = new LoginWithPasswordForm();
        $Log = new LoginAttempt();
        $SuccessLogin = new SuccessLogin();


        if (Yii::$app->request->isPost && $model->load(Yii::$app->request->post())) {
            $OauthData = Yii::$app->session->get('OauthData');
            if ($OauthData) {
                $model->IDnumber = $OauthData->{'ID No_'};
                if ($model->isFirstLogin() === true) {
                    Yii::$app->session->setFlash('success', 'Seems this is your first time to login using a Password. Kindly Create a Password');
                    return $this->redirect(['create-password']);
                }

                $_user = $model->getUser();
                if ($model->validate() && $model->login()) {
                    // $Log->LogSignInAttempt($model, 'Attempted to Login with password and succesfully logged in');
                    // $SuccessLogin->Log($model, 'Using Password');
                    Yii::$app->session->setFlash('success', 'Welcome back');
                    return $this->goHome();
                }
                //Failed Login Here
                // $Log->LogSignInAttempt($model, 'Attempted to Login with password and failed bacause password is invalid');
                Yii::$app->session->setFlash('error', 'Incorrect Password');
                return $this->redirect(Yii::$app->request->referrer);
            }
        }

        $model->password = '';
        return $this->render('password-input', [
            'model' => $model,
        ]);
    }

    public function actionOauthAuthorize()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }
        $this->layout = 'landing-page';
        $model = new OAuthAuthorise();
        $OauthData = Yii::$app->session->get('OauthData');

        if (strlen(substr($OauthData->{'Phone No_'}, 0, 3)) >= 3) { //Does not have +254
            $maskedPhone = "**** *** " . substr($OauthData->{'Phone No_'}, -3);
        } else {
            $maskedPhone = "**** *** " . substr($OauthData->{'Phone No_'}, -3);
        }

        $model->MaskedPhone = $maskedPhone;

        $model->IDnumber = $OauthData->{'ID No_'};

        if (Yii::$app->request->isPost) {
            return $this->SendOTP($model);
        }

        return $this->render('confirm-OTP-Send', [
            'model' => $model,
        ]);
    }

    public function LogOTPisSentSent($model)
    {
        $Log = new LoginAttempt();
        $Log->MemberID = $model->IDnumber;
        $Log->IP = Yii::$app->getRequest()->getUserIP();
        $Log->attempt_time = time();
        $Log->action_performed = 'Member Found and OTP Sent';
        // $this->created_at = time();
        // echo '<pre>';
        // print_r($this);
        // exit;
        if ($Log->save()) {
            return true;
        }
    }

    public function getOTP()
    {
        $Key =  Yii::$app->MpesaIntergration->generateOTPKey(5);
        return Yii::$app->MpesaIntergration->GenerateOTP($Key);
    }

    public function SendOTP($model)
    {

        $token = $this->getOTP();
        $user  = User::findByUsername($model->IDnumber);
        if ($user) {
            $user->verification_token =9999; //$token;
            // $user->token_created_at = time();
            // $user->token_expires_at =  time() + (2 * 60); // 2 Mins. 
            if ($user->update(false)) {
                // exit('Saved');
                $Message = 'Your One Time Password is ' . $token;
                $smsResult =true; // Yii::$app->MpesaIntergration->SendSMS($Message, $user->{'Phone No_'});
                // $this->LogOTPisSentSent($model, $Message);
                if ($smsResult == true) {
                    Yii::$app->session->set('PhoneNumber', $user->{'Phone No_'});
                    $PhoneNo =  ltrim($user->{'Phone No_'}, '+254');

                    if (strlen(substr($PhoneNo, 0, 3)) >= 3) { //Does not have +254
                        $maskedPhone = "**** ***" . substr($PhoneNo, -3);
                    } else {
                        $maskedPhone = "**** ***" . substr($PhoneNo, -3);
                    }

                    Yii::$app->session->setFlash('success', 'We have sent a one time password to the phone no ' . $maskedPhone);
                    return $this->redirect(['verify-phone']);
                }
                Yii::$app->session->setFlash('error', $smsResult);
                return $this->redirect(['verify-phone']);
            }
        }
    }

    public function actionValidatePassword()
    {


        if (!Yii::$app->user->isGuest) {
            return $this->redirect(['/member-profile', 'Key' => Yii::$app->user->identity->getMemberData()->Key]);
        }

        $this->layout = 'landing-page';
        $model = new PasswordForm();

        if (Yii::$app->request->post() && $model->load(Yii::$app->request->post())) {
            $memmberData = Yii::$app->session->get('MemberData');
            // echo '<pre>';
            // print_r($memmberData->Member No_);
            // exit;
            if (isset($memmberData->{'Member No_'})) {


                if ($model->checkPassword($memmberData->{'Member No_'}) === true) { //Do OTP Here

                    // if($memmberData->{'Has International No'} == 1 && isset($memmberData->{'E-Mail'})){ //Send OTP Via Mail
                    //    if( $this->sendLoginOTPViaEmail($memmberData)){
                    //     Yii::$app->session->setFlash('success', 'We have sent a one time password to your email');
                    //     return $this->redirect(['verify-phone']);

                    //    }
                    // }
                    // $memmberData= Yii::$app->session->remove('MemberData');
                    return $this->SendOTP($memmberData);
                }
            }

            Yii::$app->session->setFlash('error', 'Incorrect Password');
            return $this->goHome();
        }
        return $this->render('password-area', [
            'model' => $model,
        ]);
    }

    protected function sendOTPViaEmail($user)
    {
        $token = rand(1000, 9999);
        $user->{'Transaction OTP'} = $token;

        if ($user->save(false)) {
            return Yii::$app
                ->mailer
                ->compose(
                    ['html' => 'emailCreatePassword-html', 'text' => 'emailCreatePassword-text'],
                    ['user' => $user]
                )
                ->setFrom([Yii::$app->params['supportEmail'] => 'Mhasibu Sacco OTP' . ''])
                ->setTo($user->{'E-Mail'})
                ->setSubject('Verification Code ')
                ->send();
        }
    }


    protected function sendLoginOTPViaEmail($user)
    {
        $token = rand(1000, 9999);
        $user->token_created_at = time();
        $user->token_expires_at =  time() + (5.5 * 60);
        $user->verification_token = $token;


        if ($user->save(false)) {
            return Yii::$app
                ->mailer
                ->compose(
                    ['html' => 'emailLogin-html', 'text' => 'emailLogin-text'],
                    ['user' => $user]
                )
                ->setFrom([Yii::$app->params['supportEmail'] => 'Mhasibu Sacco OTP' . ''])
                ->setTo($user->{'E-Mail'})
                ->setSubject('Verification Code ')
                ->send();
        }
    }

    protected function sendPasswordResetOTPViaEmail($user)
    {
        $token = 9999; // rand(1000, 9999);
        $user->{'Transaction OTP'} = $token;
        if ($user->save(false)) {
            return true;
            // return Yii::$app
            //     ->mailer
            //     ->compose(
            //         ['html' => 'password-reserVerify-html', 'text' => 'password-resetemailVerify-text'],
            //         ['user' => $user]
            //     )
            //     ->setFrom([Yii::$app->params['supportEmail'] => 'Mhasibu Sacco OTP' . ''])
            //     ->setTo($user->{'E-Mail'})
            //     ->setSubject('Verification Code ')
            //     ->send();
        }
    }

    public function actionCreatePassword()
    {

        $PasswordModel = new CreatePasswordModel();

        if (!Yii::$app->user->isGuest) {
            return $this->redirect(['/member-profile', 'Key' => Yii::$app->user->identity->getMemberData()->Key]);
        }
        $userDetails = Yii::$app->session->get('userDetails');
        $userState = Yii::$app->session->get('firstTimeLogin');

        if (is_object($userDetails) && $userState === true) {
            $this->layout = 'landing-page';
            if (Yii::$app->request->post() && $PasswordModel->load(Yii::$app->request->post())  && $PasswordModel->validate()) {
                $memmberData = Yii::$app->session->get('userDetails');

                // Yii::$app->session->set('PasswordCreationData', $PasswordModel);
                // $user = $PasswordModel->getUser($memmberData->{'Member No_'});
                // $token = 9999; //rand(1000, 9999);
                // $memmberData->{'Transaction OTP'} = $token;
                // $memmberData->SetUpPassword = 1;
                $setPassword = $PasswordModel->SavePassword($memmberData->{'No_'});
                if ($setPassword) {
                    if (Yii::$app->user->login($setPassword)) {
                        Yii::$app->session->set('userDetails', false);
                        Yii::$app->session->set('firstTimeLogin', false);
                        Yii::$app->session->setFlash('success', 'You Have Successfully Created Your Password. Welcome');
                        return $this->goHome();
                    }
                }
            }
            return $this->render('create-password', ['model' => $PasswordModel]);
        }
        Yii::$app->session->set('userDetails', false);
        Yii::$app->session->set('firstTimeLogin', false);
        return $this->redirect(['login']);
    }


    public function actionResetPassword()
    {


        if (!Yii::$app->user->isGuest) {
            return $this->redirect(['/member-profile', 'Key' => Yii::$app->user->identity->getMemberData()->Key]);
        }

        $this->layout = 'landing-page';
        $PasswordModel = new CreatePasswordModel();

        if (Yii::$app->request->post() && $PasswordModel->load(Yii::$app->request->post())) {
            $memmberData = Yii::$app->session->get('userDetails');
            $_user = User::findIdentity($memmberData->{'Member No_'});
            $_user->setPassword($PasswordModel->password);
            if ($_user->save()) {
                if (Yii::$app->user->login($_user)) {
                    Yii::$app->session->setFlash('success', 'You Have Successfully Changed Your Password.');
                    return $this->goHome();
                }
            }
        }
        return $this->render('create-password', ['model' => $PasswordModel]);
    }

    public function actionRequestPasswordReset()
    {
        $this->layout = 'landing-page';

        $model = new PasswordResetRequestForm();
        if ($model->load(Yii::$app->request->post())) {
            $data =  $model->FindMember();
            if ($data) {
                Yii::$app->session->set('userDetails', $model->FindMember());
                return $this->SendChangeSMSOTP($model->FindMember());
            } else {
                Yii::$app->session->setFlash('error', 'We are not able to find a member with the details you provided');
                return $this->goHome();
            }
        }

        return $this->render('requestPasswordResetToken', [
            'model' => $model,
        ]);
    }

    public function actionVerifyPasswordChangeRequest()
    {
        $this->layout = 'landing-page';

        $ValidateTransactionModel = new ValidateTransaction();

        if (Yii::$app->request->post() && $ValidateTransactionModel->load(Yii::$app->request->post())) {
            $model = new ValidateTransaction();
            $PasswordChangeDetails = Yii::$app->session->get('PasswordCreationData');
            $user = $this->verifyTransactionToken($ValidateTransactionModel->Code);

            if (isset($user[0]['description'])) {
                Yii::$app->session->setFlash('error', $user[0]['description']);
                return $this->redirect(Yii::$app->request->referrer);
            }

            return $this->redirect(['reset-password']);
        }
        return $this->render('verify-password-change', ['ValidationModel' => $ValidateTransactionModel]);
    }


    public function actionChangePassword()
    {
        $model = new ResetPasswordForm();

        if (Yii::$app->request->post() && $model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->resetPassword(Yii::$app->user->identity->{'Member No_'})) {
                Yii::$app->session->setFlash('success', 'Your password has been changed succesfully');
                return $this->goHome();
            }
            Yii::$app->session->setFlash('error', 'We are unable to reset your password. Kindly try Again');
            return $this->goHome();
        }

        return $this->render('reset-password', ['model' => $model]);
    }



    public function SendChangeSMSOTP($model)
    {
        $Log = new LoginAttempt();
        $token = $this->getOTP();
        $Message = 'Your Password reset token is ' . $token;
        $smsResult = Yii::$app->MpesaIntergration->SendSMS($Message, $model->{'Phone No_'});
        $model->{'Transaction OTP'} = $token;
        if ($model->save(false)) {
            // $this->SendPasswordChangeSMS($token, $model->{'Phone No_'});
            if ($smsResult == true) {
                $Log->LogSignInAttempt($model, 'Requested for a password reset and a verification SMS sent');
                Yii::$app->session->set('PhoneNumber', $model->{'Phone No_'});
                $PhoneNo =  ltrim($model->{'Member No_'}, '+254');
                if (strlen(substr($PhoneNo, 0, 3)) >= 3) { //Does not have +254
                    $maskedPhone = "**** ***" . substr($PhoneNo, -3);
                } else {
                    $maskedPhone = "**** ***" . substr($PhoneNo, -3);
                }
                Yii::$app->session->setFlash('success', 'We have sent a verification token to the phone no ' . $maskedPhone);
                return $this->redirect(['verify-password-change-request']);
            }
            Yii::$app->session->setFlash('error', $smsResult);
            return $this->redirect(['verify-phone']);
        }
    }


    public function verifyTransactionToken($token)
    {

        $_user = User::verifyTransactionToken($token);

        if (!$_user) {
            return [
                [
                    'error' => 1,
                    'description' => 'The Token You Have Provided is Incorrect.'
                ]
            ];
        }

        return $_user;
    }


    public function actionVerifyPasswordChange()
    {
        $this->layout = 'landing-page';

        $ValidateTransactionModel = new ValidateTransaction();

        if (Yii::$app->request->post() && $ValidateTransactionModel->load(Yii::$app->request->post())) {
            $model = new ValidateTransaction();
            $PasswordChangeDetails = Yii::$app->session->get('PasswordCreationData');
            $user = $this->verifyTransactionToken($ValidateTransactionModel->Code);

            if (isset($user[0]['description'])) {
                Yii::$app->session->setFlash('error', $user[0]['description']);
                return $this->redirect(Yii::$app->request->referrer);
            }

            $user->setPassword($PasswordChangeDetails->password);
            $user->SetUpPassword = 1;
            if ($user->update(false)) {
                if (Yii::$app->user->login($user)) {

                    Yii::$app->session->set('userDetails', false);
                    Yii::$app->session->set('firstTimeLogin', false);

                    Yii::$app->session->setFlash('success', 'You Have Successfully Created Your Password. Welcome');
                    return $this->goHome();
                }
                return $this->goHome();
            }
        }
        return $this->render('verify-password-change', ['ValidationModel' => $ValidateTransactionModel]);
    }




    public function actionApplicantLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }
        $this->layout = 'applicant-login';
        $model = new ApplicantLogin();
        if ($model->load(Yii::$app->request->post())) {
            if ($model->getApplicant()) {
                $token = rand(12453, 4795236);
                $model->getApplicant()->verification_token = $token;
                if ($model->getApplicant()->save(false)) {
                    if ($this->SendSMS($token, $model->getApplicant()->phoneNo) === true) {
                        Yii::$app->session->set('PhoneNumber', $model->getApplicant()->phoneNo);
                        Yii::$app->session->set('MembershipType', $model->getApplicant()->memebershipType);
                        $PhoneNo =  ltrim($model->getApplicant()->phoneNo, '+254');
                        if (strlen(substr($PhoneNo, 0, 3)) >= 3) { //Does not have +254
                            $maskedPhone = "**** ***" . substr($PhoneNo, -3);
                        } else {
                            $maskedPhone = "**** ***" . substr($PhoneNo, -3);
                        }
                        Yii::$app->session->setFlash('success', 'We Have Sent a Verification Code to The Phone No ' . $maskedPhone);
                        return $this->redirect(['verify-applicant-phone']);
                    }
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

    public function actionStatement($Account = '')
    {
        // $data = [
        //     'IDnumber' => Yii::$app->user->identity->{'ID No_'},
        //     'MemberName' => Yii::$app->user->identity->{'Full Name'},
        // ];

        if ($Account == '') {
            $action = 'Full Member';
        } else {
            $action = $Account . ' Account';
        }
        // Yii::$app->recruitment->LogActionHAsBeenAccessed($data, Yii::$app->user->identity->{'Full Name'} . ' Requested For the ' . $action . ' Statement');
        $service = Yii::$app->params['ServiceName']['PortalReports'];

        $data = [
            'memberNo' => Yii::$app->user->identity->{'No_'},
            'startDate'=>'',
            'endDate'=>''
        ];
        $path = Yii::$app->navhelper->PortalReports($service, $data, 'GenerateMemberStatement');
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

    
    public function actionAccountStatement($Account = '')
    {
        // $data = [
        //     'IDnumber' => Yii::$app->user->identity->{'ID No_'},
        //     'MemberName' => Yii::$app->user->identity->{'Full Name'},
        // ];

        if ($Account == '') {
            $action = 'Full Member';
        } else {
            $action = $Account . ' Account';
        }
        // Yii::$app->recruitment->LogActionHAsBeenAccessed($data, Yii::$app->user->identity->{'Full Name'} . ' Requested For the ' . $action . ' Statement');
        $service = Yii::$app->params['ServiceName']['PortalReports'];

        $data = [
            'accountNumber' => $Account,
        ];
        $path = Yii::$app->navhelper->PortalReports($service, $data, 'GetAccountStatement');
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


    public function actionGuarantorStatement()
    {
        // $data = [
        //     'IDnumber' => Yii::$app->user->identity->{'ID No_'},
        //     'MemberName' => Yii::$app->user->identity->{'Full Name'},
        // ];

        // Yii::$app->recruitment->LogActionHAsBeenAccessed($data, Yii::$app->user->identity->{'Full Name'} . ' Requested for the Guarantor Statement');

        $service = Yii::$app->params['ServiceName']['PortalReports'];

        $data = [
            'memberNo' => Yii::$app->user->identity->{'No_'},
        ];
        $path = Yii::$app->navhelper->PortalReports($service, $data, 'GetGuaranterReport');
        //  Yii::$app->recruitment->printrr($path);

        if (is_array($path)) {

            if (file_exists($path['return_value'])) {
                return Yii::$app->response->sendFile($path['return_value'], 'Guarantor Statement', ['', 'inline' => true]);
            }

            Yii::$app->session->setFlash('error', $path);
            return $this->redirect(['index']);
        }

        Yii::$app->session->setFlash('error', $path);
        return $this->redirect(['index']);
    }


    public function actionShareCapitalStatement()
    {

        $service = Yii::$app->params['ServiceName']['PortalReports'];

        $data = [
            'memberno' => Yii::$app->user->identity->{'Member No_'}
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
        // $data = [
        //     'IDnumber' => Yii::$app->user->identity->{'ID No_'},
        //     'MemberName' => Yii::$app->user->identity->{'Full Name'},
        // ];

        // Yii::$app->recruitment->LogActionHAsBeenAccessed($data, Yii::$app->user->identity->{'Full Name'} . ' Requested to the Guaranteed Loan Statement');

        $model = new MemberStatement();
        $service = Yii::$app->params['ServiceName']['PortalReports'];

        // if(Yii::$app->request->post() && $model->load(Yii::$app->request->post())){  

        $data = [
            // 'startDate' => date('Y-m-d', strtotime(date(''))),
            // 'endDate'=> date('Y-m-d', strtotime($model->endDate)),
            'memberNo' => Yii::$app->user->identity->{'No_'}
        ];

        $path = Yii::$app->navhelper->PortalReports($service, $data, 'GetGuaranteedReport');
        // Yii::$app->recruitment->printrr($path);

        if (is_array($path)) {
            if (file_exists($path['return_value'])) {
                return Yii::$app->response->sendFile($path['return_value'], 'Guaranteed Loan Statement', ['', 'inline' => true]);
            }
        }

        Yii::$app->session->setFlash('error', $path);
        return $this->redirect(Yii::$app->request->referrer);

        // }
        return $this->render('member-statement', ['model' => $model, 'Title' => 'Guaranteed Loan Statement']);
    }

    public function actionDividendSlip()
    {

        $service = Yii::$app->params['ServiceName']['PortalReports'];

        $data = [
            'memberNo' => Yii::$app->user->identity->{'Member No_'},
            'dividendCode' => 'DIV0001',
        ];
        $path = Yii::$app->navhelper->PortalReports($service, $data, 'IanGenerateDividendslip');
        // Yii::$app->recruitment->printrr($path);
        if (is_array($path)) {
            if (is_file($path['return_value'])) {
                return Yii::$app->response->sendFile($path['return_value'], 'Dividend Slip', ['', 'inline' => true]);
            }
        }

        Yii::$app->session->setFlash('error', 'We are Unle to Generate the Slip.');
        return $this->redirect(['index']);
    }

    public function actionMemberStatement()
    {
        $model = new MemberStatement();
        $service = Yii::$app->params['ServiceName']['PortalReports'];


        $data = [
            'memberNo' => Yii::$app->user->identity->{'Member No_'}
        ];

        $path = Yii::$app->navhelper->PortalReports($service, $data, 'IanGenerateMemberStatement');
        // Yii::$app->recruitment->printrr($path);

        if (is_array($path)) {
            if (file_exists($path['return_value'])) {
                return Yii::$app->response->sendFile($path['return_value'], 'Account Statement', ['', 'inline' => true]);
            }
        }

        Yii::$app->session->setFlash('error', $path);
        return $this->redirect(['index']);

        //return $this->render('member-statement', ['model'=>$model, 'Title'=>'Detailed Member Statement']);        
    }


    public function actionVerifyPhone()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->redirect(['/member-profile', 'Key' => Yii::$app->user->identity->getMemberData()->Key]);
        }

        $this->layout = 'register';
        $model = new VerifyPhoneForm();
        $SuccessLogin = new SuccessLogin();
        $Log = new LoginAttempt();

        if (Yii::$app->request->post()) {
            $model = new VerifyPhoneForm();
            $user = $model->verifyLoginToken(Yii::$app->request->post()['VerifyPhoneForm']['token']);

            if (isset($user[0]['description'])) {
                $OauthData = Yii::$app->session->get('userDetails');

                // $model->IDnumber = $OauthData->{'ID No_'};
                // $Log->LogSignInAttempt($model, 'Attempted to Login with OTP but failed because OTP is invalid');
                Yii::$app->session->setFlash('error', $user[0]['description']);
                return $this->redirect(Yii::$app->request->referrer);
            }

            if ($user && empty($user->password_hash)) {
                Yii::$app->session->set('userDetails', $user);
                Yii::$app->session->set('firstTimeLogin', true);
                Yii::$app->session->setFlash('success', 'Seems this is your first time to login. Kindly Create a Password');
                return $this->redirect(['create-password']);
            }



            if (Yii::$app->user->login($user)) {
                $model->IDnumber = $user->{'ID No_'};
                $SuccessLogin->log($model, 'Using OTP');
                //Clean Up Session Data
                Yii::$app->session->remove('OauthData');
                Yii::$app->session->setFlash('success', 'Welcome Back');
                // return $this->redirect(['/member-profile', 'Key'=>Yii::$app->user->identity->getMemberData()->Key]);
                return $this->goHome();
            }
        }
        return $this->render('verify-phone', ['model' => $model]);
    }





    public function actionVerifyApplicantPhone()
    {
        $this->layout = 'register';
        $model = new VerifyPhoneForm();
        if (Yii::$app->request->post()) {
            $model = new VerifyPhoneForm();
            $user = $model->verifyApplicantLoginToken(Yii::$app->request->post()['VerifyPhoneForm']['token']);

            if (isset($user[0]['description'])) {
                Yii::$app->session->setFlash('error', $user[0]['description']);
                return $this->redirect(Yii::$app->request->referrer);
            }

            if (Yii::$app->user->login($user)) {
                Yii::$app->session->setFlash('success', 'Welcome Back');
                return $this->goHome();
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
        $user = User::findById(Yii::$app->user->identity->{'No_'});
        // $user->loggedIn = 0;
        // $user->logged_out_at = time();
        $user->save(false);
        Yii::$app->user->logout();

        return $this->goHome();
    }


    public function actionRegister()
    {
        $this->layout = 'register';

        $model = new Register();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {

            //    if($model->IfUserExists()){
            //         Yii::$app->session->setFlash('success', 'It Seems an Account Already Exists with the Details You Provided.
            //          Kindly Log on to the System. If You Fotgot Your Password, Click on the Forgost Password button to Reset the Password');
            //          return $this->goHome();
            //     }

            $user = $model->signup();
            if ($user) {
                $token = rand(12453, 4795236);
                $user->verification_token = $token;
                if ($user->save(false)) {
                    if ($this->SendSMS($token, $user->phoneNo) === true) {
                        Yii::$app->session->set('PhoneNumber', $user->phoneNo);
                        Yii::$app->session->set('MembershipType', $user->memebershipType);
                        $PhoneNo =  ltrim($user->phoneNo, '+254');

                        if (strlen(substr($PhoneNo, 0, 3)) >= 3) { //Does not have +254
                            $maskedPhone = "**** ***" . substr($PhoneNo, -3);
                        } else {
                            $maskedPhone = "**** ***" . substr($PhoneNo, -3);
                        }

                        Yii::$app->session->setFlash('success', 'Thank You For Registering. We Have Sent a Verification Code to The Phone No ' . $maskedPhone);
                        return $this->redirect(['verify-phone-no/']);
                    }
                }
            }
            Yii::$app->session->setFlash('error', 'Unable To Save Details. Kindly Try Registering Again');
            return $this->goHome();
        }
        return $this->render('register', [
            'model' => $model,
            'MembershipTypes' => $this->getMembershipTypes()

        ]);
    }

    public function actionResend()
    {
        // exit('7888');
        $this->layout = 'register';
        $model = new MemberTypeAndPhoneNo();
        $OauthData = Yii::$app->session->get('OauthData');
        $model->phoneNo = $OauthData->{'Phone No_'};
        $model->IDnumber = $OauthData->{'ID No_'};
        $user  = $model->IfUserExists();
        if ($user) {
            return $this->SendOTP($model);
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
            'message' => 'Dear member, your OTP to log into the portal is ' . $token . '. Use this token to validate your login.',
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

    public function SendPasswordChangeSMS($token, $PhoneNo)
    {
        //Todo: Clean The Phone Number to Form 07... 0r 2547....

        $url = Yii::$app->params['SMS']['BaseURL'];
        $ch = curl_init($url);
        $BearerToken = Yii::$app->params['SMS']['AcessToken'];

        $data = [
            'sender' => 'MHASIBU',
            'message' => 'You are trying to change your password on the portal. Your Token is ' . $token . '. Use this token to validate your login.',
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



    public function actionLoanArrears()
    {

        return $this->render('loan-arrears', [
            'LoansInAreas' => Yii::$app->user->identity->getLoanAreas()
        ]);
    }


    public function getMembershipTypes()
    {
        $service = Yii::$app->params['ServiceName']['MemberCategories'];
        $filter = [
            'isChildAccount' => 0
        ];
        $res = [];
        $MemberCategories = \Yii::$app->navhelper->getData($service, $filter);
        foreach ($MemberCategories as $MemberCategory) {
            if (!empty($MemberCategory->Code))
                $res[] = [
                    'Code' => $MemberCategory->Code,
                    'Name' => $MemberCategory->Description
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
