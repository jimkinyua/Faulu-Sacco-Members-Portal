<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%logged_in_members}}`.
 */
class m220831_114201_create_logged_in_members_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%logged_in_members}}', [
            'id' => $this->primaryKey(),
            'time_logged_in' => $this->integer(),
            'member_id' => $this->string(),
            'method_used' => $this->string(150),
            'Ip' => $this->string(150),
            'log_out_time' => $this->integer(),
            'logged_in' => $this->integer(),
            'browser' => $this->string(150),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%logged_in_members}}');
    }
}
