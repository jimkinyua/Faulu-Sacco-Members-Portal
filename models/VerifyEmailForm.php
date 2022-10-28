<?php

namespace app\models;

use app\models\Vuser;
use app\models\User;
use yii\base\InvalidArgumentException;
use yii\base\Model;
use Yii;
use Exception;
use yii\helpers\VarDumper;

class VerifyEmailForm extends Model
{
    /**
     * @var string
     */
    public $token;

    /**
     * @var User
     */
    private $_user;


    /**
     * Creates a form model with given token.
     *
     * @param string $token
     * @param array $config name-value pairs that will be used to initialize the object properties
     * @throws InvalidArgumentException if token is empty or not valid
     */
    public function __construct($token, array $config = [])
    {
        if (empty($token) || !is_string($token)) {
            throw new InvalidArgumentException('Verify email token cannot be blank.');
        }
       // $this->_user = User::findByVerificationToken($token);//find an erp user - default identity
       
        $this->_user = User::findByVerificationToken($token);//Find a supplier user

        if (!$this->_user) {
            throw new InvalidArgumentException('Wrong verify email token.');
        }
        parent::__construct($config);
    }

    /**
     * Verify email
     *
     * @return User|null the saved model or null if saving fails
     */
    public function verifyEmail()
    {
        $transaction = Yii::$app->db->beginTransaction();

        try
        {
            
            $user = $this->_user;
            $user->status = User::STATUS_ACTIVE;
            $user->save(false) ;
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
        // $memberRegistrationModel->First_Name = $user->firstName;
        // $memberRegistrationModel->Last_Name = $user->lastName;
        $memberRegistrationModel->E_Mail_Address = $user->email;
        $memberRegistrationModel->Member_Category = $user->memebershipType;
        // $memberRegistrationModel->Mobile_Phone_No = $user->phoneNo;
        // $memberRegistrationModel->SMS_Notification_Number = $user->phoneNo;
        // $memberRegistrationModel->National_ID_No = $user->idNo;

        $addToNavResult =  Yii::$app->navhelper->postData($service,$memberRegistrationModel);
        // VarDumper::dump( $addToNavResult, $depth = 10, $highlight = true); exit;
        if(is_object($addToNavResult)){
            if($updateMemeberNoResult = $this->updateMemberApplicationNo($addToNavResult)){
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

    public function updateMemberApplicationNo($result){
        $user = $this->_user;
        $user->ApplicationId = $result->Application_No;
       return $user->save(false) ;
    }

}
