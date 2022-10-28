<?php

namespace app\models;

use borales\extensions\phoneInput\PhoneInputValidator;
use Yii;
use yii\base\Model;

/**
 * ContactForm is the model behind the contact form.
 */
class Register extends Model
{
    public $oldemail;
    public $oldNationalID;
    public $oldphoneNo;

    public $email;
    public $idNo;
    public $password;
    public $confirmPassword;
    public $verifyCode;
    public $lastName;
    public $firstName;
    public $kraPinNo;
    public $memebershipType;
    public $phoneNo;
    public $agreeToTerms;
    public $membershipType;
    public $NationalID;
    public $IdentificationType;
    public $DateofBirth;

    private $_user;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            // [['NationalID', 'firstName', 'IdentificationType', 'email', 'phoneNo'], 'required'],
            [['phoneNo'], PhoneInputValidator::className()],
            [['email', 'phoneNo', 'firstName', 'NationalID'], 'trim'],
            // [['NationalID'], 'number'],
            [['email'], 'email'],
            array('NationalID', 'checkUniqueness'),
            

            ['membershipType', 'default', 'value' => env('IndividualAccount')],

            // [['phoneNo','memebershipType'], 'unique', 
            // 'targetClass' => '\app\models\User', 
            // 'targetAttribute' => ['PortalMembers.phoneNo', 'memebershipType'], 
            // 'message' => 'Already taken!'],

        ];
    }

    public function checkUniqueness($attribute, $params)
    {
        if ($this->email !== $this->oldemail || $this->NationalID !== $this->oldNationalID || $this->phoneNo !== $this->oldphoneNo) {
            // $model = User::find('email = ?  AND memebershipType = ? AND phoneNo = ?', array( '', ''));
            $model = User::find()->where(['ID No_' => $this->NationalID])->one();
            // echo '<pre>';
            // print_r($user);
            // exit;
            if ($model != null) {
                $this->addError('NationalID', 'An Account With This ID Number Exists. Contact us incase of any questions');
                $this->addError('email', 'An Account With These Details Already Exists');
                $this->addError('phoneNo', 'An Account With These Details Already Exists');
            }
        }
    }

    protected function afterFind()
    {
        parent::afterFind();
        $this->oldemail = $this->email;
        $this->oldphoneNo = $this->phoneNo;
        $this->oldmemebershipType = $this->memebershipType;
        // echo '<pre>';
        // print_r($this->oldemail);
        // exit;
    }

    public function actionAddUserToDynamics($user)
    {
        $this->_user = $user;
        $service = Yii::$app->params['ServiceName']['MemberApplicationSingle'];

        $memberRegistrationModel = new MemberApplicationCard();

        $memberRegistrationModel->Name = $user->firstName ;
        $memberRegistrationModel->Marital_Status = 'Single';
        $memberRegistrationModel->E_Mail = $user->email;
        $memberRegistrationModel->Member_Category = $user->membershipType;
        $memberRegistrationModel->Phone_No = $user->phoneNo;
        $memberRegistrationModel->Date_of_Birth = date('Y-m-d', strtotime('18 years ago'));
        $memberRegistrationModel->ID_No = $user->NationalID;
        // $memberRegistrationModel->Subscription_Start_Date = date('Y-m-d');
        $memberRegistrationModel->Date_of_Birth = $this->DateofBirth;

        $addToNavResult =  Yii::$app->navhelper->postData($service, $memberRegistrationModel);
        // VarDumper::dump( $addToNavResult, $depth = 10, $highlight = true); exit;
        if (is_object($addToNavResult)) {
            if ($updateMemeberNoResult = $this->updateMemberApplicationNo($addToNavResult)) {
                return true;
            } else {
                return false; //unable to save to Database
            }
        }
        return $addToNavResult;
    }

    public function updateMemberApplicationNo($result)
    {
        $user = $this->_user;
        $user->ApplicationId = $result->No;
        return $user->save(false);
    }

    public function checkIfApplicationExistsOnNavision()
    {
        $this->_user = NavisionMemberApplication::findApplicant($this->NationalID);
        if ($this->_user) {
            return $this->_user;
        }
        return false;
    }



    /**
     * @return array customized attribute labels
     */
    public function attributeLabels()
    {
        return [
            'memebershipType' => 'MemberShip Type',
            'IdentificationType' => 'Type of ID',
            'firstName' => 'First Name as per your ID'
        ];
    }

    /**
     * Sends confirmation email to user
     * @param User $user user model to with email should be send
     * @return bool whether the email was sent
     */
    protected function sendEmail($user)
    {
        return Yii::$app
            ->mailer
            ->compose(
                ['html' => 'emailVerify-html', 'text' => 'emailVerify-text'],
                ['user' => $user]
            )
            ->setFrom(['jimkinyua25@gmail.com' => 'Memeber Testing'])
            ->setTo($this->email)
            ->setSubject('Account registration at ' . Yii::$app->name)
            ->send();
    }

    public function signup()
    {

        $user = new ApplicantUser();
        $user->phoneNo = $this->phoneNo;
        $user->membershipType = $this->membershipType;
        $user->memebershipType = $this->membershipType;
        $user->email = $this->email;
        $user->firstName = $this->firstName;
        $user->IdentificationType = $this->IdentificationType;
        $user->NationalID = $this->NationalID;
        $user->setPassword($this->email);
        $user->generateAuthKey();
        $user->generateEmailVerificationToken();
        if ($user->save()) {
            return $user;
        }
        return false;
    }

    public function IfUserExists()
    {
        if (!$this->validate()) {
            return null;
        }

        $user = new User();
        $_user = $user::findByMemberTypeAndPhoneNo($this->memebershipType, $this->phoneNo);
        if ($user) {
            return $_user;
        }
        return false;
    }
}
