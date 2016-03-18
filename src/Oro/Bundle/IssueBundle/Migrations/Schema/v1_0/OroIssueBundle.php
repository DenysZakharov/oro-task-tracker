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
    public function getMigrationVersion()
    {
        return 'v1_0';
    }

    /**
     * {@inheritdoc}
     */
    public function up(Schema $schema, QueryBag $queries)
    {
        self::addIssueTable($schema);
    }
    /**
     * {@inheritdoc}
     */
    public static function addIssueTable(Schema $schema)
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
        $table->addColumn('createdAt', 'datetime', []);
        $table->addColumn('updatedAt', 'datetime', []);
        $table->addUniqueIndex(['code'], 'UNIQ_6463563565656398');
        $table->addUniqueIndex(['workflow_item_id'], 'UNIQ_6546H7655K576');
        $table->setPrimaryKey(['id']);
        // Hide tags column from grid by default.
        $options->set('tag', 'enableGridColumn', false);
        // Hide tags filter from grid by default.
        $options->set('tag', 'enableGridFilter', false);
        $table->addOption(OroOptions::KEY, $options);
        $table = $schema->createTable('issue_collaborator');
        $table->addColumn('issue_id', 'integer', []);
        $table->addColumn('user_id', 'integer', []);
        $table->setPrimaryKey(['issue_id', 'user_id']);
        $table->addIndex(['issue_id'], 'IDX_F7987FS797F7979F', []);
        $table->addIndex(['user_id'], 'IDX_F88908G98D08D980', []);
        $table = $schema->createTable('issue_priority');
        $table->addColumn('id', 'integer', ['autoincrement' => true]);
        $table->addColumn('code', 'string', ['length' => 255]);
        $table->addColumn('label', 'string', ['length' => 255]);
        $table->addColumn('priority', 'integer', ['default' => '1']);
        $table->setPrimaryKey(['id']);
        $table = $schema->createTable('issue_resolution');
        $table->addColumn('id', 'integer', ['autoincrement' => true]);
        $table->addColumn('code', 'string', ['length' => 255]);
        $table->addColumn('label', 'string', ['length' => 255]);
        $table->addColumn('priority', 'integer', ['default' => '1']);
        $table->setPrimaryKey(['id']);
        $table = $schema->createTable('issue_related');
        $table->addColumn('issue_source', 'integer', []);
        $table->addColumn('issue_target', 'integer', []);
        $table->setPrimaryKey(['issue_source', 'issue_target']);
        $table->addIndex(['issue_source'], 'IDX_7C19CEE5AD6AF443', []);
        $table->addIndex(['issue_target'], 'IDX_8C19CEE7B49FA5DF', []);
    }
}