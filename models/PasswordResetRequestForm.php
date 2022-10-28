<?php

namespace app\models;
use Yii;
use yii\base\Model;

/**
 * Password reset request form
 */
class PasswordResetRequestForm extends Model{
    public $memberNo;
    public $Id_No;


    /**
     * {@inheritdoc}
     */
    public function rules(){
        return [
            [['Id_No'], 'trim'],
            [['Id_No'], 'required'],
            ['Id_No', 'exist',
                'targetClass' => '\app\models\User',
                'filter' => ['status' => User::STATUS_ACTIVE],
                'message' => 'There is no user with this ID provided.'
            ],
        ];
    }

    /**
     * Sends an email with a link, for resetting the password.
     *
     * @return bool whether the email was send
     */
    public function FindMember()
    {
        /* @var $user User */
        $user = User::findByUsername($this->Id_No);
       
        if (!$user) {
            return false;
        }
        return $user;
        

    }
}