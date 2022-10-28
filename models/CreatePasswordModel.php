<?php

namespace app\models;

use app\models\Vuser;
use app\models\User;
use yii\base\InvalidArgumentException;
use yii\base\Model;
use Yii;
use Exception;
use yii\helpers\VarDumper;

class CreatePasswordModel extends Model
{
    public $password;
    public $password_repeat;
    public $_user;


    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['password', 'password_repeat'], 'required'],
            ['password', 'string',],
            [
                'password_repeat', 'compare', 'compareAttribute' => 'password',
                'message' => "Passwords don't match",
            ],
        ];
    }

    /**
     * Signs user up.
     *
     * @return bool whether the creating new account was successful and email was sent
     */
    public function SavePassword($memberNo)
    {

        $user = User::findOne([
            'No_' => $memberNo,
        ]);

        if (!$user) {
            return false;
        }
        $user->setPassword($this->password);
        $user->SetUpPassword = 1;
        // $user->generateAuthKey();
        // $user->generateEmailVerificationToken();

        if ($user->update()) {
            return $user;
        }
    }

    public function getUser($memberNo)
    {
        // exit('hapa');
        $this->_user = User::findIdentity($memberNo);
        if ($this->_user) {
            return $this->_user;
        }
        return false;
    }
}
