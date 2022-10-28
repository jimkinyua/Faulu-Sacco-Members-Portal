<?php

use yii\db\Migration;

/**
 * Class m220830_132610_add_column_action_performed_on_login_attampts_table
 */
class m220830_132610_add_column_action_performed_on_login_attampts_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%portal_login_attempts}}', 'action_performed', $this->string(300));
    }
}
