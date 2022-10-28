<?php

namespace app\models;

use Yii;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;

/**
 * User model
 *
 * @property integer $id
 * @property string $username
 * @property string $password_hash
 * @property string $password_reset_token
 * @property string $verification_token
 * @property string $email
 * @property string $auth_key
 * @property integer $status
 * @property integer $created_at
 * @property integer $updated_at
 * @property string $VendorId
 * @property string $password write-only password
 */
class user extends ActiveRecord implements IdentityInterface
{
    const STATUS_DELETED = 0;
    const STATUS_INACTIVE = 9;
    const STATUS_ACTIVE = 10;
    private $LoginID;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return  Yii::$app->params['DbCompanyName'] . 'Members '; //'PortalMembers ';
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            ['status', 'default', 'value' => self::STATUS_INACTIVE],
            ['status', 'in', 'range' => [self::STATUS_ACTIVE, self::STATUS_INACTIVE, self::STATUS_DELETED]],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentity($No)
    {
        // print_r($No);
        // exit;
        //$username = strtoupper(Yii::$app->params['ldPrefix'].'\\'.$id);
        return static::findOne(['No_' => $No]);
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        throw new NotSupportedException('"findIdentityByAccessToken" is not implemented.');
    }

    /**
     * Finds user by username
     *
     * @param string $username
     * @return static|null
     */
    public static function findByUsername($IdNo)
    {


        return static::findOne(['ID No_' => $IdNo]);
    }

    public static function findByMemberNoAndIdNumber($MemberNo, $IdNo)
    {
        return static::findOne(
            [
                'No_' => $MemberNo,
                'ID No_' => $IdNo,
                // 'Blocked'=>0,
                // 'Member Status'=>[0,3,4,5]                
            ]
        );
    }

    public static function findByEmail($email)
    {
        //$username = strtoupper(Yii::$app->params['ldPrefix'].'\\'.$username);

        return static::findOne(['E-Mail' => $email]);
    }

    public static function findByApplicantId($Applicant)
    {
        //$username = strtoupper(Yii::$app->params['ldPrefix'].'\\'.$username);

        return static::findOne([
            'Appliccation No' => $Applicant,
            // 'memberNo' => 0,
        ]);
    }

    public static function findByApplicantWithNoMemberNo($Applicant)
    {
        //$username = strtoupper(Yii::$app->params['ldPrefix'].'\\'.$username);
        return static::findOne([
            'ApplicationId' => $Applicant,
            'hasMemberNo' => 0,
            'ApplicationApproved' => 2, // Approved
        ]);
    }


    public static function ApplicantWhoseApplicationHasJustBeenApproved($Applicant)
    {
        //$username = strtoupper(Yii::$app->params['ldPrefix'].'\\'.$username);
        return static::findOne([
            'ApplicationId' => $Applicant,
            'hasMemberNo' => 0,
            'ApplicationApproved' => 0 //Not Yet Approved
        ]);
    }

    public static function findById($id)
    {
        return static::findOne([
            'No_' => $id,
        ]);
    }

    public static function findByMemberTypeAndPhoneNo($MemberType, $PhoneNo)
    {
        return static::findOne([
            'Member Category' => $MemberType,
            'Phone No_' => $PhoneNo,
            // 'hasMemberNo'=> 1,
        ]);
    }

    public static function findApplicant($MemberType, $PhoneNo)
    {
        return static::findOne([
            'Member Category' => $MemberType,
            'Phone No_' => $PhoneNo,
            // 'hasMemberNo'=> false,
        ]);
    }

    /**
     * Finds user by password reset token
     *
     * @param string $token password reset token
     * @return static|null
     */
    public static function findByPasswordResetToken($token)
    {

        if (!static::isPasswordResetTokenValid($token)) {
            return null;
        }

        return static::findOne([
            'password_reset_token' => $token,
            'status' => self::STATUS_ACTIVE,
        ]);
    }

    /**
     * Finds user by verification email token
     *
     * @param string $token verify email token
     * @return static|null
     */
    public static function findByVerificationToken($token)
    {

        return static::findOne([
            'verification_token' => $token,
            // 'status' => self::STATUS_INACTIVE
        ]);
    }

    public static function findByLoginToken($token)
    {
        $memmberData = Yii::$app->session->get('MemberData');
        return static::findOne([
            'verification_token' => $token,
            // 'status' => self::STATUS_ACTIVE,
            // 'No_' => $memmberData->{'No_'}
        ]);
    }



    public static function verifyTransactionToken($token)
    {
        $memmberData = Yii::$app->session->get('userDetails');
        return static::findOne([
            'Transaction OTP' => $token,
            // 'status' => self::STATUS_ACTIVE,
            'No_' => $memmberData->{'No_'}
        ]);
    }


    public static function findApplicantByLoginToken($token)
    {
        return static::findOne([
            'verification_token' => $token,
            'status' => self::STATUS_ACTIVE,
            // 'memebershipType'=> Yii::$app->session->get('MembershipType')
        ]);
    }

    /**
     * Finds out if password reset token is valid
     *
     * @param string $token password reset token
     * @return bool
     */
    public static function isPasswordResetTokenValid($token)
    {

        if (empty($token)) {
            return false;
        }

        $timestamp = (int) substr($token, strrpos($token, '_') + 1);
        $expire = Yii::$app->params['user.passwordResetTokenExpire'];
        return $timestamp + $expire >= time();
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->{'No_'};
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    public function getNationalIDNo()
    {
        return $this->{'No_'};
    }

    /**
     * {@inheritdoc}
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    public function validateID($ID_Number)
    {
        return $this->getNationalIDNo() === $ID_Number;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return bool if password provided is valid for current user
     */
    public function validatePassword($password)
    {

        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }

    /**
     * Generates password hash from password and sets it to the model
     *
     * @param string $password
     */
    public function setPassword($password)
    {

        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }

    /**
     * Generates "remember me" authentication key
     */
    public function generateAuthKey()
    {

        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    /**
     * Generates new password reset token
     */
    public function generatePasswordResetToken()
    {

        $this->password_reset_token = Yii::$app->security->generateRandomString() . '_' . time();
    }

    /**
     * Generates new token for email verification
     */
    public function generateEmailVerificationToken()
    {
        $this->verification_token = Yii::$app->security->generateRandomString() . '_' . time();
    }

    /**
     * Removes password reset token
     */
    public function removePasswordResetToken()
    {
        $this->password_reset_token = null;
    }

    /*public static function getDb(){
        return Yii::$app->nav;
    }*/

    public function getMemberCategory()
    {
        $service = Yii::$app->params['ServiceName']['MemberApplicationCard'];
        $employee = \Yii::$app->navhelper->findOneRecord($service, 'Application_No', Yii::$app->user->identity->ApplicationId);
        return $employee->Member_Category;
    }

    public function getMemberApplicationStatus()
    {
        $service = Yii::$app->params['ServiceName']['MemberApplicationCard'];
        $employee = \Yii::$app->navhelper->findOneRecord($service, 'Application_No', Yii::$app->user->identity->ApplicationId);
        return $employee->Approval_Status;
    }

    public function getApplicantData()
    {
        $service = Yii::$app->params['ServiceName']['MemberApplicationCard'];
        $Applicant = \Yii::$app->navhelper->findOneRecord($service, 'Application_No', Yii::$app->user->identity->{'Appliccation No'});
        return $Applicant;
    }

    public function getMemberData()
    {
        $service = Yii::$app->params['ServiceName']['CustomerCard'];

        $Applicant = \Yii::$app->navhelper->findOneRecord($service, 'Application_No', $this->{'No_'});
        return $Applicant;
    }

    public function getAccounts()
    {
        $service = Yii::$app->params['ServiceName']['SavingsStatistics'];

        $filter = [
            'Member_No' => $this->{'No_'},
        ];
  

        $CustomerCardDetails = Yii::$app->navhelper->getData($service, $filter);

        if (is_object($CustomerCardDetails)) {
            return array();
        }
        return $CustomerCardDetails;
    }



    public function getCustomerCardDetails()
    {
        $service = Yii::$app->params['ServiceName']['MemberSingle'];
        $filter = [
            'No' => $this->{'No_'},
        ];
        $CustomerCardDetails = Yii::$app->navhelper->getData($service, $filter);

        if (is_object($CustomerCardDetails)) {
            return array();
        }
        return $CustomerCardDetails[0];
    }


    public function getLoanAreas()
    {
        $service = Yii::$app->params['ServiceName']['LoansLookup'];
        $filter = [
            'Member_No' => $this->{'No_'}
        ];
        $Loans = Yii::$app->navhelper->getData($service, $filter);
        $result = [];

        if (is_array($Loans)) {
            foreach ($Loans as $Loan) {
                if ($Loan->Total_Arrears <= 0) {
                    continue;
                }


                $result[] = [
                    'Key' => $Loan->Key,
                    'Application_No' => isset($Loan->Application_No) ? $Loan->Application_No : '',
                    'Application_Date' => !empty($Loan->Application_Date) ? $Loan->Application_Date : '',
                    'Product_Description' => isset($Loan->Product_Description) ? $Loan->Product_Description : '',
                    'Approved_Amount' => isset($Loan->Approved_Amount) ? $Loan->Approved_Amount : '',
                    'Repayment_Start_Date' => isset($Loan->Repayment_Start_Date) ? $Loan->Repayment_Start_Date : '',
                    'Repayment_End_Date' => isset($Loan->Repayment_End_Date) ? $Loan->Repayment_End_Date : '',
                    'Loan_Balance' => isset($Loan->Loan_Balance) ? $Loan->Loan_Balance : '',
                    'Total_Arrears' => isset($Loan->Total_Arrears) ? $Loan->Total_Arrears : ''
                ];
            }
        }


        return $result;
    }

    public function getMemberKins()
    {

        $service = Yii::$app->params['ServiceName']['NextoKIN'];
        $filter = [
            'Account_No' => $this->{'No_'}
        ];

        $result = Yii::$app->navhelper->getData($service, $filter);
        if (is_array($result)) {
            return $result;
        } else {
            return false;
        }
    }

    public function getMyAccounts()
    {
        $service = Yii::$app->params['ServiceName']['PortalReports'];

        $data = [
            'memberNo' => $this->{'No_'},
        ];
        $response = Yii::$app->navhelper->PortalReports($service, $data, 'GetMemberAccounts');
        //  Yii::$app->recruitment->printrr($response);

        if (is_array($response)) {

            if (isset($response['return_value'])) { //image iko
                return json_decode($response['return_value']);
            } else {
                return false;
            }
        }
        return false;
    }
}
