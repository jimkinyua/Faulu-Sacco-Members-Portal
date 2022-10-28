<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%portal_login_attempts}}`.
 */
class m220830_071042_create_portal_login_attempts_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        // $this->createTable('{{%portal_login_attempts}}', [
        //     'id' => $this->primaryKey(),
        //     'MemberID' => $this->string(250)->notNull(),
        //     'IP' => $this->string(250)->notNull(),
        //     'attempted_at' => $this->timestamp()->notNull(),
        //     'created_at' => $this->timestamp()->notNull(),
        // ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // $this->dropTable('{{%portal_login_attempts}}');
    }
}
