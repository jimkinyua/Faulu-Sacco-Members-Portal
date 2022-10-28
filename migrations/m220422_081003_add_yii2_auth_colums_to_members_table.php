<?php

use yii\db\Migration;

/**
 * Class m220422_081003_add_yii2_auth_colums_to_members_table
 */
class m220422_081003_add_yii2_auth_colums_to_members_table extends Migration
{


    public function safeUp()
    {
        $this->addColumn(Yii::$app->params['DbCompanyName'] . 'Members ', 'auth_key', $this->string(200));
        $this->addColumn(Yii::$app->params['DbCompanyName'] . 'Members ', 'phoneNo', $this->string(20));
        $this->addColumn(Yii::$app->params['DbCompanyName'] . 'Members ', 'password_hash', $this->string(200));
        $this->addColumn(Yii::$app->params['DbCompanyName'] . 'Members ', 'password_reset_token', $this->string(200));
        $this->addColumn(Yii::$app->params['DbCompanyName'] . 'Members ', 'verification_token', $this->string()->defaultValue(null));
        $this->addColumn(Yii::$app->params['DbCompanyName'] . 'Members ', 'status',  $this->smallInteger()->notNull()->defaultValue(10));
        $this->addColumn(Yii::$app->params['DbCompanyName'] . 'Members ', 'Transaction OTP',  $this->integer());
        $this->addColumn(Yii::$app->params['DbCompanyName'] . 'Members ', 'SetUpPassword',  $this->boolean()->defaultValue(10));
        $this->addColumn(Yii::$app->params['DbCompanyName'] . 'Members ', 'created_at',  $this->integer());
        $this->addColumn(Yii::$app->params['DbCompanyName'] . 'Members ', 'updated_at',  $this->integer());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn(Yii::$app->params['DbCompanyName'] . 'Members ', 'auth_key');
        $this->dropColumn(Yii::$app->params['DbCompanyName'] . 'Members ', 'phoneNo');
        $this->dropColumn(Yii::$app->params['DbCompanyName'] . 'Members ', 'password_hash');
        $this->dropColumn(Yii::$app->params['DbCompanyName'] . 'Members ', 'password_reset_token');
        $this->dropColumn(Yii::$app->params['DbCompanyName'] . 'Members ', 'verification_token');
        $this->dropColumn(Yii::$app->params['DbCompanyName'] . 'Members ', 'status');
        $this->dropColumn(Yii::$app->params['DbCompanyName'] . 'Members ', 'Transaction OTP');
        $this->dropColumn(Yii::$app->params['DbCompanyName'] . 'Members ', 'SetUpPassword');
        $this->dropColumn(Yii::$app->params['DbCompanyName'] . 'Members ', 'created_at');
        $this->dropColumn(Yii::$app->params['DbCompanyName'] . 'Members ', 'updated_at');
    }
}
