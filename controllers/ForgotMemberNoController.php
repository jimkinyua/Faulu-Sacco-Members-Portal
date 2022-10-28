<?php
namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use app\models\ForgotMemberNoForm;
use yii\filters\VerbFilter;
use app\models\MemberApplicationCard;

class ForgotMemberNoController extends Controller
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
    public function SendSMS($Message, $PhoneNo){
        //Todo: Clean The Phone Number to Form 07... 0r 2547....
        $PhoneNo =  ltrim($PhoneNo, '+254');
            //  exit($PhoneNo);

        $url =Yii::$app->params['SMS']['BaseURL'];
        $ch = curl_init($url);
        $BearerToken =Yii::$app->params['SMS']['AcessToken'];

        $data = [
            'sender'=> 'MHASIBU',
            'message'=> $Message,
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
            // Yii::$app->session->setFlash('error', 'Unable to Send a Message to the ');
            // return $this->redirect(Yii::$app->request->referrer);
            //Notify ICT
            return false;
        }
        return true;

    }


    public function actionIndex(){
        $this->layout = 'register';
        $model = new ForgotMemberNoForm();
        if( Yii::$app->request->post() && $model->load(Yii::$app->request->post() )){  
            $user = $model->IfUserExists();
            
            if($user){
                //Send SMS
                $Message = 'Your MembershipNo is '. $user->memberNo;
               if( $this-> SendSMS($Message, $user->phoneNo)){
                    Yii::$app->session->setFlash('success', 'We have sent the Membership No To Your Phone No');
                    return $this->redirect(Yii::$app->request->referrer);
               }
               Yii::$app->session->setFlash('error', 'We have found Your account but we are unable to send it to you. Kindly try again after a while or contact us through ict@mhasibussaco.com');
               return $this->redirect(Yii::$app->request->referrer);
            }

            Yii::$app->session->setFlash('error', 'Sorry we are unable to locate your account. If you think this is a mistake, contact us via ict@mhasibusacco.com');
            return $this->redirect(Yii::$app->request->referrer);
        }
        return $this->render('index', [
            'model'=>$model,
            'MembershipTypes'=>$this->getMembershipTypes()
        ]);        
    }

    public function getMembershipTypes(){
        $service = Yii::$app->params['ServiceName']['MemberCategories'];
        $res = [];
        $filter =  [
            'isChildAccount'=>0
        ];
        $MemberCategories = \Yii::$app->navhelper->getData($service, $filter);
        foreach($MemberCategories as $MemberCategory){
            if(!empty($MemberCategory->Code))
            $res[] = [
                'Code' => $MemberCategory->Code,
                'Name' => $MemberCategory->Description
            ];
        }

        return $res;
    }

    
}
