<?php

namespace app\models;

use app\models\Vuser;
use app\models\User;
use yii\base\InvalidArgumentException;
use yii\base\Model;
use Yii;
use Exception;
use yii\db\ActiveRecord;
use yii\helpers\VarDumper;

class VerifyPhoneForm extends ActiveRecord
{

    public $token;
    private $_user;

    public $IDnumber;

    public function rules()
    {
        return [
            [['token'], 'required']
        ];
    }

    public function verifyPhoneNo($token)
    {

        $this->_user = User::findByVerificationToken($token);

        if (!$this->_user) {
            return [
                [
                    'error' => 1,
                    'description' => 'The Token You Have Provided is Incorrect.'
                ]
            ];
        }



        try {
            $user = $this->_user;
            $user->status = User::STATUS_ACTIVE;
            $user->save(false);
            return $user;
        } catch (Exception $e) {
            return false;
        }
    }

    public function verifyLoginToken($token)
    {

        $this->_user = User::findByLoginToken($token);

        if (!$this->_user) {
            return [
                [
                    'error' => 1,
                    'description' => 'The Token You Have Provided is Incorrect.'
                ]
            ];
        }

        // if($this->_user->token_expires_at < time() ){ //Token Expired
        //     return [
        //         [
        //             'error'=>1,
        //             'description'=>'Your token has expired. Kindly generate another one'
        //         ]
        //     ];
        // }

        try {
            $user = $this->_user;
            // $user->loggedIn = 1;
            // $user->logged_in_at = time();
            $user->status = User::STATUS_ACTIVE;
            $user->verification_token = 1;
            $user->save(false);
            return $user;
        } catch (Exception $e) {
            return false;
        }
    }


    public function verifyApplicantLoginToken($token)
    {

        $this->_user = User::findApplicantByLoginToken($token);

        if (!$this->_user) {
            return [
                [
                    'error' => 1,
                    'description' => 'The Token You Have Provided is Incorrect.'
                ]
            ];
        }

        try {
            $user = $this->_user;
            $user->status = User::STATUS_ACTIVE;
            $user->verification_token = 1;
            $user->save(false);
            return $user;
        } catch (Exception $e) {
            return false;
        }
    }

    public function actionAddUserToDynamics($user)
    {
        $service = Yii::$app->params['ServiceName']['MemberApplicationCard'];

        $memberRegistrationModel = new MemberApplicationCard();
        // $memberRegistrationModel->First_Name = $user->firstName;
        // $memberRegistrationModel->Last_Name = $user->lastName;
        $memberRegistrationModel->E_Mail_Address = $user->email;
        $memberRegistrationModel->Member_Category = $user->memebershipType;
        // $memberRegistrationModel->Mobile_Phone_No = $user->phoneNo;
        // $memberRegistrationModel->SMS_Notification_Number = $user->phoneNo;
        // $memberRegistrationModel->National_ID_No = $user->idNo;

        $addToNavResult =  Yii::$app->navhelper->postData($service, $memberRegistrationModel);
        // VarDumper::dump( $addToNavResult, $depth = 10, $highlight = true); exit;
        if (is_object($addToNavResult)) {
            if ($updateMemeberNoResult = $this->updateMemberApplicationNo($addToNavResult)) {
                return true;
            } else {
                return false; //unable to save to Database
            }
        }
        return [
            [
                'error' => 1,
                'description' => $addToNavResult
            ]
        ];
    }

    public function updateMemberApplicationNo($result)
    {
        $user = $this->_user;
        $user->ApplicationId = $result->Application_No;
        return $user->save(false);
    }
}
