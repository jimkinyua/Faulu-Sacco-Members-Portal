<?php

use yii\db\Migration;

/**
 * Class m220830_094214_append_updated_at_column_on_login_attampts_table
 */
class m220830_094214_append_updated_at_column_on_login_attampts_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
    }

    public function up()
    {
        // $this->addColumn('{{%portal_login_attempts}}', 'updated_at', $this->timestamp()->notNull());
    }

    public function down()
    {
        $this->dropColumn('{{%portal_login_attempts}}', 'updated_at');
    }
}
