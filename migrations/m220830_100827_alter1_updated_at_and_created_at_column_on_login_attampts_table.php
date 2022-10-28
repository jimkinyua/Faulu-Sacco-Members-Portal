<?php

use yii\db\Migration;

/**
 * Class m220830_100827_alter1_updated_at_and_created_at_column_on_login_attampts_table
 */
class m220830_100827_alter1_updated_at_and_created_at_column_on_login_attampts_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%portal_login_attempts}}', 'updated_at', $this->integer());
        // $this->addColumn('{{%portal_login_attempts}}', 'created_at', $this->integer());
    }
}
