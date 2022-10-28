<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%member_application}}`.
 */
class m220622_123949_add_yii2_auth_columns_to_member_application_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn(Yii::$app->params['DbCompanyName'] . 'Member Application ', 'auth_key', $this->string(20));
        $this->addColumn(Yii::$app->params['DbCompanyName'] . 'Member Application ', 'password_hash', $this->string(200));
        $this->addColumn(Yii::$app->params['DbCompanyName'] . 'Member Application ', 'password_reset_token', $this->string(200));
        $this->addColumn(Yii::$app->params['DbCompanyName'] . 'Member Application ', 'verification_token', $this->string()->defaultValue(null));
        $this->addColumn(Yii::$app->params['DbCompanyName'] . 'Member Application ', 'status',  $this->smallInteger()->notNull()->defaultValue(10));
        $this->addColumn(Yii::$app->params['DbCompanyName'] . 'Member Application ', 'created_at',  $this->integer());
        $this->addColumn(Yii::$app->params['DbCompanyName'] . 'Member Application ', 'updated_at',  $this->integer());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn(Yii::$app->params['DbCompanyName'] . 'Member Application ', 'auth_key');
        $this->dropColumn(Yii::$app->params['DbCompanyName'] . 'Member Application ', 'password_hash');
        $this->dropColumn(Yii::$app->params['DbCompanyName'] . 'Member Application ', 'password_reset_token');
        $this->dropColumn(Yii::$app->params['DbCompanyName'] . 'Member Application ', 'verification_token');
        $this->dropColumn(Yii::$app->params['DbCompanyName'] . 'Member Application ', 'status');
        $this->dropColumn(Yii::$app->params['DbCompanyName'] . 'Member Application ', 'created_at');
        $this->dropColumn(Yii::$app->params['DbCompanyName'] . 'Member Application ', 'updated_at');
    }
}
