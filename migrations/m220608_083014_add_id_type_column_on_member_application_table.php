<?php

use yii\db\Migration;

/**
 * Class m220608_083014_add_id_type_column_on_member_application_table
 */
class m220608_083014_add_id_type_column_on_member_application_table extends Migration
{
    public function safeUp()
    {
        // $this->addColumn('PortalMemberApplicationsAuth ', 'IdentificationType', $this->string(200)->defaultValue(null));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // $this->dropColumn('PortalMemberApplicationsAuth ', 'IdentificationType');
    }
}
