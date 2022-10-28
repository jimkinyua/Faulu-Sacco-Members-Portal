<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%member_application}}`.
 */
class m220524_054620_add_id_column_to_member_application_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        // $this->addColumn('PortalMemberApplicationsAuth ', 'NationalID', $this->string(200)->defaultValue(null));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // $this->dropColumn('PortalMemberApplicationsAuth ', 'NationalID');
    }
}
