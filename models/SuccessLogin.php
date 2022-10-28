<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;

class SuccessLogin extends ActiveRecord
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
        return  '{{%logged_in_members}}';
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            // TimestampBehavior::className(),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            ['logged_in', 'default', 'value' => 1],
        ];
    }

    public function Log($model, $action = '')
    {
        $browser = 'Unable to Fetch Browser'; //get_browser(null, true);
        // echo '<pre>';
        // print_r($model);
        // exit;
        $this->member_id = $model->IDnumber;
        $this->Ip = Yii::$app->getRequest()->getUserIP();
        $this->time_logged_in = time();
        $this->method_used = $action;
        $this->log_out_time = time();
        $this->browser = $browser;
        // echo '<pre>';
        // print_r($this);
        // exit;
        if ($this->save()) {
            return $this;
        }
    }
}
