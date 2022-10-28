<?php
namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use app\models\VerifyPhoneForm;
use yii\filters\VerbFilter;
use app\models\MemberApplicationCard;
use yii\helpers\VarDumper;

class VerifyPhoneNoController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['index'],
                'rules' => [

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
    public function actionIndex(){
        $this->layout = 'register';
        $model = new VerifyPhoneForm();
        if(Yii::$app->request->post()){  
            $model = new VerifyPhoneForm();
            $user = $model->verifyPhoneNo(Yii::$app->request->post()['VerifyPhoneForm']['token']);
            if (isset($user[0]['description'])) {
                Yii::$app->session->setFlash('error', $user[0]['description']);
                return $this->redirect(Yii::$app->request->referrer);
            }
            else{
                  //Check If User Has an ApplicationNo
                if(empty($user->ApplicationId)){ //Has No Applicaation No
                    $sendUserToNavResult = $this->SendUserToNav($user);
                    if (is_array($sendUserToNavResult)) {
                        $user->delete();
                        Yii::$app->session->setFlash('error', $sendUserToNavResult[0]['description']);
                        return $this->goHome();
                    }
                    elseif ($user == false){
                        Yii::$app->session->setFlash('error', 'We are able to Verify your Account');
                        return $this->goHome();
                    }
                    else{
                        if (Yii::$app->user->login($user)) {
                            Yii::$app->session->setFlash('success', 'Your Account Has been confirmed!');
                            return $this->goHome();
                        }
                    }
                }
                if (Yii::$app->user->login($user)) {
                    Yii::$app->session->setFlash('success', 'Your Account Has been confirmed!');
                    return $this->goHome();
                }
              
                return $this->redirect(['reset-password/']);
            }
        }
        return $this->render('index', ['model'=>$model]);        
    }

    public function SendUserToNav($user){
        
        $transaction = Yii::$app->db->beginTransaction();
        try{
            if(is_array($addtoNavResult = $this->actionAddUserToDynamics($user)) ){ //Unable to Add to Nav
                $transaction->rollBack();
                return $addtoNavResult;
            }
            $transaction->commit();
                return $user;
        }
        catch(Exception $e)
        {
            $transaction->rollBack();
            return false;
        }
    }

    public function actionAddUserToDynamics($user){
        $service = Yii::$app->params['ServiceName']['MemberApplicationCard'];
        $memberRegistrationModel = new MemberApplicationCard();
        $memberRegistrationModel->E_Mail_Address = $user->email;
        $memberRegistrationModel->Member_Category = $user->memebershipType;
        $memberRegistrationModel->Mobile_Phone_No = $user->phoneNo;
        $memberRegistrationModel->SMS_Notification_Number = $user->phoneNo;
        $addToNavResult =  Yii::$app->navhelper->postData($service,$memberRegistrationModel);
        // VarDumper::dump( $addToNavResult, $depth = 10, $highlight = true); exit;
        if(is_object($addToNavResult)){
            if($updateMemeberNoResult = $this->updateMemberApplicationNo($user, $addToNavResult)){
                return true;
            }
            else{
                return false; //unable to save to Database
            }
        }
        return [
            [
                'error'=>1,
                'description'=>$addToNavResult
            ]
        ];
        

        

    }

    public function updateMemberApplicationNo($user,$result){
        $user->ApplicationId = $result->Application_No;
       return $user->save(false) ;
    }


  

    
}
