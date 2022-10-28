<?php

use yii\db\Migration;

/**
 * Class m220830_125051_alter_attempted_at_column_on_login_attampts_table
 */
class m220830_125051_alter_attempted_at_column_on_login_attampts_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%portal_login_attempts}}', 'attempt_time', $this->integer());
    }
}
