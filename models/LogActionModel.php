<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;

class LogActionModel extends ActiveRecord
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
        return  '{{%member_actions}}';
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
            ['time_accessed', 'default', 'value' => time()],
        ];
    }

    public function Log($data, $action = '')
    {
        // echo '<pre>';
        // print_r($model);
        // exit;
        $this->member_id = $data['IDnumber'];
        $this->Ip = Yii::$app->getRequest()->getUserIP();
        $this->action_perfomed = $action;
        if ($this->save()) {
            return $this;
        }
    }
}
