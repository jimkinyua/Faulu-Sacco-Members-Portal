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

class VerifyApplicantPhoneForm extends ActiveRecord
{

    public $token;
    private $_user;

    public function rules()
    {
        return [
            [['token'], 'required']
        ];
    }

    public function verifyPhoneNo($token)
    {

        $this->_user = ApplicantUser::findByVerificationToken($token);

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
            $user->status = ApplicantUser::STATUS_ACTIVE;
            $user->save(false);
            return $user;
        } catch (Exception $e) {
            return false;
        }
    }

    public function verifyLoginToken($token)
    {

        $this->_user = ApplicantUser::findByLoginToken($token);

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
            // $user->status = ApplicantUser::STATUS_ACTIVE;
            $user->verification_token = 0;
            $user->save(false);
            return $user;
        } catch (Exception $e) {
            return false;
        }
    }


    public function verifyApplicantLoginToken($token)
    {

        $this->_user = ApplicantUser::findApplicantByLoginToken($token);

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
            $user->status = ApplicantUser::STATUS_ACTIVE;
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

        $memberRegistrationModel->First_Name = $user->firstName;
        // $memberRegistrationModel->Last_Name = $user->lastName;
        $memberRegistrationModel->_x0026_E_Mail_Address = $user->email;
        $memberRegistrationModel->Member_Category = $user->membershipType;
        $memberRegistrationModel->Mobile_Phone_No = $user->phoneNo;
        // $memberRegistrationModel->SMS_Notification_Number = $user->phoneNo;
        $memberRegistrationModel->National_ID_No = $user->NationalID;

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
