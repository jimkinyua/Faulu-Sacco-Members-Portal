<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;

class LoginAttempt extends ActiveRecord
{

    // public $MemberID;
    // public $attempted_at;
    // public $created_at;
    // public $IP;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return  '{{%portal_login_attempts}}';
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
            ['attempted_at', 'default', 'value' => date('Y-m-d H:i:s')],
        ];
    }

    public function LogSignInAttempt($model, $action = '')
    {
        // echo '<pre>';
        // print_r($model);
        // exit;
        $this->MemberID = $model->IDnumber;
        $this->IP = Yii::$app->getRequest()->getUserIP();
        $this->attempt_time = time();
        $this->action_performed = $action;
        // $this->created_at = time();
        // echo '<pre>';
        // print_r($this);
        // exit;
        if ($this->save()) {
            return true;
        }
    }
}
