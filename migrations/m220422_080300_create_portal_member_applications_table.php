<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%PortalMemberApplicationsAuth}}`.
 */
class m220422_080300_create_portal_member_applications_table extends Migration
{
    public function safeUp()
    {
        // $this->createTable('PortalMemberApplicationsAuth ', [
            // 'id' => $this->primaryKey(),
            // 'username' => $this->string(),
            // 'auth_key' => $this->string(32)->notNull(),
            // 'phoneNo' => $this->string(32)->notNull(),
            // 'membershipType' => $this->string(32)->notNull(),
            // 'memebershipType' => $this->string(32)->notNull(),
            // 'firstName' => $this->string(250)->notNull(),
            // 'password_hash' => $this->string()->notNull(),
            // 'password_reset_token' => $this->string(),
            // 'email' => $this->string()->notNull()->unique(),
            // 'status' => $this->smallInteger()->notNull()->defaultValue(10),
            // 'created_at' => $this->integer()->notNull(),
            // 'updated_at' => $this->integer()->notNull(),
            // 'verification_token' => $this->string()->defaultValue(null),
            // 'ApplicationId'=>$this->string()->defaultValue(null),

        // ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('PortalMemberApplicationsAuth ');
    }
}
