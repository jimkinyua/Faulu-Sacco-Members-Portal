<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;
use app\models\MemberTypeAndPhoneNo;
use app\models\VerifyEmailForm;
use yii\helpers\VarDumper;
use app\models\MemberStatistics;

class MemberShipPhoneNoController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout',  'register'],
                'rules' => [

                    [
                        'actions' => ['index', 'logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],

                    [
                        'actions' => ['index'],
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



    public function actionError(){
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
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }
        $this->layout = 'login';
        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        }

        $model->password = '';
        return $this->render('login', [
            'model' => $model,
        ]);
    }

    /**
     * Logout action.
     *
     * @return Response
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * Displays contact page.
     *
     * @return Response|string
     */
    public function actionContact()
    {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->contact(Yii::$app->params['adminEmail'])) {
            Yii::$app->session->setFlash('contactFormSubmitted');

            return $this->refresh();
        }
        return $this->render('contact', [
            'model' => $model,
        ]);
    }

      /**
     * Displays contact page.
     *
     * @return Response|string
     */
    public function actionIndex()
    {
        $this->layout = 'register';

        $model = new MemberTypeAndPhoneNo();
        if ($model->load(Yii::$app->request->post())) {
            $user  = $model->IfUserExists();
            if($user){
                $token = rand(12453, 4795236);
                $user->verification_token = $token ;
                if($user->save(false)){
                    if($this->SendSMS($token,$user->phoneNo) === true){
                        Yii::$app->session->set('PhoneNumber', $user->phoneNo);
                        Yii::$app->session->set('MembershipType', $user->memebershipType);

                        Yii::$app->session->setFlash('success', 'Thank you for registration. It Seems We Already Have Your Details. We Have Sent a Verification Token to Your Mobile Number. ');
                        return $this->redirect(['verify-phone-no/']);
                    }
                }              
            }
            Yii::$app->session->set('PhoneNumber', $model->phoneNo);
            Yii::$app->session->set('MembershipType', $model->memebershipType);
            $this->redirect(['site/register']);
        }
        return $this->render('index', [
            'model' => $model,
            'MembershipTypes'=>$this->getMembershipTypes()
        ]);
    }

    public function SendSMS($token, $PhoneNo){
        //Todo: Clean The Phone Number to Form 07... 0r 2547....

        $url =Yii::$app->params['SMS']['BaseURL'];
        $ch = curl_init($url);
        $BearerToken =Yii::$app->params['SMS']['AcessToken'];

        $data = [
            'sender'=> 'MHASIBU',
            'message'=> 'Your Verification Code is '. $token,
            'phone'=> $PhoneNo,
        ];
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: Bearer  '. $BearerToken));
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        $response = curl_exec($ch);
        $result = json_decode($response);
        curl_close($ch); // Close the connection
        if(empty($result->status)){ //Error
            Yii::$app->session->setFlash('error', 'Unable to Send a Message to the ');
            return $this->redirect(Yii::$app->request->referrer);
        }
        return true;

    }

    public function actionResend(){
        $this->layout = 'register';
        $model = new MemberTypeAndPhoneNo();
        $model->phoneNo = Yii::$app->session->get('PhoneNumber');
        $model->memebershipType = Yii::$app->session->get('MembershipType');
        $user  = $model->IfUserExists();

        if($user){
            $token = rand(12453, 4795236);
            $user->verification_token = $token ;
            if($user->save(false)){
                if($this->SendSMS($token,$user->{'Phone No_'}) === true){
                    Yii::$app->session->set('PhoneNumber', $user->{'Phone No_'});
                    Yii::$app->session->set('MembershipType', $user->{'Member Category'});
                    Yii::$app->session->setFlash('success', 'We Have Resent the Verification Code ');
                    return $this->redirect(['verify-phone-no/']);
                }
            }              
        }

    }

     /**
     * Verify email address
     *
     * @param string $token
     * @throws BadRequestHttpException
     * @return yii\web\Response
     */
    public function actionVerifyEmail($token)
    {
        try {
            $model = new VerifyEmailForm($token);
        } catch (InvalidArgumentException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }
        $user = $model->verifyEmail();

            if (is_array($user)) {
                // echo '<pre>';
                // print_r($user);
                // exit;
                
                Yii::$app->session->setFlash('error', $user[0]['description']);
                return $this->goHome();
            }
            elseif ($user == false){
                Yii::$app->session->setFlash('error', 'We are able to Verify your Account');
                return $this->goHome();
            }
            else{
                if (Yii::$app->user->login($user)) {
                    Yii::$app->session->setFlash('success', 'Your email has been confirmed!');
                    return $this->goHome();
                }
            }
        

        
    }

    public function getMembershipTypes(){
        $service = Yii::$app->params['ServiceName']['MemberCategories'];
        $res = [];
        $MemberCategories = \Yii::$app->navhelper->getData($service);
        foreach($MemberCategories as $MemberCategory){
            if(!empty($MemberCategory->Code))
            $res[] = [
                'Code' => $MemberCategory->Code,
                'Name' => $MemberCategory->Description
            ];
        }

        return $res;
    }

   

    /**
     * Displays about page.
     *
     * @return string
     */
    public function actionAbout()
    {
        return $this->render('about');
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
