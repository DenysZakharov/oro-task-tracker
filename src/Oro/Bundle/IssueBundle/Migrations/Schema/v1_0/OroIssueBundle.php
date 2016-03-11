<?php

namespace Oro\Bundle\IssueBundle\Migrations\Schema\v1_0;

use Doctrine\DBAL\Schema\Schema;

use Oro\Bundle\EntityExtendBundle\Migration\OroOptions;
use Oro\Bundle\MigrationBundle\Migration\Migration;
use Oro\Bundle\MigrationBundle\Migration\QueryBag;

class OroIssueBundle implements Migration
{
    /**
     * {@inheritdoc}
     */
    public function up(Schema $schema, QueryBag $queries)
    {
        $options = new OroOptions();
        $table = $schema->createTable('tracker_issue');
        $table->addColumn('id', 'integer', ['autoincrement' => true]);
        $table->addColumn('organization_id', 'integer', ['notnull' => false]);
        $table->addColumn('workflow_item_id', 'integer', ['notnull' => false]);
        $table->addColumn('resolution_id', 'integer', ['notnull' => false]);
        $table->addColumn('priority_id', 'integer', ['notnull' => false]);
        $table->addColumn('assignee_id', 'integer', ['notnull' => false]);
        $table->addColumn('workflow_step_id', 'integer', ['notnull' => false]);
        $table->addColumn('parent_id', 'integer', ['notnull' => false]);
        $table->addColumn('owner_id', 'integer', ['notnull' => false]);
        $table->addColumn('reporter_id', 'integer', ['notnull' => false]);
        $table->addColumn('code', 'string', ['length' => 255]);
        $table->addColumn('summary', 'string', ['length' => 255]);
        $table->addColumn('type', 'string', ['length' => 255]);
        $table->addColumn('description', 'text', ['notnull' => false]);
        $table->addColumn('created', 'datetime', []);
        $table->addColumn('updated', 'datetime', []);
        $table->setPrimaryKey(['id']);
        $table->addUniqueIndex(['code'], 'UNIQ_46A8C3E677153098');
        $table->addUniqueIndex(['workflow_item_id'], 'UNIQ_46A8C3E61023C4EE');
        $table->addIndex(['priority_id'], 'IDX_46A8C3E6497B19F9', []);
        $table->addIndex(['resolution_id'], 'IDX_46A8C3E612A1C43A', []);
        $table->addIndex(['workflow_step_id'], 'IDX_46A8C3E671FE882C', []);
        $table->addIndex(['reporter_id'], 'IDX_46A8C3E6E1CFE6F5', []);
        $table->addIndex(['assignee_id'], 'IDX_46A8C3E659EC7D60', []);
        $table->addIndex(['parent_id'], 'IDX_46A8C3E6727ACA70', []);
        $table->addIndex(['owner_id'], 'IDX_46A8C3E67E3C61F9', []);
        $table->addIndex(['organization_id'], 'IDX_46A8C3E632C8A3DE', []);
        // Hide tags column from grid by default.
        $options->set('tag', 'enableGridColumn', false);

        // Hide tags filter from grid by default.
        $options->set('tag', 'enableGridFilter', false);

        $table->addOption(OroOptions::KEY, $options);
    }
}