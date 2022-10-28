<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%member_actions}}`.
 */
class m220901_093241_create_member_actions_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%member_actions}}', [
            'id' => $this->primaryKey(),
            'time_accessed' => $this->integer(),
            'member_id' => $this->string(),
            'action_perfomed' => $this->string(150),
            'Ip' => $this->string(150),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%member_actions}}');
    }
}
