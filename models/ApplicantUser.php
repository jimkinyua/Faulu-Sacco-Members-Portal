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
class ApplicantUser extends ActiveRecord implements IdentityInterface
{
    public $dateOfBirth;
    const STATUS_DELETED = 0;
    const STATUS_INACTIVE = 9;
    const STATUS_ACTIVE = 10;
    private $_user = false;



    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'PortalMemberApplicationsAuth ';
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
    public static function findIdentity($id)
    {
        //$username = strtoupper(Yii::$app->params['ldPrefix'].'\\'.$id);
        return static::findOne(['id' => $id]);
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
    public static function findByUsername($email)
    {
        //$username = strtoupper(Yii::$app->params['ldPrefix'].'\\'.$username);

        return static::findOne(['email' => $email]);
    }

    public static function findByMemberNo($MemberNo)
    {
        return static::findOne(['memberNo' => $MemberNo]);
    }

    public static function findByEmail($email)
    {
        //$username = strtoupper(Yii::$app->params['ldPrefix'].'\\'.$username);

        return static::findOne(['email' => $email]);
    }

    public static function findByApplicantId($Applicant)
    {
        //$username = strtoupper(Yii::$app->params['ldPrefix'].'\\'.$username);

        return static::findOne([
            'ApplicationId' => $Applicant,
            // 'memberNo' => 0,
        ]);
    }

    public static function findByApplicantWithNoMemberNo($Applicant)
    {
        //$username = strtoupper(Yii::$app->params['ldPrefix'].'\\'.$username);
        return static::findOne([
            'ApplicationId' => $Applicant,
            'hasMemberNo' => 0,
            'ApplicationApproved' => 0 // Approved

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
            'id' => $id,
        ]);
    }

    public static function findByMemberTypeAndPhoneNo($MemberType, $PhoneNo)
    {
        return static::findOne([
            'memebershipType' => $MemberType,
            'phoneNo' => $PhoneNo,
            'hasMemberNo' => 1,
        ]);
    }

    public static function findApplicant($MemberType, $PhoneNo)
    {
        return static::findOne([
            'memebershipType' => $MemberType,
            'phoneNo' => $PhoneNo,
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
            'status' => self::STATUS_INACTIVE
        ]);
    }

    public static function findByLoginToken($token)
    {
        return static::findOne([
            'verification_token' => $token,
            'status' => self::STATUS_INACTIVE,
            'phoneNo' => Yii::$app->session->get('PhoneNumber'),
            'memebershipType' => Yii::$app->session->get('MembershipType')
        ]);
    }

    public static function findApplicantByLoginToken($token)
    {
        return static::findOne([
            'verification_token' => $token,
            // 'status' => self::STATUS_INACTIVE,
            'memebershipType' => Yii::$app->session->get('MembershipType'),
            'phoneNo' => Yii::$app->session->get('PhoneNumber'),

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
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * {@inheritdoc}
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
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
        $employee = \Yii::$app->navhelper->findOneRecord($service, 'Application_No', Yii::$app->applicant->identity->ApplicationId);
        return $employee->Member_Category;
    }

    public function getMemberApplicationStatus()
    {
        $service = Yii::$app->params['ServiceName']['MemberApplicationCard'];
        $employee = \Yii::$app->navhelper->findOneRecord($service, 'Application_No', Yii::$app->applicant->identity->ApplicationId);
        return $employee->Approval_Status;
    }

    public function getApplicantData()
    {
        $service = Yii::$app->params['ServiceName']['MemberApplicationCard'];
        $Applicant = \Yii::$app->navhelper->findOneRecord($service, 'Application_No', Yii::$app->applicant->identity->ApplicationId);
        return $Applicant;
    }

    public function getMemberData()
    {
        $service = Yii::$app->params['ServiceName']['CustomerCard'];
        $Applicant = \Yii::$app->navhelper->findOneRecord($service, 'No', $this->memberNo);
        return $Applicant;
    }

    public function getMemberStatistics()
    {
        $service = Yii::$app->params['ServiceName']['MemberStatistics'];
        $Applicant = \Yii::$app->navhelper->findOneRecord($service, 'No', $this->memberNo);
        return $Applicant;
    }
}
