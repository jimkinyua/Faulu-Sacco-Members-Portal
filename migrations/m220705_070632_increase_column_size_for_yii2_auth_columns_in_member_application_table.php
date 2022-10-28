<?php

use yii\db\Migration;

/**
 * Class m220705_070632_increase_column_size_for_yii2_auth_columns_in_member_application_table
 */
class m220705_070632_increase_column_size_for_yii2_auth_columns_in_member_application_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
    }

    public function up()
    {
        $this->alterColumn(Yii::$app->params['DbCompanyName'] . 'Members ', 'auth_key', $this->string(255));
        $this->alterColumn(Yii::$app->params['DbCompanyName'] . 'Members ', 'password_hash', $this->string(255));
    }

    public function down()
    {
        $this->alterColumn(Yii::$app->params['DbCompanyName'] . 'Members ', 'auth_key', $this->string(200));
        $this->alterColumn(Yii::$app->params['DbCompanyName'] . 'Members ', 'password_hash', $this->string(200));
    }
}
